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


function list_dir_shell(&$data, $fulldir, $modified_since = 0, $include_checksum = 0){
	$success = false;
	/*
		add script to /etc/sudoers
			www-data ALL=(ALL:ALL) NOPASSWD: /var/www/filerep/getfileindexrecur.sh

		then: sudo /etc/init.d/sudo restart

	*/
	
	// sudo ls -AGgR --full-time "/media/usbdrive/Mijn Documenten" > /media/usbdrive/_filerep_temp/output.txt 2>/media/usbdrive/_filerep_temp/error.txt &
	
	$str = shell_exec('sudo /var/www/filerep/getfileindexrecur.sh "/media/usbdrive/_filerep_temp/output.txt" "/media/usbdrive/_filerep_temp/error.txt" "' . escapeshellcmd($fulldir) . '"');
	
	if(substr($fulldir, -1, 1) != '/'){
		$fulldir = $fulldir . '/';
	}
	
	
	// no md5 possible?
	/*output:
	
/var/www/users:
total 108
-rw-rw-rw- 1  1097 2014-03-11 05:38:28.000000000 +0100 error.php
drwxrwxrwx 2  4096 2014-06-25 19:02:59.580763910 +0200 includes
-rw-rw-rw- 1  1372 2014-06-11 09:25:16.000000000 +0200 index.php
drwxrwxrwx 2  4096 2014-06-07 15:07:31.906190987 +0200 js
-rw-rw-rw- 1   808 2014-07-01 11:07:22.000000000 +0200 loginform.php
-rw-rw-rw- 1  1496 2014-06-11 10:45:43.000000000 +0200 login.php
-rw-rw-rw- 1 61286 2014-03-11 05:38:28.000000000 +0100 php-secure-login.odt
-rw-rw-rw- 1  1789 2014-06-06 11:59:14.000000000 +0200 protected_page.php
-rw-rw-rw- 1  3023 2014-03-11 05:38:28.000000000 +0100 register.php
-rw-rw-rw- 1  1006 2014-03-11 05:38:28.000000000 +0100 register_success.php
-rw-rw-rw- 1  2004 2014-03-11 05:38:28.000000000 +0100 secure_login.sql
drwxrwxrwx 2  4096 2014-06-07 15:07:31.966189124 +0200 styles
-rw-rw-rw- 1  2954 2014-03-11 05:38:28.000000000 +0100 todo.txt

/var/www/users/includes:
total 36
-rw-rw-rw- 1  947 2014-03-11 05:38:28.000000000 +0100 db_connect.php
-rw-rw-rw- 1 8703 2014-06-12 21:08:24.895554800 +0200 functions.php
-rw-rw-rw- 1 1102 2014-03-11 05:38:28.000000000 +0100 logout.php
-rw-rw-rw- 1 1413 2014-06-11 10:01:26.000000000 +0200 process_login.php
-rw-rw-rw- 1 2080 2014-06-06 10:47:47.000000000 +0200 psl-config.php
-rw-rw-rw- 1 3332 2014-06-06 08:46:15.000000000 +0200 register.inc.php
-rw-rw-rw- 1   71 2014-06-06 11:59:54.000000000 +0200 sec-users.php

/var/www/users/js:
total 20
-rw-rw-rw- 1  3074 2014-03-11 05:38:28.000000000 +0100 forms.js
-rw-rw-rw- 1 15841 2014-03-11 05:38:28.000000000 +0100 sha512.js

/var/www/users/styles:
total 4
-rw-rw-rw- 1 820 2014-07-01 11:05:23.000000000 +0200 main.css


	*/
	/*
	output subdir when not possible:
ls: kan geen toegang krijgen tot /media/usbdrive/index.php/: Is geen map
	*/
	/*
	
	- read lines, ignoring the first one		substr ($string,  $start, $length)
		- is directory? => first char = d		substr ($string, 0, 1)
		- size									substr ($string, 17, 7)
		- date modified							substr ($string, 25, 35)
		- filename								substr ($string, 61)
	
	*/
	
	$filearr = explode("\n", $str);
	$count = count($filearr);
	
	$i_modifier = -1;
	$i_x = -1;
	$i_owner = -1;
	$i_size = -1;
	$i_modified_date = -1;
	$i_modified_hour = -1;
	$i_modified_tz = -1;
	$i_filename = -1;
	
	for ($i = 2; $i < $count; $i++) {
		$filestr = $filearr[$i];
		if($filestr != ''){
			$filepart = substr($filestr, -2);
			// we got directories
			if($filepart == '..'){
				$success = true;
			}
			
			$dir = substr($filestr, 0, 1);
			
			$size = -1;
			$modifiedstr = '';
			$file = '';
			
			// first dir: '..', check filename index
			if($i_filename == -1){
				/*
				$filestrarr = explode(' ', $filestr);
				$fin = -1;
				$fcount = count($filestrarr);
				for ($fi = 0; $fi < $fcount; $fi++) {
					if($filestrarr[$fi] != ''){
						$fin++;
						
						switch($fin){
							case 0: //drwxrwxrwx
								$i_modifier = strpos($filestr, $filestrarr[$fi]);
								break;
							case 1: //1
								$i_x = strpos($filestr, $filestrarr[$fi], $i_modifier + 1);
								break;
							case 2: //root
								$i_owner = strpos($filestr, $filestrarr[$fi], $i_x + 1);
								break;
								
							case 3: //<size>
								$i_size = strpos($filestr, $filestrarr[$fi], $i_owner + 1);
								$size = $filestrarr[$fi];
								break;
								
							case 4: //<modified - date>
								$i_modified_date = strpos($filestr, $filestrarr[$fi], $i_size + 1);
								$modifiedstr = $filestrarr[$fi];
								break;
							case 5: //<modified - hour>
								$i_modified_hour = strpos($filestr, $filestrarr[$fi], $i_modified_date + 1);
								$modifiedstr .= ' ' . $filestrarr[$fi];
								break;
							case 6: //<modified - tz>
								$i_modified_tz = strpos($filestr, $filestrarr[$fi], $i_modified_hour + 1);
								$modifiedstr .= ' ' . $filestrarr[$fi];
								break;
								
							// everything greater than 5 - all at the end
							default: //<filename>
								if($i_filename == -1){
									$i_filename = strpos($filestr, $filestrarr[$fi], $i_modified_tz + 1);
								}
								//break;
						}
					}
					if($fin > 7){
						$file .= ' ';
					}
					if($fin > 6){
						$file .= $filestrarr[$fi];
						$fin++;
					}
				}
				*/
				
				$i_filename = strpos($filestr, '..', $i_modified_tz + 1);
			}
			else {
				/*if($i == 2){
					echo '$i_size='  . $i_size. '<br>';
					echo '$i_modified_date='  . $i_modified_date. '<br>';
					echo '$i_filename='  . $i_filename. '<br>';
					
					echo 'mod=' . substr($filestr, 0, $i_x) . '<br>';
					echo 'x=' . substr($filestr, $i_x, $i_owner - $i_x) . '<br>';
					echo 'owner=' . substr($filestr, $i_owner, $i_size - $i_owner) . '<br>';
				}
				
				$size = substr($filestr, $i_size, $i_modified_date - $i_size);
				$modifiedstr = substr($filestr, $i_modified_date, $i_filename - $i_modified_date);
				$file = substr($filestr, $i_filename);*/
				
				$file = substr($filestr, $i_filename);
				
				$meta = preg_replace('!\s+!', ' ', substr($filestr, 0, $i_filename));
				
				list($access, $x, $owner, $size, $dm1, $dm2, $dm3) = explode(' ', $meta);
				$modifiedstr = $dm1 . ' ' . $dm2 . ' ' . $dm3;
			}
			
			//echo $file . '<br>';
			
			//$file = substr($filestr, $filestart);
			
			$fullfile = $fulldir . $file;
			
			// we got directories
			/*if($file == '.' || $file == '..'){
				$success = true;
			}*/
			
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
				if( $dir == 0 && $modified_ok == 0 || ($modified_ok == 1 && $modified >= $modified_since) ){
				//if($dir == 0){
					$data[] = array(
						'name' => $file,
						'nativepath' => $fullfile,
						'size' => $size,
						'modified' => $modified,
						'modified_cest' => $modified_cest,
						'modifiedstr' => $modifiedstr,
						//'modifiedstr2' => explode('.', $modifiedstr)[0],
						'dir' => $dir,
						'checksum' => ($include_checksum == 1 ? hash_file('md5', $fullfile) : '')
					);
				}
				
				if($dir == 1){// && strpos($fullfile, '/./') === false && strpos($fullfile, '/../') === false){
					$success = $success && list_dir_shell($data, $fullfile, $modified_since);
				}
			}
		}
	}
	
	if(!$success){
		throw new Exception('cannot read dir ' . $fulldir);
	}
	
	return $success;
}


if(substr($fulldir, -1, 1) != '/'){
	$fulldir = $fulldir . '/';
}
echo $fulldir;

$str = shell_exec('sudo /var/www/filerep/getfileindex.sh "' . $fulldir . '"');

echo "<br><br>===str===================<br><br>";

echo $str;

echo "<br><br>======================<br><br>";

	
	$data = [];
	$success = list_dir_shell($data, $fulldir, 0);
	echo 'success:' . $success;
	
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