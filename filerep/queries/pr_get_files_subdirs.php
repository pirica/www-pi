<?php

$qry_files = mysql_query("
	select
		max(f.active) as active,
		
		#replace(f.relative_directory, SUBSTRING_INDEX(f.relative_directory, '/', -1), '') as relative_directory,
		f.relative_directory, 
		sum(f.size) as size,
		max(f.date_last_modified) as date_last_modified,
		max(f.date_deleted) as date_deleted,
		max(f.date_last_checked) as date_last_checked,
		
		'' as glyphicon
	
	from t_file f
	
	where
		f.id_share = " . $id_share . "
		# and replace(f.relative_directory, f.filename, '') = '" . $dir . "'
		#and replace(f.relative_directory, SUBSTRING_INDEX(f.relative_directory, '/', -1), '') like '" . $dir . "%'
		and f.relative_directory = '" . $dir . "'
		
		#and ROUND (   
		#	(
		#		LENGTH(f.relative_directory)
		#		- LENGTH( REPLACE ( f.relative_directory, '/', '') ) 
		#	) / LENGTH('/')        
		#)
		#=
		#ROUND (   
		#	(
		#		LENGTH('" . $dir . "')
		#		- LENGTH( REPLACE ( '" . $dir . "', '/', '') ) 
		#	) / LENGTH('/')        
		#) + 1
		##AS depth 
	
	group by
		#replace(f.relative_directory, SUBSTRING_INDEX(f.relative_directory, '/', -1), '')
		f.relative_directory
		
	", $conn);
	
?>