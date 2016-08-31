<?php

/*

set @db = 'router';
set @table = 't_category';

select 'insert into users.t_tableeditor (id_app, tablename, description, tableid, action, use_active_flag, enable_create, enable_edit, enable_delete) values ( appid'
union
select
	concat(', ''' , t.table_name , ''', ''' , replace(t.table_name,'_',' ') , ''', ')
from  information_schema.tables t
where t.table_name = @table
and t.table_schema = @db
union
select
	concat('''' , c.column_name , ''', ''action''', ',')
from  information_schema.tables t
join information_schema.columns c on c.table_schema = t.table_schema and c.table_name = t.table_name and c.extra = 'auto_increment'
where t.table_name = @table
and t.table_schema = @db
union
select
	case when ifnull(c.column_name,'') = '' then 0 else 1 end
from  information_schema.tables t
left join information_schema.columns c on c.table_schema = t.table_schema and c.table_name = t.table_name and c.column_name = 'active'
where t.table_name = @table
and t.table_schema = @db
union
select ', 1, 1, 1)'
;


select concat('insert into users.t_tableeditor_field (id_tableeditor, fieldname, fieldtype, maxlength, id_tableeditor_lookup, sort_order, required, show_in_overview, show_in_editor) values (',
ifnull(e.id_tableeditor,-1), ',',
'''', c.column_name, ''',',
'''', c.data_type, ''',',
case when c.data_type = 'varchar' then c.CHARACTER_MAXIMUM_LENGTH else 'null' end, ',',
'null', ',',
c.ORDINAL_POSITION, ',',
'0,1,1);'
)
from  information_schema.tables t
join information_schema.columns c on c.table_schema = t.table_schema and c.table_name = t.table_name
left join users.t_tableeditor e on e.tablename = t.table_name
where t.table_name = @table
and t.table_schema = @db
and c.column_name <> 'active'
and c.extra <> 'auto_increment'
;



select
	*
from  information_schema.tables t
join information_schema.columns c on c.table_schema = t.table_schema and c.table_name = t.table_name
where t.table_name = 't_host'


*/

$qry_tableeditor = mysql_query("
	
	select
		te.id_tableeditor,
		te.id_app,
		te.tablename,
		te.tableid,
		te.action,
		
		te.use_active_flag,
		te.enable_create,
		te.enable_edit,
		te.enable_delete,
		
		a.database,
		
		ifnull(nullif(te.description, ''), te.tablename) as tabledescription
		
	from t_tableeditor te
		join t_app a on a.id_app = te.id_app
		
	where
		te.id_tableeditor = " . $action->getEditorId() . "
		and te.active = 1
		
	", $conn_users);
	
$tableeditor = mysql_fetch_array($qry_tableeditor);

$tableeditor_fields_overview = ($tableeditor['tablename'] == '' ? '' : $tableeditor['tablename'] . ".") . $tableeditor['tableid'];
$tableeditor_fields_entry = $tableeditor['tableid'];

$tableeditor_sql_lookups = '';
$tableeditor_sql_orderby = '';
$tableeditor_sql_orderby_fields = array();

$qry_tableeditor_fields = mysql_query("
	
	select
		tef.id_tableeditor_field,
		tef.id_tableeditor,
		tef.fieldname,
		tef.fieldtype,
		tef.maxlength,
		tef.sort_order,
		tef.tooltip,
		tef.required,
		tef.show_in_overview,
		tef.show_in_editor,
		tef.sorting_sortorder,
		
		ifnull(nullif(tel.description, ''), ifnull(nullif(tef.description, ''), replace(tef.fieldname, '_', ' '))) as fielddescription,
		
		tel.id_tableeditor_lookup,
		tel.description as lookup_description,
		tel.tablename as lookup_tablename,
		tel.idfield as lookup_idfield,
		tel.labelfield as lookup_labelfield,
		tel.cache as lookup_cache,
		'' as lookup_data
		
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
			$tableeditor_fields_overview .= ',' . $tableeditor_field['lookup_tablename'] . "." . $tableeditor_field['lookup_labelfield'] . " as `" . $tableeditor_field['fielddescription'] . "`";
			
			$tableeditor_sql_lookups .= " left join " . ($tableeditor['database'] == '' ? '' : $tableeditor['database'] . ".") . $tableeditor_field['lookup_tablename'] . " on " . ($tableeditor['database'] == '' ? '' : $tableeditor['database'] . ".") . $tableeditor_field['lookup_tablename'] . "." . $tableeditor_field['lookup_idfield'] . " = " . ($tableeditor['database'] == '' ? '' : $tableeditor['database'] . ".") . $tableeditor['tablename'] . "." . $tableeditor_field['fieldname'];
			
		}
		else
		{
			$tableeditor_fields_overview .= ',' . ($tableeditor['tablename'] == '' ? '' : $tableeditor['tablename'] . ".") . $tableeditor_field['fieldname'] . " as `" . $tableeditor_field['fielddescription'] . "`";
		}
	}
	
	if($tableeditor_field['sorting_sortorder'] != '' && $tableeditor_field['sorting_sortorder'] != 0)
	{
		$tableeditor_sql_orderby_fields[] = array(
			'order' => abs($tableeditor_field['sorting_sortorder']),
			'field' => $tableeditor_field['fieldname'],
			'direction' => $tableeditor_field['sorting_sortorder'] > 0 ? 'asc' : 'desc'
		);
	}
	
	$tableeditor_fields_entry .= ',' . $tableeditor_field['fieldname'];
}

