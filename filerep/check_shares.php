<?php

set_time_limit(0);
ini_set('max_input_time', 99999);

include 'connection.php';
include 'act_settings.php';
include 'functions.php';


// check if script is already running - no, continue
//if($setting_fileindex_running == '0' && $setting_directoryindex_running == '0' && $setting_shareindex_running == '0'){
if($setting_directoryindex_running == '0'){
	// mark as running
	mysqli_query($conn, "update t_setting set value = '1' where code = 'directoryindex_running'");


	$qry_shares = mysqli_query($conn, "
		select
			s.id_share,
			s.name,
			s.server_directory
		from t_share s
		where
			s.active = 1
			and s.external = 0
		
		");
		

	$id_share = -1;

	while ($share = mysqli_fetch_array($qry_shares)) {
		$id_share = $share{'id_share'};
		$dir = $share{'server_directory'};
		
		echo "Share " . $share{'server_directory'} . "<br>\n";
		flush();
		
		$str = shell_exec('sudo /var/www/filerep/checkdirectory.sh "' . $dir . '" ' . '-60');
		
/*

/var/www/messages
/var/www/filerep/checkdirectory.sh

*/

		//echo $str;
		
		$str = str_replace("\r", "\n", $str);
		$str = str_replace("\n\n", "\n", $str);
		$str = str_replace("\n\n", "\n", $str);
		$tmpdirs = explode("\n", $str);
		
		$tmpdircount = count($tmpdirs);
		$tmpinsertcount = 0;
		
		echo ($tmpdircount-1) . " changes<br>\n";
		flush();
		
		for ($i = 0; $i < $tmpdircount; $i++) {
			if($tmpdirs[$i] != ''){
				$dirname = $tmpdirs[$i];
				$dirname = str_replace($dir, '', $dirname); // remove main dir
				
/*

/messages
/filerep/checkdirectory.sh

*/
				
				$dbdir = $dirname;
				if(substr($dbdir, -1, 1) != '/'){
					$dbdir = $dbdir . '/';
				}
	
				echo "dir: " . $dbdir . "<br>\n";
				
				// if directory: update
				mysqli_query($conn, "
					update t_directory
					set
						date_last_checked = null
					where
						id_share = " . $id_share . "
						and relative_directory = '" . mysqli_real_escape_string($conn, $dbdir) . "'
					
					");
				
				// apparently not a directory, but a file? : update parent dir
				if(mysqli_affected_rows($conn) == 0){
					// remove filename
					$dirparts = explode('/', $dirname);
					array_pop($dirparts);
					$dirname = implode('/', $dirparts);
					
					$dbdir = $dirname;
					if(substr($dbdir, -1, 1) != '/'){
						$dbdir = $dbdir . '/';
					}
	
					echo "dirfixed: " . $dbdir . "<br>\n";
					
					mysqli_query($conn, "
						update t_directory
						set
							date_last_checked = null
						where
							id_share = " . $id_share . "
							and relative_directory = '" . mysqli_real_escape_string($conn, $dbdir) . "'
						
						");
					
				}
			}
		}
	}


	// script is done, unmark as running
	mysqli_query($conn, "update t_setting set value = '0' where code = 'directoryindex_running'");
	
}


?>