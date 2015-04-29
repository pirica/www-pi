<?php
error_reporting(E_ALL);
//session_start();
require dirname(__FILE__).'/nmaApi.class.php';


function send_nma_msg($channel, $title, $message, $priority = 0){

	/**
	 *	send a NotifyMyAndroid message
	 *	notify($application = '', $event = '', $description = '', $priority = 0, $apiKeys = false, $options = array())
	 */
	//$nma = new nmaApi(array('apikey' => 'ae87ee05b547fc07cc129eeef0406b061a32ecdf189197e6'));
	
	$nma = new nmaApi(array('apikey' => 'f5a6f0ddebd08006dea7f2398a79f7de1499a4a33844c468'));	// main
	//$nma = new nmaApi(array('apikey' => '28eb41c6067d3bbc0111e2696ae6f273518c69287dbdacdd'));	// second

	if($title == ''){
		$title = $channel;
	}
	if($message == ''){
		$message = $title;
	}
	
	if($channel != ''){
		if($nma->verify()){
			if($nma->notify($channel, $title, $message, $priority)){
				//echo "Notifcation sent!";
				return true;
			}
		}
	}
	else {
		//echo 'message or channel not set';
	}
	return false;

}
?>