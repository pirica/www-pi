<?php

$qry_tableeditor = mysql_query("
	
	select
		te.id_tableeditor,
		te.id_app,
		te.tablename,
		te.tableid,
		te.action,
		te.use_active_flag,
		a.database
		
	from t_tableeditor te
		join t_app a on a.id_app = te.id_app
		
	where
		te.id_tableeditor = " . $action->getEditorId() . "
		and te.active = 1
		
	", $conn_users);
	
$tableeditor = mysql_fetch_array($qry_tableeditor);

$tableeditor_fields_overview = $tableeditor['tableid'];
$tableeditor_fields_entry = $tableeditor['tableid'];

$tableeditor_sql_lookups = '';

$qry_tableeditor_fields = mysql_query("
	
	select
		tef.id_tableeditor_field,
		tef.id_tableeditor,
		tef.fieldname,
		tef.fieldtype,
		tef.maxlength,
		tef.sort_order,
		tef.required,
		tef.show_in_overview,
		
		tel.id_tableeditor_lookup,
		tel.description as lookup_description,
		tel.tablename as lookup_tablename,
		tel.idfield as lookup_idfield,
		tel.labelfield as lookup_labelfield
		
	from t_tableeditor_field tef
		left join t_tableeditor_lookup tel on tel.id_tableeditor_lookup = tef.id_tableeditor_lookup
			and tel.active = 1
	where
		tef.id_tableeditor = " . $action->getEditorId() . "
		and tef.active = 1
		
	order by
		tef.sort_order
	
	", $conn_users);
	
while($tableeditor_field = mysql_fetch_array($qry_tableeditor_fields))
{
	if($tableeditor_field['show_in_overview'] == 1)
	{
		
		if($tableeditor_field['id_tableeditor_lookup'] > 0)
		{
			$tableeditor_fields_overview .= ',' . $tableeditor_field['lookup_tablename'] . "." . $tableeditor_field['lookup_labelfield'] . " as `" . $tableeditor_field['lookup_description'] . "`";
			
			$tableeditor_sql_lookups .= " left join " . ($tableeditor['database'] == '' ? '' : $tableeditor['database'] . ".") . $tableeditor_field['lookup_tablename'] . " on " . ($tableeditor['database'] == '' ? '' : $tableeditor['database'] . ".") . $tableeditor_field['lookup_tablename'] . "." . $tableeditor_field['lookup_idfield'] . " = " . ($tableeditor['database'] == '' ? '' : $tableeditor['database'] . ".") . $tableeditor['tablename'] . "." . $tableeditor_field['fieldname'];
		}
		else
		{
			$tableeditor_fields_overview .= ',' . ($tableeditor['tablename'] == '' ? '' : $tableeditor['tablename'] . ".") . $tableeditor_field['fieldname'];
		}
	}
	$tableeditor_fields_entry .= ',' . $tableeditor_field['fieldname'];
}

if($mode == 'save')
{
	mysql_data_seek($qry_tableeditor_fields, 0);
	
	if($id > 0)
	{
		$qry_update = "";
		
		while($tableeditor_field = mysql_fetch_array($qry_tableeditor_fields))
		{
			$qry_update .= ($qry_update == '' ? '' : ',');
			$qry_update .= $tableeditor_field['fieldname'] . " = '" . mysql_real_escape_string($_POST['tef_' . $tableeditor_field['fieldname']]) . "'";
		}
		
		mysql_query("
			update " . ($tableeditor['database'] == '' ? '' : $tableeditor['database'] . ".") . $tableeditor['tablename'] . "
			set " .
				$qry_update . 
			" where	
				" . $tableeditor['tableid'] . " = " . $id . "
				
			", $conn_users);
			
	}
	else
	{
		$qry_insert_fields = "";
		$qry_insert_values = "";
		
		while($tableeditor_field = mysql_fetch_array($qry_tableeditor_fields))
		{
			$qry_insert_fields .= ($qry_insert_fields == '' ? '' : ',');
			$qry_insert_values .= ($qry_insert_values == '' ? '' : ',');
			
			$qry_insert_fields .= $tableeditor_field['fieldname'];
			$qry_insert_values .= "'" . mysql_real_escape_string($_POST['tef_' . $tableeditor_field['fieldname']]) . "'";
		}
		
		mysql_query("
			insert into " . ($tableeditor['database'] == '' ? '' : $tableeditor['database'] . ".") . $tableeditor['tablename'] . "
			(
				" .	$qry_insert_fields . "
			)
			values
			(
				" .	$qry_insert_values . "
			)
			", $conn_users);
			

	}
	
}


if($mode == 'dodelete')
{
	if($tableeditor['use_active_flag'] == 1)
	{
		mysql_query("
			update " . ($tableeditor['database'] == '' ? '' : $tableeditor['database'] . ".") . $tableeditor['tablename'] . "
			set active = 0
			where	
				" . $tableeditor['tableid'] . " = " . $id . "
			
			", $conn_users);

	}
	else
	{
		mysql_query("
			delete from " . ($tableeditor['database'] == '' ? '' : $tableeditor['database'] . ".") . $tableeditor['tablename'] . "
			where	
				" . $tableeditor['tableid'] . " = " . $id . "
			
			", $conn_users);
	}
}


if($mode == 'edit')
{
	
	if($id > 0)
	{
		$qry_tableeditor_entry = mysql_query("
			select
				" . $tableeditor_fields_entry . "
			from " . ($tableeditor['database'] == '' ? '' : $tableeditor['database'] . ".") . $tableeditor['tablename'] . "
			where	
				" . $tableeditor['tableid'] . " = " . $id . "
			
			", $conn_users);
	}
	else 
	{
		$qry_tableeditor_entry = mysql_query("
			select
				-1 " . str_replace(',', ", '' ", $tableeditor_fields_entry) . "
			
			", $conn_users);
	}
	$tableentry = mysql_fetch_array($qry_tableeditor_entry);
	
}
else 
{
	$sql = "
		select
			" . $tableeditor_fields_overview . "
		from " . ($tableeditor['database'] == '' ? '' : $tableeditor['database'] . ".") . $tableeditor['tablename'] . "
		" . $tableeditor_sql_lookups . "
		" . ($tableeditor['use_active_flag'] == 1 ? 'where ' . $tableeditor['tablename'] . '.active = 1' : '') . "
		
		";
		
	//echo '<!--' . $sql . '-->';
	$qry_overview = mysql_query($sql, $conn_users);
	
}


?>