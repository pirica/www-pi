<?php
require 'connection.php';
//require 'functions.php';

require '../_core/appinit.php';


switch($action->getCode()){
	/*
	case 'login':
		require '../_core/dsp_header.php';
		require '../users/dsp_loginform.php';
		require '../_core/dsp_footer.php';
		break;
	*/
	
	case 'showtracks':
		include 'queries/pr_get_tracks.php';
		
		require '../_core/dsp_header.php';
		require 'dsp_tracks.php';
		require '../_core/dsp_footer.php';
		break;
	
	
	
	case 'setplaces':
		include 'queries/pr_get_places.php';
		
		require '../_core/dsp_header.php';
		require 'dsp_places.php';
		require '../_core/dsp_footer.php';
		break;
	
	case 'setplace':
		$id_place = saneInput('id_place', 'int', -1);
		include 'queries/pr_get_places.php';
		
		$place_description = '';
		$place_pre_description = '';
		$place_lat_top = 'null';
		$place_lon_right = 'null';
		$place_lat_bottom = 'null';
		$place_lon_left = 'null';
		
		while($place = mysql_fetch_array($qry_places)){ 
			if($place['id_place'] == $id_place){
				$place_description = $place['description'];
				$place_pre_description = $place['pre_description'];
				
				$place_lat_top = $place['lat_top'];
				$place_lon_right = $place['lon_right'];
				$place_lat_bottom = $place['lat_bottom'];
				$place_lon_left = $place['lon_left'];
			}
		}
		
		if($id_place > 0){
			$app->setTitle('Location ' . $place_description);
		}
		else {
			$app->setTitle('New location');
		}
		$app->setHeaderScripts('<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?libraries=drawing"></script>' . "\n");
		
		require '../_core/dsp_header.php';
		require 'dsp_place.php';
		require '../_core/dsp_footer.php';
		break;
	
	case 'do_setplace':
		$id_place = saneInput('id_place', 'int', -1);
		include 'act_set_place.php';
		goto_action('main', false, 'id_place=' . $id_place);
		break;
		
	
	case 'delplace':
		$id_place = saneInput('id_place', 'int', -1);
		$ajaxcall = saneInput('ajaxcall', 'boolean', true);
		
		include 'queries/pr_get_places.php';
		
		$place_description = '';
		
		while($place = mysql_fetch_array($qry_places)){ 
			if($place['id_place'] == $id_place){
				$place_description = $place['description'];
			}
		}
		
		if($ajaxcall === false){
			include '../_core/dsp_header.php';
		}
		include 'dsp_delete_place.php';
		if($ajaxcall === false){
			include '../_core/dsp_footer.php';
		}
		break;
	
	case 'do_delplace':
		include 'act_del_place.php';
		goto_action('setplaces', false, 'id_place=' . $id_place);
		break;
		
	
	
	case 'track':
		$app->setHeaderScripts('<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>' . "\n");
		//$app->setHeaderScripts('<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>' . "\n");
		
		require '../_core/dsp_header.php';
		require 'dsp_track.php';
		require '../_core/dsp_footer.php';
		break;
	
	
	case 'js_position':
		$id_user = saneInput('id_user', 'int', -1);
		
		$qry_log = mysql_query("
			select
				lt.*,
				u.id_user,
				p.id_place
			from t_log_track lt
			left join t_user u on u.username = lt.username
			left join t_place p on p.
			where u.id_user = 
			order by lt.time desc
			
			");
		echo '{"lat": "51.12361720763147", "lon": "5.011744424700737", "speed": "13.6875", "accuracy":"5", "heading":"-27.16271730885871", "time":"1375905366719", "time_lbl":""}';
		break;
	
	// main: overview
	default:
		include '../_core/dsp_header.php';
		//require 'dsp_submenu.php';
		include 'dsp_main.php';
		include '../_core/dsp_footer.php';
        break;
		
}
?>