function cmp($a, $b)
{
    return strcmp($a['order'], $b['order']);
}
$tableeditor_sql_orderby_fields_len = count($tableeditor_sql_orderby_fields);
if($tableeditor_sql_orderby_fields_len > 0)
{
	usort($tableeditor_sql_orderby_fields, "cmp");
	
	for($i = 0; $i < $tableeditor_sql_orderby_fields_len; $i++)
	{
		if($tableeditor_sql_orderby == '')
		{
			$tableeditor_sql_orderby .= 'order by';
		}
		else {
			$tableeditor_sql_orderby .= ',';
		}
		$tableeditor_sql_orderby .= ' ' . ($tableeditor['tablename'] == '' ? '' : $tableeditor['tablename'] . ".") . $tableeditor_sql_orderby_fields[$i]['field'] . ' ' . $tableeditor_sql_orderby_fields[$i]['direction'];
		
	}
}

if($mode == 'save')
{
	mysql_data_seek($qry_tableeditor_fields, 0);
	
	if($id > 0)
	{
		$qry_update = "";
		
		while($tableeditor_field = mysql_fetch_array($qry_tableeditor_fields))
		{
			if($tableeditor_field['show_in_editor'] == 1)
			{
				$qry_update .= ($qry_update == '' ? '' : ',');
				$qry_update .= $tableeditor_field['fieldname'] . " = '" . mysql_real_escape_string($_POST['tef_' . $tableeditor_field['fieldname']]) . "'";
			}
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
			if($tableeditor_field['show_in_editor'] == 1)
			{
				$qry_insert_fields .= ($qry_insert_fields == '' ? '' : ',');
				$qry_insert_values .= ($qry_insert_values == '' ? '' : ',');
				
				$qry_insert_fields .= $tableeditor_field['fieldname'];
				$qry_insert_values .= "'" . mysql_real_escape_string($_POST['tef_' . $tableeditor_field['fieldname']]) . "'";
			}
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


if($mode == 'dodelete' && $tableeditor['enable_delete'] == 1)
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
		" . $tableeditor_sql_orderby . "
		";
		
	$qry_overview = mysql_query($sql, $conn_users);
	
	if($qry_overview === false)
	{
		echo '<!--' . $sql . '-->';
	}
}


?>