<?php

function increase_counter(&$grab_counters, $c){
	$grabcount = count($grab_counters);
	switch($grab_counters[$c]['type']){
		case 'date':
			//$grab_counters[$c]['datevalue']++;
			$grab_counters[$c]['datevalue'] = date('Y-m-d H:i:s', strtotime($grab_counters[$c]['datevalue']) + (60 * 60 * 24)); // + 1 day
			if($grab_counters[$c]['datevalue'] > $grab_counters[$c]['dateto']){
				$grab_counters[$c]['datevalue'] = $grab_counters[$c]['datefrom'];
				// increase next counter if any
				if($c < $grabcount - 1){
					increase_counter($grab_counters, $c + 1);
				}
				// or reset all
				else {
					for($ci = 0; $ci < $grabcount; $ci++){
						switch($grab_counters[$ci]['type']){
							case 'date':
								$grab_counters[$ci]['datevalue'] = $grab_counters[$ci]['datefrom'];
								break;
								
							case 'int':
								$grab_counters[$ci]['intvalue'] = $grab_counters[$ci]['intfrom'];
								break;
							
							case 'list':
								$grab_counters[$ci]['listindex'] = 0;
								break;
						}
					}
				}
			}
			break;
		
		case 'int':
			$grab_counters[$c]['intvalue']++;
			if($grab_counters[$c]['intvalue'] > $grab_counters[$c]['intto']){
				$grab_counters[$c]['intvalue'] = $grab_counters[$c]['intfrom'];
				// increase next counter if any
				if($c < $grabcount - 1){
					increase_counter($grab_counters, $c + 1);
				}
				// or reset all
				else {
					for($ci = 0; $ci < $grabcount; $ci++){
						switch($grab_counters[$ci]['type']){
							case 'date':
								$grab_counters[$ci]['datevalue'] = $grab_counters[$ci]['datefrom'];
								break;
							
							case 'int':
								$grab_counters[$ci]['intvalue'] = $grab_counters[$ci]['intfrom'];
								break;
							
							case 'list':
								$grab_counters[$ci]['listindex'] = 0;
								break;
						}
					}
				}
			}
			break;
		
		case 'list':
			$listvalues = explode(',', $grab_counters[$c]['listvalues']);
			$listvaluescount = count($listvalues);
			//$listindex = 0;
			
			$grab_counters[$c]['listindex']++;
			if($grab_counters[$c]['listindex'] >= $listvaluescount){
				$grab_counters[$c]['listindex'] = 0;
				if($c < $grabcount - 1){
					increase_counter($grab_counters, $c + 1);
				}
				// or reset all
				else {
					for($ci = 0; $ci < $grabcount; $ci++){
						switch($grab_counters[$ci]['type']){
							case 'date':
								$grab_counters[$ci]['datevalue'] = $grab_counters[$ci]['datefrom'];
								break;
							
							case 'int':
								$grab_counters[$ci]['intvalue'] = $grab_counters[$ci]['intfrom'];
								break;
							
							case 'list':
								$grab_counters[$ci]['listindex'] = 0;
								break;
						}
					}
				}
			}
			
			break;
	}
	
}

function cURLcheckBasicFunctions()
{
	if( function_exists("curl_init")
		&& function_exists("curl_setopt")
		&& function_exists("curl_exec")
		&& function_exists("curl_close") 
	) return true;
	else return false;
}

/*
 * Returns string status information.
 * Can be changed to int or bool return types.
 */
