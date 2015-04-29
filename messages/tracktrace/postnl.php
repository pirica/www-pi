<?php

$response = get_headers('https://mijnpakket.postnl.nl/Claim?barcode='.$tt['tracking_code'].'&postalcode='.$tt['postal_code'].'&CountryIso=BE', 1);
//print_r($response);

$cookies = 'Cookie: ';

foreach($response as $c=>$v){
	if($c == 'Set-Cookie'){
		$cc = count($v);
		for($i=0; $i<$cc; $i++){
			$cookies .= "" . explode(';', $v[$i])[0] . "; ";
		}
	}
}

//echo 'cookies:' . $cookies;

$opts = array(
	'http'=>array(
		'method'=>"GET",
		'header'=>"Accept-language: en\r\n" .
		$cookies . "\r\n"
	)
);
$context = stream_context_create($opts);
$str = file_get_contents('https://mijnpakket.postnl.nl/Inbox/Claimed', false, $context);

$DOM = new DOMDocument;
$DOM->loadHTML($str, LIBXML_NOERROR);

//get all H1
$items = $DOM->getElementsByTagName('div');

//display all H1 text
for ($i = $items->length - 1; $i >= 0; $i--){
	if(stripos($items->item($i)->nodeValue, 'Status zending') !== false){
		echo $items->item($i)->textContent;
		if($status != $items->item($i)->textContent){
			$msg = $items->item($i)->textContent;

			$msg = str_replace("\r", ' ', $msg);
			$msg = str_replace("\n", ' ', $msg);
			$msg = str_replace("\t", ' ', $msg);
			$msg = str_replace('  ', ' ', $msg);
			$msg = str_replace('  ', ' ', $msg);
			$msg = str_replace('  ', ' ', $msg);
			$msg = str_replace('  ', ' ', $msg);
			$msg = str_replace('  ', ' ', $msg);
			$msg = str_replace('  ', ' ', $msg);
			$msg = str_replace('  ', ' ', $msg);

			$msg = str_replace("Status zending: ", '', $msg);

			$status_changed = true;
			
			break;
		}
	}
}

?>