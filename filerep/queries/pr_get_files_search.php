<?php

$qry_files_subdirs = mysql_query("
	
	# sub directories under current dir
	select
		3 as sort,
		'sub' as infocode,
		-1 as id_file,
		d.active,
		1 as is_directory,
		case when d.date_last_checked is null then 1 else 0 end as indexing,
		
		case when d.date_last_checked is not null then 1 else 0 end as can_reindex,
		0 as can_download,
		0 as can_view,
		
		d.dirname as filename,
		d.relative_directory,
		d.parent_directory,
		'' as filetype,
		ifnull(d.size,0) + ifnull(d.size_sub,0) as size,
		0 as version,
		d.date_last_modified,
		'' as date_deleted,
		'' as date_last_checked,
		
		'glyphicon-folder-close' as glyphicon,
		'fa-folder-o' as fontawesome
	
	from t_directory d
	
	where
		d.id_share = " . $id_share . "
		and d.relative_directory like '%" . $search . "%'
	
	", $conn);
		
	

$qry_files = mysql_query("
	
	# files under current dir
	select
		4 as sort,
		'' as infocode,
		f.id_file,
		f.active,
		0 as is_directory,
		0 as indexing,
		
		0 as can_reindex,
		1 as can_download,
		1 as can_view,
		
		f.filename,
		f.relative_directory,
		SUBSTRING_INDEX(f.filename, '.', -1) as filetype,
		f.size,
		f.version,
		f.date_last_modified,
		f.date_deleted,
		f.date_last_checked,
		
		fi.glyphicon,
		#ifnull(fi.fontawesome, 'fa-file-o') as fontawesome
		ifnull(fi.fontawesome, '') as fontawesome,
		
		f.rename_to,
		f.move_to
	
	from t_file f
	left join t_file_icon fi on fi.extension = SUBSTRING_INDEX(f.filename, '.', -1)
	
	where
		f.id_share = " . $id_share . "
		and (f.relative_directory like '%" . $search . "%' or f.filename like '%" . $search . "%')
		
	order by
		sort asc,
		is_directory desc,
		filename asc
		
	", $conn);
	
?>