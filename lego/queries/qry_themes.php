<?php


$qry_theme = mysqli_query($conn, "

	SELECT qo.id, qo.parent_id, qo.level, t.name
	FROM (
		SELECT _id AS id, parent_id,
			@cl := @cl + 1 AS level
		FROM (
			SELECT @r AS _id,
				(
					SELECT @r := parent_id
					FROM themes
					WHERE id = _id
				) AS parent_id,
				@l := @l + 1 AS level
			FROM (
				SELECT
					@r := '" . mysqli_real_escape_string($conn, $themeId) . "',
					@l := 0,
					@cl := 0
			) vars, themes h
			WHERE @r <> 0
			ORDER BY
				level DESC
		) qi
	) qo
	join themes t on t.id = qo.id
	order by
		qo.level
	
	");
	

$qry_themes = mysqli_query($conn, "

	select
		t.id,
		t.name,
		t.parent_id,
		s2.sets
		
	from themes t
		join (
			select
				theme_id,
				substring_index(group_concat(substring_index(set_num,'-',1) order by rand()), ',', 6) as sets
			from sets
			group by theme_id
		) s2 on s2.theme_id = t.id
	
	where
		t.parent_id = '" . mysqli_real_escape_string($conn, $themeId) . "'
		
	order by
		t.name
		
	");
	
?>