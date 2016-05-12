<?php

$qry_tableeditor = mysql_query("
	
	select
		te.id_tableeditor,
		te.id_app,
		te.tablename,
		te.tableid,
		te.action,
		a.database
		
	from t_tableeditor te
		join t_app a on a.id_app = te.id_app
		
	where
		te.id_tableeditor = " . $action->getEditorId() . "
		and te.active = 1
		
	", $conn_users);
	
$tableeditor = mysql_fetch_array($qry_tableeditor);
$tableeditor_fields_overview = $tableeditor['tableid'];

$qry_tableeditor_fields = mysql_query("
	
	select
		tef.id_tableeditor_field,
		tef.id_tableeditor,
		tef.fieldname,
		tef.fieldtype,
		tef.maxlength,
		tef.sort_order,
		tef.required,
		tef.show_in_overview
		
	from t_tableeditor_field tef
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
		$tableeditor_fields_overview = $tableeditor_fields_overview . ',' . $tableeditor_field['fieldname'];
	}
	
}

if($mode == 'edit')
{
	
}
else 
{
	$qry_overview = mysql_query("
		select
			" . $tableeditor_fields_overview . "
		from " . ($tableeditor['database'] == '' ? '' : $tableeditor['database'] . ".") . $tableeditor['tablename'] . "
		
		", $conn_users);
}

?>