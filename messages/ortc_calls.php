<?php
require dirname(__FILE__).'/ortc.php';
//session_start();



function send_ortc_msg($channel, $message, &$response = Array()){
	/* -------------------- */
	/* REPLACE THESE VALUES */
	/* -------------------- */
	$URL = 'http://ortc-developers.realtime.co/server/2.1';
	$AK = 'pAFwtu';					// your realtime.co application key
	$PK = 'Enosb2vlZyqr';			// your realtime.co private key
	$TK = 'myAuthenticationToken';	// token: could be randomly generated in the session
	$ttl = 180000;					// just a very large time to live
	//$CH = 'motion:cam1';			//channel
	$use_session = false;
	$authenticated = false;

	// ORTC auth
	// on a live usage we would already have the auth token authorized and stored in a php session
	// Since a developer appkey does not require authentication the following code is optional

	if(($use_session && !array_key_exists('ortc', $_SESSION)) || !$use_session ){
		//$_SESSION['ortc_token'] = $TK;
		$realtime = new Realtime( $URL, $AK, $PK, $TK );
		$_SESSION['ortc'] = $realtime;
		$authenticated = $realtime->auth(
			array(
				//$CH => 'w'
				$channel => 'wpr'
			),
			$ttl
		);//post authentication permissions. w -> write; r -> read
		//print 'authentication status '.( $authenticated ? 'success' : 'failed' ).'<br>\n';
	}
	else {
		$realtime = $_SESSION['ortc'];
		$authenticated = $realtime->auth(
			array(
				//$CH => 'w'
				$channel => 'wpr'
			),
			$ttl
		);
	}
	/*
	if($authenticated){
		$result = $realtime->send($CH, $msg, $response);
		print 'send status '.( $result ? 'success' : 'failed' ).'<br>\n';
		print 'response:'. $response .'<br>\n';
	}
	*/
	
	if($authenticated){
		$result = $realtime->send($channel, $message, $response);
		//print 'send status '.( $result ? 'success' : 'failed' ).'<br>\n';
		//print 'response:'. $response .'<br>\n';
		return $result;
	}
	else {
		return false;
	}
}
?>