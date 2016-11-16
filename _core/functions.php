<?php

function timezone_offset_string()
{
	$offset = timezone_offset_get( new DateTimeZone( 'Europe/Brussels' ), new DateTime() );
	return sprintf( "%s%02d:%02d", ( $offset >= 0 ) ? '+' : '-', abs( $offset / 3600 ), abs( $offset % 3600 ) );
}

function goto_action($action, $secure = false, $params = ''){
	
	$httpprefix = 'http://';
	if($secure){
		$httpprefix = 'https://';
	}
	
	$redirect_url = $httpprefix . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?action=' . $action;
	
	if($params != ''){
		if(strpos($redirect_url, '?') > 0){
			$redirect_url = $redirect_url . '&' . $params;
		}
		else {
			$redirect_url = $redirect_url . '?' . $params;
		}
	}
	
	header('Location: ' . $redirect_url);
	
}

function saneInput($name, $type = 'string', $default = '', $caseSensitive = false){
	$value = $default;
	if($caseSensitive){
		$post = $_POST;
		$get = $_GET;
	}
	else {
		$post = array_change_key_case($_POST, CASE_LOWER);
		$get = array_change_key_case($_GET, CASE_LOWER);
		$name = strtolower($name);
	}
	if(isset($post[$name])){
		switch($type){
			case 'string':
				$value = $post[$name];
				break;
			case 'int':
			case 'integer':
			case 'unixtime':
				if($post[$name] != '' && is_numeric($post[$name]) ){
					$value = (int)$post[$name];
				}
				break;
			case 'intlist':
			case 'integerlist':
				if($post[$name] != ''){
					$value = $post[$name];
					$test = explode(',', $post[$name]);
					for($i=0; $i<count($test); $i++){
						if(!is_numeric($test[$i])){
							$value = $default;
						}
					}
				}
				break;
			case 'bool':
			case 'boolean':
			case 'bit':
				$value = false;
				if($post[$name] == '1' || $post[$name] == 'true'){
					$value = true;
				}
				break;
				break;
		}
	}
	else if(isset($get[$name])){
		switch($type){
			case 'string':
				$value = $get[$name];
				break;
			case 'int':
			case 'integer':
			case 'unixtime':
				if($get[$name] != '' && is_numeric($get[$name]) ){
					$value = (int)$get[$name];
				}
				break;
			case 'intlist':
			case 'integerlist':
				if($get[$name] != ''){
					$value = $get[$name];
					$test = explode(',', $get[$name]);
					for($i=0; $i<count($test); $i++){
						if(!is_numeric($test[$i])){
							$value = $default;
						}
					}
				}
				break;
			case 'bool':
			case 'boolean':
			case 'bit':
				$value = false;
				if($get[$name] == '1' || $get[$name] == 'true'){
					$value = true;
				}
				break;
		}
	}
	return $value;
}

function list_dir(&$data, $fulldir, $modified_since = 0, $include_subdirs = 0, $list_subdirs = 1){
	
	if(substr($fulldir, -1, 1) != '/'){
		$fulldir = $fulldir . '/';
	}
	
	$handle = opendir($fulldir);
	
	while (($file = readdir($handle)) == true){
		$fullfile = $fulldir . $file;
		if($file != '.' && $file != '..'){
			if(is_dir($fullfile)){
				$size = -1;
				$modified = '';
				$dir = 1;
			}
			else {
				$size = filesize($fullfile);
				$modified = filemtime($fullfile);
				$dir = 0;
			}
			
			if(/*$dir == 1 ||*/ ( $dir == 0 && $modified >= $modified_since)
                ||
                ($dir == 1 && $include_subdirs == 1)){
				$data[] = array(
					'name' => $file,
					'nativepath' => $fullfile,
					'size' => $size,
					'modified' => $modified,
					'dir' => $dir,
					'checksum' => '' //hash_file('md5', $fullfile)
				);
			}
			
			if($dir == 1 && $list_subdirs == 1){
				list_dir($data, $fulldir . $file . '/', $modified_since);
			}
		}
	}
	
    closedir( $handle ); 
	
}


