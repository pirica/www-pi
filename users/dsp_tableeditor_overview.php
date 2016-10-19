
<?php

echo '<h1>' . substr($tableeditor['tabledescription'], 0, -1) . (substr($tableeditor['tabledescription'], -1) == 'y' ? 'ie' : substr($tableeditor['tabledescription'], -1)) . 's</h1>';

?>

<?php
if($tableeditor['enable_search'] == 1)
	{
	?>
		<form method="get" action="">
			Search:
			<input type="text" name="searchvalue" value="<?= $searchvalue ?>" />
			<input type="submit" value="Search" />
		</form>
	<?php
	}
	
if($tableeditor['enable_create'] == 1)
{
?>
	<p>
		<a class="btn btn-primary" href="index.php?action=<?= $action->getCode() ?>&amp;mode=edit&amp;id=-1">
			<span class="glyphicon glyphicon-plus"></span>
			Add new entry
		</a>
	</p>
<?php
}
?>

<table class="table">
	<thead>
		<tr>
			<th>ID</th>
			<?php
			mysql_data_seek($qry_tableeditor_fields, 0);
			while($tableeditor_field = mysql_fetch_array($qry_tableeditor_fields))
			{
				if($tableeditor_field['show_in_overview'] == 1)
				{
					echo '<th>' . $tableeditor_field['fielddescription'] . '</th>';
				}
			}
			?>
			<?php
			if($tableeditor['enable_edit'] == 1)
			{
			?>
				<th>Edit</th>
			<?php
			}
			if($tableeditor['enable_delete'] == 1)
			{
			?>
				<th>Delete</th>
			<?php
			}
			?>
		</tr>
	</thead>
	
	<tbody>
	<?php 
	while($item = mysql_fetch_array($qry_overview))
	{
	?>
		<tr>
			<td><?= $item[$tableeditor['tableid']] ?></td>
			
			<?php
			mysql_data_seek($qry_tableeditor_fields, 0);
			while($tableeditor_field = mysql_fetch_array($qry_tableeditor_fields))
			{
				if($tableeditor_field['show_in_overview'] == 1)
				{
					echo '<td>' . $item[$tableeditor_field['fielddescription']] . '</td>';
				}
			}
			?>
			
			<?php
			if($tableeditor['enable_edit'] == 1)
			{
			?>
				<td>
					<a class="btn btn-primary" href="index.php?action=<?= $action->getCode() ?>&amp;mode=edit&amp;id=<?= $item[$tableeditor['tableid']] ?>">
						<span class="glyphicon glyphicon-edit"></span>
						Edit
					</a>
				</td>
			<?php
			}
			if($tableeditor['enable_delete'] == 1)
			{
			?>
				<td>
					<a class="btn btn-danger btn-delete-grab" href="index.php?action=<?= $action->getCode() ?>&amp;mode=delete&amp;id=<?= $item[$tableeditor['tableid']] ?>" data-toggle="modal" data-target="#myModal">
						<span class="glyphicon glyphicon-remove"></span>
						Delete
					</a>
				</td>
			<?php
			}
			?>
		</tr>
		
	<?php 
	}
	?>
	</tbody>
	
</table>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