function cURLdownload($url, $file, $redirects = 5, $referer = '')
{
	if( !cURLcheckBasicFunctions() ) return "UNAVAILABLE: cURL Basic Functions";
	$ch = curl_init();
	if($ch)
	{
		$fp = fopen($file, "w");
		if($fp)
		{
			if( !curl_setopt($ch, CURLOPT_URL, $url) )
			{
				fclose($fp); // to match fopen()
				curl_close($ch); // to match curl_init()
				return "FAIL: curl_setopt(CURLOPT_URL)";
			}
			if ((!ini_get('open_basedir') && !ini_get('safe_mode')) || $redirects < 1) {
				curl_setopt($ch, CURLOPT_USERAGENT, '"Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.11) Gecko/20071204 Ubuntu/7.10 (gutsy) Firefox/2.0.0.11');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				if($referer != ''){
					curl_setopt($ch, CURLOPT_REFERER, $referer);
				}
				//if( !curl_setopt($ch, CURLOPT_HEADER, $curlopt_header)) return "FAIL: curl_setopt(CURLOPT_HEADER)";
				if( !curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $redirects > 0)) return "FAIL: curl_setopt(CURLOPT_FOLLOWLOCATION)";
				if( !curl_setopt($ch, CURLOPT_FILE, $fp) ) return "FAIL: curl_setopt(CURLOPT_FILE)";
				if( !curl_setopt($ch, CURLOPT_MAXREDIRS, $redirects) ) return "FAIL: curl_setopt(CURLOPT_MAXREDIRS)";

				return curl_exec($ch);
			}
			else {
				curl_setopt($ch, CURLOPT_USERAGENT, '"Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.11) Gecko/20071204 Ubuntu/7.10 (gutsy) Firefox/2.0.0.11');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				if($referer != ''){
					curl_setopt($ch, CURLOPT_REFERER, $referer);
				}
				if( !curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false)) return "FAIL: curl_setopt(CURLOPT_FOLLOWLOCATION)";
				if( !curl_setopt($ch, CURLOPT_FILE, $fp) ) return "FAIL: curl_setopt(CURLOPT_FILE)";
				if( !curl_setopt($ch, CURLOPT_HEADER, true)) return "FAIL: curl_setopt(CURLOPT_HEADER)";
				if( !curl_setopt($ch, CURLOPT_RETURNTRANSFER, true)) return "FAIL: curl_setopt(CURLOPT_RETURNTRANSFER)";
				if( !curl_setopt($ch, CURLOPT_FORBID_REUSE, false)) return "FAIL: curl_setopt(CURLOPT_FORBID_REUSE)";
				curl_setopt($ch, CURLOPT_USERAGENT, '"Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.11) Gecko/20071204 Ubuntu/7.10 (gutsy) Firefox/2.0.0.11');
			}
			// if( !curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true) ) return "FAIL: curl_setopt(CURLOPT_FOLLOWLOCATION)";
			// if( !curl_setopt($ch, CURLOPT_FILE, $fp) ) return "FAIL: curl_setopt(CURLOPT_FILE)";
			// if( !curl_setopt($ch, CURLOPT_HEADER, 0) ) return "FAIL: curl_setopt(CURLOPT_HEADER)";
			if( !curl_exec($ch) ) return "FAIL: curl_exec()";
			/*$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if($httpCode == 404) {
				
			}*/
			curl_close($ch);
			fclose($fp);
			return "SUCCESS: $file [$url]";
		}
		else return "FAIL: fopen()";
	}
	else return "FAIL: curl_init()";
}


function downloadFile ($url, $path) {
	$newfname = $path;
	$file = fopen ($url, "rb");
	if ($file) {
		$newf = fopen ($newfname, "wb");

		if ($newf){
			while(!feof($file)) {
				fwrite($newf, fread($file, 1024 * 8 ), 1024 * 8 );
			}
		}
	}

	if ($file) {
		fclose($file);
	}

	if ($newf) {
		fclose($newf);
	}
}
				


/*
function getHeaders($httpobj){
	
	$ret = structnew();
	if(isset(httpobj.header)){
		<cfloop index="i" from="1" to="#ListLen(httpobj.header, chr(10)&','&chr(13))#">
			$fieldname_orig = ListGetAt(ListGetAt(httpobj.header, i, chr(10)&','&chr(13)), 1, ':');
			$fieldname = fieldname_orig;
			$fieldname = Replace(fieldname, '-', '_', 'all');
			$fieldname = Replace(fieldname, '.', '_dot_', 'all');
			$fieldname = Replace(fieldname, '/', '_slash_', 'all');
			
			// HTTP/1.1 200 OK
			if(FindNoCase('http', fieldname_orig) == 1){
				$ret.HttpStatusCode = fieldname_orig;
			}
			else {
				if(ListLen(ListGetAt(httpobj.header, i, chr(10)&','&chr(13)), ':')  >= 2){
					$data = ListGetAt(httpobj.header, i, chr(10)&','&chr(13));
					$data = ReplaceNoCase(data, fieldname_orig, '');
					$data = ReplaceNoCase(data, ':', '');
					$ret[fieldname] = trim(data);
				}
				else {
					$ret[fieldname] = '';
				}
			}
		</cfloop>
	}
	
	return $ret;
}
*/
?>