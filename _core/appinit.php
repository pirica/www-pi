<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

require dirname(__FILE__).'/../_core/com/App.php';
require dirname(__FILE__).'/../_core/com/Settings.php';
require dirname(__FILE__).'/../_core/com/Action.php';
require dirname(__FILE__).'/../_core/com/User.php';

require dirname(__FILE__).'/functions.php';

require dirname(__FILE__).'/../users/sec-users.php';

$request_uri = '';
// from command line
//if(isset($_SERVER['TERM']) && isset($_SERVER['SHELL'])){
	$request_uri = str_replace('/var/www', '', $_SERVER['SCRIPT_FILENAME']);
//}
// from www
/*else if(isset($_SERVER['REQUEST_URI'])){
	$request_uri = $_SERVER['REQUEST_URI'];
}*/


$app = new App($mysqli, $request_uri);
$settings = new Settings($mysqli, $app->getId());

sec_session_start();

$_SESSION['log'] = '';

$user = new User($mysqli, $app->getId(), $_SESSION);

$loggedin = login_check($mysqli);
$id_profile = $settings->val('default_profile_notloggedin', -1);

$action = new Action($mysqli, $app->getId(), saneInput('action', 'string', ''), $id_profile);

$_SESSION['log'] .= '1:' . $action->getId() . '-' . $action->getCode() . '-' . $action->getAllowed() . "\n";

$app->setTitle( $action->getPageTitle() );

if ($loggedin){
	$id_profile = $_SESSION['id_profile'];
}

if ($action->getLoginRequired() && !$loggedin){
	$action = new Action($mysqli, $app->getId(), 'login', $id_profile);
	$_SESSION['url_after_login'] = get_url_after_login();
	
	$_SESSION['log'] .= '2:' . $action->getId() . '-' . $action->getCode() . "\n";
}
else if ($action->getLoginRequired() && !$action->getAllowed()){
	$action = new Action($mysqli, $app->getId(), 'login', $id_profile);
	$_SESSION['url_after_login'] = get_url_after_login();
	
	$_SESSION['log'] .= '3:' . $action->getId() . '-' . $action->getCode() . "\n";
}

?>