<?php

$request_uri = '';
if(isset($_SERVER['REQUEST_URI'])){
	$request_uri = $_SERVER['REQUEST_URI'];
}

if($request_uri == '' || $request_uri == '/'){
	$request_uri = '/';
}
else {
	$request_uri = '/' . explode("/", $request_uri)[1];
}
//echo '<!--u:'.$request_uri.'-->';

$qry_apps = mysql_query("
	
	select
		a.id_app,
		a.description,
		'' as info,
		a.relative_url,
		a.show_in_overview,
		a.show_in_topmenu,
		a.login_required,
		count(aa.id_app_action) as menu_actions,
		
		case when '" . mysql_real_escape_string($request_uri) . "' = a.relative_url then 1 else 0 end as is_current
		
	from users.t_app a
		join users.t_profile p on p.id_profile = " . $id_profile . "
		left join users.t_profile_app pa on pa.id_app = a.id_app and pa.id_profile = p.id_profile
		left join users.t_app_action aa on aa.id_app = a.id_app and aa.show_in_menu = 1
	
	where
		pa.allowed = 1 or p.full_access = 1
		
	group by
		a.id_app
		
	order by
		ifnull(a.sort_order, a.id_app)
		
	", $conn_users);
	

	
$array_actions = $cache->get("topmenu_actions");

if($array_actions == null) {
	
	$qry_actions = mysql_query("
		
		select
			ifnull(aa.id_app, -1) as id_app,
			aa.id_app_action,
			aa.code,
			ifnull(nullif(aa.page_title,''), aa.code) as page_title,
			aa.login_required,
			aa.show_in_menu,
			count(aad.id_app_action_data) as menu_subs
			
		from users.t_app_action aa
			left join users.t_app_action_data aad on aad.id_app = aa.id_app and aad.code = aa.code and aad.active >= 1
		
		where
			aa.active = 1
		
		group by
			aa.id_app_action
			
		order by
			aa.sort_order,
			ifnull(aa.code,'Main')
			
			
		", $conn_users);
	
	$array_actions = array();
	while($menu_action = mysql_fetch_array($qry_actions))
	{
		$array_actions[] = array(
			'id_app' => $menu_action['id_app'],
			'id_app_action' => $menu_action['id_app_action'],
			'code' => $menu_action['code'],
			'page_title' => $menu_action['page_title'],
			'login_required' => $menu_action['login_required'],
			'show_in_menu' => $menu_action['show_in_menu'],
			'menu_subs' => $menu_action['menu_subs']
		);
	}
	
	$cache->set("topmenu_actions", $array_actions, 8 * 60 * 60);
}



$cache_name = "topmenu_actions_data";
$userid = -1;
if(isset($_SESSION['user_id']))
{
	$cache_name = "topmenu_actions_data_" . $_SESSION['user_id'];
	$userid = $_SESSION['user_id'];
}

$array_actions_data = $cache->get($cache_name);

if($array_actions_data == null) {
	
	$qry_actions_data = mysql_query("
		
		select
			ifnull(aa.id_app, -1) as id_app,
			aa.id_app_action_data,
			aa.code,
			aa.url,
			aa.description
			
		from users.t_app_action_data aa
		
		where
			aa.active >= 1
			and ifnull(aa.id_user, " . $userid . ") = " . $userid . "
			
		order by
			aa.sort_order,
			aa.description
			
			
		", $conn_users);
	
	$array_actions_data = array();
	while($menu_action = mysql_fetch_array($qry_actions_data))
	{
		$array_actions_data[] = array(
			'id_app' => $menu_action['id_app'],
			'id_app_action_data' => $menu_action['id_app_action_data'],
			'code' => $menu_action['code'],
			'url' => $menu_action['url'],
			'description' => $menu_action['description']
		);
	}
	
	$cache->set($cache_name, $array_actions_data, 8 * 60 * 60);
}
	
?>