function mysql2json($mysql_result){
	$rows = mysqli_num_rows($mysql_result);
	$data = [];
	for($x=0;$x<$rows;$x++){
		$row = mysqli_fetch_assoc($mysql_result);
		$data[] = $row;
	}
	return($data);
}


/**
 * read file - my way
 */
function readfile_advanced($filename, $chunksize_mb = 1, $bitrate_kb = 0, $retbytes = false) {
	$chunksize = $chunksize_mb * (1024 * 1024); // how many MB per chunk
	$bitrate = round($bitrate_kb * 1024);	// or, how many KB per second 
	$buffer = '';
	$cnt = 0;
	// $handle = fopen($filename, 'rb');
	$handle = fopen($filename, 'rb');
	if ($handle === false) {
		return false;
	}
	while (!feof($handle)) {
		if($bitrate > 0){
			$buffer = fread($handle, $bitrate);
		}
		else {
			$buffer = fread($handle, $chunksize);
		}
		echo $buffer;
		//ob_flush();
		flush();
		
		if($bitrate > 0){
			sleep(1);
		}
		
		if ($retbytes) {
			$cnt += strlen($buffer);
		}
	}
	$status = fclose($handle);
	if ($retbytes && $status) {
		return $cnt; // return num. bytes delivered like readfile() does.
	}
	return $status;

}

/**
 * read file in parts of 10Mb
 * /
function readfile_chunked($filename,$retbytes=true) {
	$chunksize = 1*(1024*1024); // how many bytes per chunk
	$buffer = '';
	$cnt =0;
	// $handle = fopen($filename, 'rb');
	$handle = fopen($filename, 'rb');
	if ($handle === false) {
		return false;
	}
	while (!feof($handle)) {
		$buffer = fread($handle, $chunksize);
		echo $buffer;
		ob_flush();
		flush();
		if ($retbytes) {
			$cnt += strlen($buffer);
		}
	}
	$status = fclose($handle);
	if ($retbytes && $status) {
		return $cnt; // return num. bytes delivered like readfile() does.
	}
	return $status;

}*/

/**
 * download file with httpranges support
 */
function smartReadFile($location, $filename, $mimeType='application/octet-stream')
{ if(!file_exists($location))
  { header ("HTTP/1.0 404 Not Found");
    return;
  }
 
  $size=filesize($location);
  $time=date('r',filemtime($location));
 
  $fm=@fopen($location,'rb');
  if(!$fm)
  { header ("HTTP/1.0 505 Internal server error");
    return;
  }
 
  $begin=0;
  $end=$size;
 
  if(isset($_SERVER['HTTP_RANGE']))
  { if(preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches))
    { $begin=intval($matches[0]);
      if(!empty($matches[1]))
        $end=intval($matches[1]);
    }
  }
 
  if($begin>0||$end<$size)
    header('HTTP/1.0 206 Partial Content');
  else
    header('HTTP/1.0 200 OK'); 
 
  header("Content-Type: $mimeType");
  header('Cache-Control: public, must-revalidate, max-age=0');
  header('Pragma: no-cache'); 
  header('Accept-Ranges: bytes');
  header('Content-Length:'.($end-$begin));
  header("Content-Range: bytes $begin-$end/$size");
  header("Content-Disposition: inline; filename=$filename");
  header("Content-Transfer-Encoding: binary\n");
  header("Last-Modified: $time");
  header('Connection: close'); 
 
  $cur=$begin;
  fseek($fm,$begin,0);

  while(!feof($fm)&&$cur<$end&&(connection_status()==0))
  { print fread($fm,min(1024*16,$end-$cur));
    $cur+=1024*16;
  }
}

/**
 * limit download rate of a file 
 * /
function download_limit($local_file){
	// set the download rate limit (=> 20,5 kb/s)
	$download_rate = 20.5;
	if(file_exists($local_file) && is_file($local_file))
	{
		header('Cache-control: private');
		header('Content-Type: application/octet-stream');
		header('Content-Length: '.filesize($local_file));
		header('Content-Disposition: filename='.$local_file);

		flush();
		$file = fopen($local_file, "r");
		while(!feof($file))
		{
			// send the current file part to the browser
			print fread($file, round($download_rate * 1024));
			// flush the content to the browser
			flush();
			// sleep one second
			sleep(1);
		}
		fclose($file);}
	else {
		die('Error: The file '.$local_file.' does not exist!');
	}
}*/

