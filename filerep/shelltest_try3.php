<?php
set_time_limit(0);
ini_set('max_input_time', 99999);


//echo shell_exec('whoami');

//$fulldir = '/media/usbdrive/Music/0/2 Many Dj\'s';
//$fulldir = '/var/www';
$fulldir = '/home/pi/filereptest';
//$fulldir = '/media/usbdrive/Mijn Documenten';


function timezone_offset_string()
{
	$offset = timezone_offset_get( new DateTimeZone( 'Europe/Brussels' ), new DateTime() );
	return sprintf( "%s%02d:%02d", ( $offset >= 0 ) ? '+' : '-', abs( $offset / 3600 ), abs( $offset % 3600 ) );
}


/*
	add script to /etc/sudoers
		www-data ALL=(ALL:ALL) NOPASSWD: /var/www/filerep/getfileindexrecur.sh

	then: sudo /etc/init.d/sudo restart

*/

// sudo ls -AGgR --full-time "/media/usbdrive/Mijn Documenten" > /media/usbdrive/_filerep_temp/output.txt 2>/media/usbdrive/_filerep_temp/error.txt &

//$str = shell_exec('sudo /var/www/filerep/getfileindexrecur.sh "/media/usbdrive/_filerep_temp/output.txt" "/media/usbdrive/_filerep_temp/error.txt" "' . escapeshellcmd($fulldir) . '"');

/*

/home/pi/filereptest:
total 5996
-rw-r--r-- 1 6000054 2014-05-26 16:08:48.000000000 +0200 bigimage.bmp
drwxr-xr-x 2    4096 2014-06-11 12:14:52.261265877 +0200 map1
drwxr-xr-x 2    4096 2014-06-11 12:15:13.450628815 +0200 submap
-rw-r--r-- 1      68 2014-06-03 14:18:40.176174752 +0200 test.txt
drwxr-xr-x 2  126976 2014-09-12 13:43:17.858727495 +0200 veel

/home/pi/filereptest/map1:
total 24
-rw-r--r-- 1 21078 2014-04-03 12:18:33.000000000 +0200 test.bmp

/home/pi/filereptest/submap:
total 4
-rw-r--r-- 1 1 2014-05-14 09:34:36.000000000 +0200 ik doe wel mee.bmp

*/


$handle = fopen("/media/usbdrive/_filerep_temp/output2.txt", "r");

$currentdir = '';
$success = false;
$data = [];

if($handle){
	echo "Begin <br>\n";
    while (($filestr = fgets($handle)) !== false) {
	
        //if($output == 1) echo "\tLine: ' . $filestr . '<br>\n";
		
		if($filestr != ''){
			
			if(substr($filestr, 0, 1) == '/'){
				$currentdir = substr($filestr, 0, -2) . '/';
			}
			else if(substr($filestr, 0, 6) == 'total '){
				// ignore
			}
			else {
				
				$dir = substr($filestr, 0, 1);
				
				$size = -1;
				$modifiedstr = '';
				$file = '';
				
				$filestrarr = explode(' ', $filestr);
				$fin = -1;
				$fcount = count($filestrarr);
				for ($fi = 0; $fi < $fcount; $fi++) {
					if($filestrarr[$fi] != ''){
						$fin++;
						
						switch($fin){
							case 0: //drwxrwxrwx
							case 1: //1
								break;
								
							case 2: //<size>
								$size = $filestrarr[$fi];
								break;
								
							case 3: //<modified - date>
								$modifiedstr = $filestrarr[$fi];
								break;
							case 4: //<modified - hour>
							case 5: //<modified - tz>
								$modifiedstr .= ' ' . $filestrarr[$fi];
								break;
								
							// everything greater than 5 - all at the end
							default: //<filename>
								
								//break;
						}
					}
					if($fin > 6){
						$file .= ' ';
					}
					if($fin > 5){
						$file .= $filestrarr[$fi];
						$fin++;
					}
				}
				
				//$file = substr($filestr, $filestart);
				
				$fullfile = $currentdir . $file;
				
				// we got directories
				if($file == '.' || $file == '..'){
					$success = true;
				}
				
				if($file != '.' && $file != '..'){
					$modified_ok = 0;
					
					if($dir == 'd'){
						$size = -1;
						$modified = 0;
						$modified_cest = 0;
						$dir = 1;
					}
					else {
						//$size = substr($filestr, $sizestart, $sizelen);
						//$modified = substr($filestr, $datestart, $datelen);
						$modified = $modifiedstr;
						$modified = str_replace('-', '/', $modified);
						$modified = explode('.', $modified)[0];
						$modified_cest = $modified;
						
						$modified = strtotime($modified);
						
						if($modified === false){
							$modified = 0;
							$modified_cest = 0;
						}
						else {
							$modified_cest = strtotime($modified_cest . ' ' . timezone_offset_string());
							$modified_ok = 1;
						}
						
						$dir = 0;
					}
					
					//if(/*$dir == 1 ||*/ ( $dir == 0 && $modified >= $modified_since) ){
					//if( $dir == 0 && $modified_ok == 0 || ($modified_ok == 1 && $modified >= $modified_since) ){
					if($dir == 0){
						$data[] = array(
							'name' => $file,
							'nativepath' => $fullfile,
							'size' => $size,
							'modified' => $modified,
							'modified_cest' => $modified_cest,
							'modifiedstr' => $modifiedstr,
							//'modifiedstr2' => explode('.', $modifiedstr)[0],
							'dir' => $dir
							//'checksum' => ($include_checksum == 1 ? hash_file('md5', $fullfile) : '')
						);
					}
					
				}
				
			}
			
		}
    }
	fclose($handle);
	echo "End <br>\n";
}
else {
	echo "Could not open file<br>\n";
}




$count = count($data);
for ($i = 0; $i < $count; $i++) {
	echo 'name=';
	echo $data[$i]['name'];
	echo "<br>\n";
	echo 'nativepath=';
	echo $data[$i]['nativepath'];
	echo "<br>\n";
	echo 'size=';
	echo $data[$i]['size'];
	echo "<br>\n";
	echo 'modified=';
	echo $data[$i]['modified'];
	echo "<br>\n";
	//echo 'modified2=';
	//echo $data[$i]['modified2'];
	//echo "<br>\n";
	//echo 'modified3=';
	//echo $data[$i]['modified3'];
	//echo "<br>\n";
	echo 'modifiedstr=';
	echo $data[$i]['modifiedstr'];
	echo "<br>\n";
	//echo 'modifiedstr2=';
	//echo $data[$i]['modifiedstr2'];
	//echo "<br>\n";
	echo "<br>\n";
	//echo '<br><br>';
	
}


?>