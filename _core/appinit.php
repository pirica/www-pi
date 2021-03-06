<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

require dirname(__FILE__).'/../_core/components/rollbar/rollbar.php';
Rollbar::init(array(
    'access_token' => '1c76ba313eea4f0da68747e2c660f3a4',
    'environment' => 'production',
    'root' => '/var/www/html'
));

require dirname(__FILE__).'/../_core/com/App.php';
require dirname(__FILE__).'/../_core/com/Settings.php';
//require dirname(__FILE__).'/../_core/com/Action.php';
//require dirname(__FILE__).'/../_core/com/User.php';
require dirname(__FILE__).'/../_core/com/Task.php';

require dirname(__FILE__).'/functions.php';

require dirname(__FILE__).'/../users/sec-users.php';

require_once dirname(__FILE__).'/../_core/components/phpfastcache/phpfastcache.php';


$request_uri = $_SERVER['SCRIPT_FILENAME'];
// from command line
//if(isset($_SERVER['TERM']) && isset($_SERVER['SHELL'])){
	$request_uri = str_replace('/var/www/html', '', $request_uri);
	$request_uri = str_replace('/var/www', '', $request_uri);
//}
// from www
/*else if(isset($_SERVER['REQUEST_URI'])){
	$request_uri = $_SERVER['REQUEST_URI'];
}*/

/*
phpFastCache::setup("storage","files");
phpFastCache::setup("path","/tmp/rudy/");
$cache = phpFastCache();
*/

$app = new App($mysqli, $request_uri);
$settings = new Settings($mysqli, $app->getId()/*, $cache*/);
$task = new Task($mysqli, $app->getId(), $request_uri);

/*
sec_session_start();

$_SESSION['log'] = '';

$_SESSION['shell'] = 0;
// from command line
if(isset($_SERVER['SHELL'])){
	$_SESSION['shell'] = 1;
}
*/

//$user = new User($mysqli, $app->getId(), $_SESSION);

//$loggedin = login_check($mysqli);
//$id_profile = $settings->val('default_profile_notloggedin', -1);

//$action = new Action($mysqli, $app->getId(), saneInput('action', 'string', ''), $id_profile);

//$_SESSION['log'] .= '1:' . $action->getId() . '-' . $action->getCode() . '-' . $action->getAllowed() . "\n";

//$app->setTitle( $action->getPageTitle() );

/*if ($loggedin){
	$id_profile = $_SESSION['id_profile'];
}*/


?>