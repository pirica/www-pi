<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

require dirname(__FILE__).'/../_core/com/App.php';
require dirname(__FILE__).'/../_core/com/Settings.php';
require dirname(__FILE__).'/../_core/com/Action.php';
require dirname(__FILE__).'/../_core/com/User.php';

require dirname(__FILE__).'/functions.php';

require dirname(__FILE__).'/../users/sec-users.php';

require_once dirname(__FILE__).'/../_core/components/phpfastcache/phpfastcache.php';


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

phpFastCache::setup("storage","files");
$cache = phpFastCache();

sec_session_start();

$_SESSION['log'] = '';

$_SESSION['shell'] = 0;
// from command line
if(isset($_SERVER['SHELL'])){
	$_SESSION['shell'] = 1;
}

if(isset($_GET['__mode'])){
	$app->setMode($_GET['__mode']);
}

$user = new User($mysqli, $app->getId(), $_SESSION);

$loggedin = login_check($mysqli);
$id_profile = $settings->val('default_profile_notloggedin', -1);
if ($loggedin){
	$id_profile = $_SESSION['id_profile'];
}

$action = new Action($conn_users, $app->getId(), saneInput('action', 'string', ''), $id_profile);

$_SESSION['log'] .= '1:' . $action->getId() . '-' . $action->getCode() . '-' . $action->getAllowed() . "\n";

$app->setTitle( $action->getPageTitle() );


if($_SESSION['shell'] == 0 && (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], 'index.php') !== false)){
	if ($action->getLoginRequired() == 1 && !$loggedin){
		$action = new Action($conn_users, $app->getId(), 'login', $id_profile);
		if($app->getId() > 0){
			$_SESSION['url_after_login'] = get_url_after_login();
		}
		$_SESSION['log'] .= '2:' . $action->getId() . '-' . $action->getCode() . "\n";
	}
	else if ($action->getLoginRequired() == 1 && $action->getAllowed() == 0){
		$action = new Action($conn_users, $app->getId(), 'notallowed', $id_profile);
		//$_SESSION['url_after_login'] = get_url_after_login();
		
		$_SESSION['log'] .= '3:' . $action->getId() . '-' . $action->getCode() . "\n";
	}
}

// custom table editor
if($action->getEditorId() > 0)
{
	$mode = saneInput('mode');
	$id = saneInput('id', 'int', -1);
	
	require dirname(__FILE__).'/../users/act_init_tableeditor.php';
	
	if($mode == 'edit')
	{
		require dirname(__FILE__).'/../_core/dsp_header.php';
		require dirname(__FILE__).'/../users/dsp_tableeditor_edit.php';
		require dirname(__FILE__).'/../_core/dsp_footer.php';
	}
	else if($mode == 'delete')
	{
		require dirname(__FILE__).'/../users/dsp_tableeditor_delete.php';
	}
	else if($mode == 'save')
	{
		goto_action($action->getCode(), false);
	}
	else if($mode == 'dodelete')
	{
		goto_action($action->getCode(), false);
	}
	else 
	{
		require dirname(__FILE__).'/../_core/dsp_header.php';
		require dirname(__FILE__).'/../users/dsp_tableeditor_overview.php';
		require dirname(__FILE__).'/../_core/dsp_footer.php';
	}
	exit();
	
}
else 
{
	switch($action->getCode()){

		case 'login':
			require dirname(__FILE__).'/../_core/dsp_header.php';
			require dirname(__FILE__).'/../users/dsp_loginform.php';
			require dirname(__FILE__).'/../_core/dsp_footer.php';
			exit();
			break;
			
		case 'notallowed':
			require dirname(__FILE__).'/../_core/dsp_header.php';
			require dirname(__FILE__).'/../users/dsp_notallowed.php';
			require dirname(__FILE__).'/../_core/dsp_footer.php';
			exit();
			break;
			
	}
}

?>