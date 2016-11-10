<?php
require '../_core/webinit.php';

include 'connection.php';
//include 'functions.php';
//include 'act_settings.php';

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
		while($_playlist = mysqli_fetch_array($qry_playlists)){
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
	
	case 'songs_in_incorrect_dir':
		/*
SELECT * FROM `songs` WHERE type = 'music'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'a/a%' 
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'b/b%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'c/c%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'd/d%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'e/e%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'f/f%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'g/g%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'h/h%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'i/i%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'j/j%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'k/k%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'l/l%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'm/m%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'n/n%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'o/o%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'p/p%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'q/q%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'r/r%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 's/s%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 't/t%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'u/u%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'v/v%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'w/w%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'x/x%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'y/y%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE 'z/z%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE '0/0%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE '0/1%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE '0/2%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE '0/3%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE '0/4%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE '0/5%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE '0/6%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE '0/7%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE '0/8%'
AND replace(replace(replace(lower(`path`),'_',' '), 'the ', ''), 'dj ', '') NOT LIKE '0/9%'
		*/
		break;
	
	case 'songs_with incorrect_filename':
		/*
SELECT * FROM `songs` WHERE type = 'music'
and `path` like '%-%'
and `path` not like '% - %'
;
SELECT * FROM `songs` WHERE type = 'music'
and `path` like '%rmx%'
;
SELECT * FROM `songs` WHERE type = 'music'
and `path` like '%_%'
;
SELECT * FROM `songs` WHERE type = 'music'
and `path` like '%mix%'
and `path` not like '%(%'
;

SELECT * FROM `songs` WHERE type = 'music'
and `path` like '%  %'
;
SELECT * FROM `songs` WHERE type = 'music'
and `path` not like '%-%'
;

-- -- --

SELECT 
concat(
	'mv ".', relative_directory, filename, '" ',
	'".', relative_directory, replace(replace(replace(filename,'_',' '),'[','('),']',')'), '"'
) as moves
FROM t_file WHERE id_share = 1 and active = 1
and filename like '%-%'
and replace(filename,'_',' ') not like '% - %'
;

SELECT 
concat(
	'mv ".', relative_directory, filename, '" ',
	'".', relative_directory, replace(replace(replace(filename,'_',' '),'[','('),']',')'), '"'
) as moves
FROM t_file WHERE id_share = 1 and active = 1
and filename like '%  %'
;


SELECT
concat(
	'mv ".', relative_directory, filename, '" ',
	'".', relative_directory, replace(replace(replace(filename,'_',' '),'[','('),']',')'), '"'
)
#* 
FROM t_file WHERE id_share = 1 and active = 1
and filename like '%mp3'
and filename like '%)%'
and filename not like '%(%'
;

SELECT
concat(
	'mv ".', relative_directory, filename, '" ',
	'".', relative_directory, replace(replace(replace(filename,'_',' '),'[','('),']',')'), '"'
)
#* 
FROM t_file WHERE id_share = 1 and active = 1
and filename like '%mp3'
and filename like '%(%'
and filename not like '%)%'
;

SELECT
concat(
	'mv ".', relative_directory, filename, '" ',
	'".', relative_directory, replace(replace(replace(filename,'_',' '),'[','('),']',')'), '"'
)
#* 
FROM t_file WHERE id_share = 1 and active = 1
and filename like '%mp3'
and (filename like '%[%' or filename like '%]%')
;

SELECT 
concat(
	'mv ".', relative_directory, filename, '" ',
	'".', relative_directory, replace(replace(replace(filename,'_',' '),'[','('),']',')'), '"'
) as moves
FROM t_file WHERE id_share = 1 and active = 1
and filename like '%mp3'
and filename like '%10%'
and filename not like '10. %'
and relative_directory like '%/%/%/%'
;


		*/
		break;
		
	// main: overview
	default:
		include 'queries/pr_get_playlists.php';
		
		include '../_core/dsp_header.php';
		include 'dsp_main.php';
		include '../_core/dsp_footer.php';
	
}


?>