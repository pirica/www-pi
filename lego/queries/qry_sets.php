<?php
	
$qry_sets = mysqli_query($conn, "

	select
		substring_index(s.set_num,'-',1) as set_num,
		s.name,
		s.year,
		s.theme_id,
		s.num_parts
		
	from sets s
	where
		s.theme_id = '" . mysqli_real_escape_string($conn, $themeId) . "'
		
	");
	
$qry_set = mysqli_query($conn, "

	select
		substring_index(s.set_num,'-',1) as set_num,
		s.name,
		s.year,
		s.theme_id,
		s.num_parts
		
	from sets s
	where
		substring_index(s.set_num,'-',1) = '" . mysqli_real_escape_string($conn, $setId) . "'
		
	");
	
$set = mysqli_fetch_array($qry_set);

?>