function formatFileSize($sizeInBytes, $rounding = 2){
	$index = 0;
    $sizes = array('b', 'kB', 'MB', 'GB', 'TB');
	while($sizeInBytes >= 1024){
		$sizeInBytes /= 1024;
		$index++;
	}
	return round($sizeInBytes, $rounding) . ' ' . $sizes[$index];
}

function revertFileSize($value){
	$newvalue = 1;
	$value = strtolower($value);
	
	$value = str_replace('b', '', $value);
	
	if(strpos($value, 't') !== false){
		$newvalue = 1024 * 1024 * 1024 * 1024;
		$value = str_replace('t', '', $value);
	}
	else if(strpos($value, 'g') !== false){
		$newvalue = 1024 * 1024 * 1024;
		$value = str_replace('g', '', $value);
	}
	else if(strpos($value, 'm') !== false){
		$newvalue = 1024 * 1024;
		$value = str_replace('m', '', $value);
	}
	else if(strpos($value, 'k') !== false){
		$newvalue = 1024;
		$value = str_replace('k', '', $value);
	}
	
	$value *= $newvalue;
	
	return $value;
}


function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    foreach ($arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }

    array_multisort($sort_col, $dir, $arr);
}


function countProcesses($scriptName)
{
	// ps aux | grep "[u]pdate.php" | wc -l
	$first = substr($scriptName, 0, 1);
	$rest = substr($scriptName, 1);
	
	$name = '"['.$first.']'.$rest.'"';
	$name = $scriptName;
	$cmd = "ps aux | grep $name | grep -v grep | wc -l";
	
	$result = exec($cmd);
	
	return $result;
} 


function secondsToTimeRange($value)
{
	$intervals = array(
		array('label' => 'sec',   'value' => 1.0),
		array('label' => 'min',   'value' => 60.0),
		array('label' => 'hour',  'value' => 60.0 * 60.0),
		array('label' => 'day',   'value' => 24.0 * 60.0 * 60.0),
		//array('label' => 'week',  'value' => 7.0 * 24.0 * 60.0 * 60.0),
		array('label' => 'month', 'value' => 30.0 * 24.0 * 60.0 * 60.0),
		array('label' => 'year',  'value' => 12.0 * 30.0 * 24.0 * 60.0 * 60.0)
	);
	$ret = '';
	
	for($i = count($intervals)-1; $i >= 0; $i--)
	{
		if($value * 1.0 >= $intervals[$i]['value'] ){
			$tmpval = floor(($value * 1.0) / $intervals[$i]['value']);
			$ret .= $tmpval . $intervals[$i]['label'] . ($tmpval == 1 ? '' : 's') . ' ';
			$value = $value * 1.0 % $intervals[$i]['value'];
		}
	}
	
	return $ret;
}

function minutesToTimeRange($value)
{
	return secondsToTimeRange($value * 60);
}


function timeRangeToSeconds($value)
{
	$intervals = array(
		'sec' => 1.0,
		'min' => 60.0,
		'hour' => 60.0 * 60.0,
		'day' => 24.0 * 60.0 * 60.0,
		'week' => 7.0 * 24.0 * 60.0 * 60.0,
		'month' => 30.0 * 24.0 * 60.0 * 60.0,
		'year' => 12.0 * 30.0 * 24.0 * 60.0 * 60.0
	);
	$ret = 0;
	$parts = explode(' ', $value);
	
	for($i = 0; $i < count($parts); $i++)
	{
		if($parts[$i] != ''){
			foreach($intervals as $intervalkey => $intervalvalue){
				if(strpos($parts[$i], $intervalkey . 's') !== false){
					$ret += (str_replace($intervalkey . 's', "", $parts[$i]) * $intervalvalue);
				}
				if(strpos($parts[$i], $intervalkey) !== false){
					$ret += (str_replace($intervalkey, "", $parts[$i]) * $intervalvalue);
				}
			}
			
		}
	}
	
	return $ret;
}

function timeRangeToMinutes($value)
{
	return timeRangeToSeconds($value) / 60;
}


?>