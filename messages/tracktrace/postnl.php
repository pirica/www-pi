<?php

$str = file_get_contents('https://jouw.postnl.be/api/shipment?barcode='.$tt['tracking_code'].'&language=nl&postalCode='.$tt['postal_code'].'&_'.time().'=');
$str = substr($str,1,strlen($str)-2);
$o = json_decode($str);

//print_r($o);

$msg = $o->phaseMessage;

//echo $msg;


?>