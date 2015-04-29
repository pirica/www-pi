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
			www-data ALL=(ALL:ALL) NOPASSWD: /var/www/filerep/getfileindex.sh

		then: sudo /etc/init.d/sudo restart

	*/
	
	//date_default_timezone_set('Europe/Brussels');
	
	$str = shell_exec('sudo /var/www/filerep/getfileindex.sh "' . $fulldir . '"');
	
	if(substr($fulldir, -1, 1) != '/'){
		$fulldir = $fulldir . '/';
	}
	
	// no md5 possible?
	/*output:
totaal 360
drwxrwxrwx 1 root   8192 2013-09-01 13:42:05.782574000 +0200 .
drwxr-xr-x 3 root   4096 2013-08-02 16:51:28.706753178 +0200 ..
drwxrwxrwx 1 root   4096 2008-06-11 19:24:49.125131200 +0200 datasheets
drwxrwxrwx 1 root 172032 2008-06-24 19:08:45.223529600 +0200 Downloads
drwxrwxrwx 1 root  12288 2010-02-28 21:19:25.218750000 +0100 drivers
drwxrwxrwx 1 root   4096 2012-04-14 14:17:33.859375000 +0200 extra
drwxrwxrwx 1 root   4096 2008-06-10 17:47:17.311500800 +0200 lacie
drwxrwxrwx 1 root   4096 2008-06-11 19:20:28.089780800 +0200 mail
drwxrwxrwx 1 root  69632 2011-07-20 18:20:17.046875000 +0200 Mijn Documenten
drwxrwxrwx 1 root   4096 2013-08-07 10:35:48.817928000 +0200 Music
drwxrwxrwx 1 root   4096 2013-09-17 13:46:17.859653000 +0200 mysql
drwxrwxrwx 1 root   8192 2008-06-14 09:13:01.993870400 +0200 Nieuwe map
drwxrwxrwx 1 root   4096 2013-09-01 13:46:09.075225000 +0200 pc an
drwxrwxrwx 1 root      0 2008-08-19 14:42:41.233320000 +0200 RECYCLER
drwxrwxrwx 1 root   4096 2010-02-28 21:21:36.156250000 +0100 slipstreamxp
drwxrwxrwx 1 root  20480 2008-08-19 14:36:37.069678400 +0200 station ripper
drwxrwxrwx 1 root   4096 2008-06-18 20:44:45.517352000 +0200 System Volume Information
drwxrwxrwx 1 root   4096 2008-06-10 19:52:51.124592000 +0200 Warez
drwxrwxrwx 1 root  20480 2008-06-10 19:15:15.260819200 +0200 webroot
drwxrwxrwx 1 root  12288 2008-06-11 19:17:18.156670400 +0200 Wim
drwxrwxrwx 1 root      0 2008-06-11 19:27:10.728747200 +0200 Wim.WIMPC

-rwxrwxrwx 1 root  31678 2006-06-18 14:13:38.173112000 +0200 index.php
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
	
	for ($i = 1; $i < $count; $i++) {
		$filestr = $filearr[$i];
		if($filestr != ''){
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
						case 2: //root
							break;
							
						case 3: //<size>
							$size = $filestrarr[$fi];
							break;
							
						case 4: //<modified - date>
							$modifiedstr = $filestrarr[$fi];
							break;
						case 5: //<modified - hour>
						case 6: //<modified - tz>
							$modifiedstr .= ' ' . $filestrarr[$fi];
							break;
							
						// everything greater than 5 - all at the end
						default: //<filename>
							
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
			
			//$file = substr($filestr, $filestart);
			
			$fullfile = $fulldir . $file;
			
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
				
				if($dir == 1 && strpos($fullfile, '/./') === false && strpos($fullfile, '/../') === false){
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