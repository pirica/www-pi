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
if(isset($_SERVER['TERM']) && isset($_SERVER['SHELL'])){
	$request_uri = str_replace('/var/www', '', $_SERVER['SCRIPT_FILENAME']);
}
// from www
else if(isset($_SERVER['REQUEST_URI'])){
	$request_uri = $_SERVER['REQUEST_URI'];
}


$app = new App($mysqli, $request_uri);
$settings = new Settings($mysqli, $app->getId());
$action = new Action($mysqli, $app->getId(), saneInput('action', 'string', ''));

$app->setTitle( $action->getPageTitle() );

sec_session_start();

$user = new User($mysqli, $app->getId(), $_SESSION);

$loggedin = login_check($mysqli);

if (!$loggedin && $action->getLoginRequired()){
	$action = new Action($mysqli, $app->getId(), 'login');
	$_SESSION['url_after_login'] = get_url_after_login();
}


?>