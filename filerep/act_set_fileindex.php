<?php
set_time_limit(0);

$logging = '';

$date_last_replicated = saneInput('date_last_replicated'); // seconds since epoch
$filestr = saneInput('files', 'string', '[]');

if($date_last_replicated != ''){
	mysqli_query($conn, "
		update t_host_share
		set
			date_last_replicated = '" . $date_last_replicated . "'
		where
			id_share = " . $id_share . " 
			and id_host = " . $id_host . " 
			and active = 1
		");
}

$files = json_decode($filestr);

$modifiedcount = 0;
$insertcount = 0;
$conflictcount = 0;
$dpmod_newc_count = 0;
$dpmod_nonc_count = 0;
$dpmod_nonc_c_count = 0;

// mark all files as not found first
mysqli_query($conn, "
	update t_file_index
	set
		notfound = 1
	
	where
		id_share = " . $id_share . " 
		and id_host = " . $id_host . " 
	");
	

// clear all my actions
mysqli_query($conn, "
	delete from t_file_action
	where
		(id_share = " . $id_share . " 
		and id_host = " . $id_host . " )
		or date_action < now() - interval 5 day
	");
	
// get indexed files
$dbfiles = [];
$qry_index = mysqli_query($conn, "
	select
		relative_directory,
		filename
	from t_file_index
	where
		id_share = " . $id_share . " 
		and id_host = " . $id_host . " 
	");
while ($dbfile = mysqli_fetch_array($qry_index)) {
	$dbfiles[] = $dbfile;
}

$filelen = count($files);
for ($i = 0; $i < $filelen; $i++) {
	//echo $i . ': ' . $files[$i]->p . '<br/>' ;
	
	$file_found = 0;
	
	$path = $files[$i]->p;
	if(substr($path, -1, 1) != '/'){
		$path = $path . '/';
	}
	
	$dbfilelen = count($dbfiles);
	for ($j = 0; $j < $dbfilelen; $j++) {
		
		// if the file is already in the index => update
		if($dbfiles[$j]['relative_directory'] == $path && $dbfiles[$j]['filename'] == $files[$i]->n){
			$file_found = 1;
			
			if($files[$i]->c == 1){
				mysqli_query($conn, "
					update t_file_index
					set
						date_previous_modified = date_last_modified
					where
						id_share = " . $id_share . " 
						and id_host = " . $id_host . " 
						and relative_directory = '" . mysqli_real_escape_string($path) . "'
						and filename = '" . mysqli_real_escape_string($files[$i]->n) . "'
						and conflict = 0
					");
				$dpmod_newc_count = $dpmod_newc_count + mysqli_affected_rows($conn);
			}
			else {
				mysqli_query($conn, "
					update t_file_index
					set
						date_previous_modified = date_last_modified
					where
						id_share = " . $id_share . " 
						and id_host = " . $id_host . " 
						and relative_directory = '" . mysqli_real_escape_string($path) . "'
						and filename = '" . mysqli_real_escape_string($files[$i]->n) . "'
						and conflict = 0
					");
				$dpmod_nonc_count = $dpmod_nonc_count + mysqli_affected_rows($conn);
				
				mysqli_query($conn, "
					update t_file_index
					set
						conflict = 0,
						date_previous_modified = date_last_modified
					where
						id_share = " . $id_share . " 
						and id_host = " . $id_host . " 
						and relative_directory = '" . mysqli_real_escape_string($path) . "'
						and filename = '" . mysqli_real_escape_string($files[$i]->n) . "'
						and conflict = 1
					");
				$dpmod_nonc_c_count = $dpmod_nonc_c_count + mysqli_affected_rows($conn);
			}
			
			mysqli_query($conn, "
				update t_file_index
				set
					date_last_modified = '" . mysqli_real_escape_string($files[$i]->m) . "',
					excluded = " . mysqli_real_escape_string($files[$i]->e) . ",
					notfound = 0
				
				where
					id_share = " . $id_share . " 
					and id_host = " . $id_host . " 
					and relative_directory = '" . mysqli_real_escape_string($path) . "'
					and filename = '" . mysqli_real_escape_string($files[$i]->n) . "'
				");
			
			$modifiedcount = $modifiedcount + mysqli_affected_rows($conn);
			
			// remove from dbfiles list 
			unset($dbfiles[$j]);
			$dbfiles = array_values($dbfiles);
			
			break;
		}
	}
	
	// not found, insert
	if($file_found == 0){
		
		mysqli_query($conn, "
			insert into t_file_index
			(
				relative_directory,
				filename,
				date_last_modified,
				date_previous_modified,
				notfound,
				conflict,
				excluded,
			
				id_share,
				id_host
			)
			values
			(
				'" . mysqli_real_escape_string($path) . "',
				'" . mysqli_real_escape_string($files[$i]->n) . "',
				'" . mysqli_real_escape_string($files[$i]->m) . "',
				'" . mysqli_real_escape_string($files[$i]->m) . "',
				0,
				0,
				" . mysqli_real_escape_string($files[$i]->e) . ",
			
				" . $id_share . ",
				" . $id_host . " 
			)
			");
			
		$insertcount++;
	}
	
}

/*
// set as conflicting where t_file date modified is already greater than current index
mysqli_query($conn, "
	update t_file_index fi
	join t_file f on f.relative_directory = fi.relative_directory and f.filename = fi.filename and f.id_share = fi.id_share and f.active = 1 
		and f.date_last_modified > fi.date_previous_modified
		and f.date_last_modified < fi.date_last_modified
	set
		conflict = 1
	where
		fi.id_share = " . $id_share . " 
	
	");

$conflictcount = $conflictcount + mysqli_affected_rows($conn);
*/

$logging = $logging . ' mod:' . $modifiedcount;
$logging = $logging . ' ins:' . $insertcount;
$logging = $logging . ' confl:' . $conflictcount;

$logging = $logging . ' newconfl:' . $dpmod_newc_count;
$logging = $logging . ' nonconfl:' . $dpmod_nonc_count;
$logging = $logging . ' nonconflnew:' . $dpmod_nonc_c_count;

include 'act_server_fileindex.php';

include 'act_compare_fileindex.php';

$qry = mysqli_query($conn, "
	select
		fa.id_file,
		fa.id_file_action,
		fa.date_action,
		fa.action,
		fa.source,
		fa.target,
		f.date_last_modified as modified

	from t_file_action fa
		left join t_file f on f.id_file = fa.id_file
	
	where
		fa.id_share = " . $id_share . " 
		and fa.id_host = " . $id_host . " 
	
	");
$data = mysql2json($qry);

/*
SELECT 
     CONCAT("[",
          GROUP_CONCAT(
               CONCAT("{username:'",username,"'"),
               CONCAT(",email:'",email),"'}")
          )
     ,"]") 
AS json FROM users;
*/

$returnvalue = array('data' => $data, 'logging' => $logging);


?>