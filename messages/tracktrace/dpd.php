<?php

$str = file_get_contents('https://tracking.dpd.de/cgi-bin/simpleTracking.cgi?parcelNr='.$tt['tracking_code'].'&locale=nl_BE&type=1&jsoncallback=&_'.time().'=');
$str = substr($str,1,strlen($str)-2);
$o = json_decode($str);

//print_r($o);

$d = $o->TrackingStatusJSON->statusInfos;

//print_r($d);

$msg = $d[count($d)-1]->contents[0]->label;

//echo $msg;


?>