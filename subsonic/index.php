<?php
include 'connection.php';
//include 'functions.php';
//include 'act_settings.php';

require '../_core/webinit.php';

$playlistId = saneInput('playlistId', 'int', -1);
$songId = saneInput('songId', 'intlist', -1);
$songIndex = saneInput('songIndex', 'int', -1);

$app->setHeaderScripts('<script type="text/javascript">var playlistId = ' . $playlistId . ', songId = ' . $songId . ', songIndex = ' . $songIndex . ', dir = \'' . /*$dir .*/ '\';</script>' . "\n");

switch($action->getCode()){
	
	
	case 'playlists':
		include 'queries/pr_get_playlists.php';
		
		$for_action = false;
		
		include '../_core/dsp_header.php';
		include 'dsp_playlists.php';
		include '../_core/dsp_footer.php';
		break;
	
	case 'playlist':
		include 'queries/pr_get_playlists.php';
		include 'queries/pr_get_playlist_entries.php';
		$playlist = array(
			'id' => -1,
			'name' => ''
		);
		while($_playlist = mysql_fetch_array($qry_playlists)){
			if($_playlist['id'] == $playlistId){
				$playlist = $_playlist;
			}
		}
		
		include '../_core/dsp_header.php';
		include 'dsp_playlist.php';
		include '../_core/dsp_footer.php';
		break;
	
	
	case 'add_playlist':
		include 'act_playlist_add.php';
		
		include '../_core/dsp_header.php';
		include 'dsp_playlist_add.php';
		include '../_core/dsp_footer.php';
		break;
	
	case 'del_playlist':
		include 'act_playlist_delete.php';
		break;
	
	
	case 'add_playlist_entry':
		include 'queries/pr_get_playlists.php';
		$for_action = true;
		include 'dsp_playlists.php';
		break;
	
	case 'do_add_playlist_entry':
		include 'act_playlist_add_entry.php';
		break;
	
	
	case 'songs_recent':
	case 'songs_search':
		include 'act_init_songs.php';
		
		include '../_core/dsp_header.php';
		include 'dsp_songs.php';
		include '../_core/dsp_footer.php';
		break;
	
	// main: overview
	default:
		include 'queries/pr_get_playlists.php';
		
		include '../_core/dsp_header.php';
		include 'dsp_main.php';
		include '../_core/dsp_footer.php';
	
}


?>