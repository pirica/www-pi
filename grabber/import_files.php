<?php
set_time_limit(3600);
include "connections.php";
include "functions.php";

$fulldir = '/media/usbdrive2/flickr/sets';
$files = [];

echo "Reading dir " . $fulldir . "\n";

$handle = opendir($fulldir);
flush();

while (($file = readdir($handle)) == true){
	$fullfile = $fulldir . '/' . $file;
	if($file != '.' && $file != '..'){
		echo 'file: ' . $file;
		if(!is_dir($fullfile)){
			echo ' => reading';
			//filearr = explode('_', $file);
			$filearr = explode('-', $file);
			
			$files[] = array(
				'name' => $file,
				'nativepath' => $fullfile,
				'set' => $filearr[0]
			);
		}
		
	}
	echo "\n";
}
closedir( $handle ); 
flush();

$insertcount = 0;

$filecount = count($files);
echo $filecount .  " files\n";
for($f = 0; $f < $filecount; $f++){
	
	$values = file_get_contents($files[$f]['nativepath']);
	
	if($values === false){
		echo 'Could not read ' . $files[$f]['nativepath'] . "\n";
	}
	else {
		echo 'Reading ' . $files[$f]['nativepath'] . "\n";
		$lines = explode("\n", $values);
		$linecount = 0;
		$ins = 0;
		echo ' -> ' . count($lines) . " lines in file\n";
		foreach ($lines as $line_num => $line) {
			if($line != '' && stripos($line, '_null_') === false){
				//echo $i.' => ' . $line . "<br>\n";
				$linecount++;
				
				$valarr = explode('/', $line);
				$valpath = $fulldir . '/' . $files[$f]['set'] . '/' . $valarr[count($valarr)-1];
				
				$qry_grabs = mysql_query("
					select
						g.id_grab,
						ifnull(gf.id_grab_file,0) as id_grab_file
					from t_grab g
					left join t_grab_file gf on gf.id_grab = g.id_grab and gf.active = 1 and replace('https://', 'http://', gf.full_url) = '" . mysql_real_escape_string(str_replace('https://', 'http://', $line)) . "'
					where
						g.active = 1
						and g.description like '%flickr%'
						
					", $conn);

				$grabs = mysql_fetch_array($qry_grabs);
				
				if($grabs['id_grab_file'] <= 0){
					mysql_query("
						insert into t_grab_file
						(
							id_grab,
							full_url,
							full_path
						)
						values (
							" . $grabs['id_grab'] . ",
							'" . $line . "',
							'" . $valpath . "'
						)
						", $conn);
					
					$ins++;
					
					// update grab stats
					include 'queries/pr_set_grab_stats.php';
					
					
				}
				
			}
		}
		echo ' -> ' . $linecount . " lines read, " . $ins . " inserted\n";
	}
	flush();

}

?>