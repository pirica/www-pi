
<?php

echo '<h1>' . substr($tableeditor['tabledescription'], 0, -1) . (substr($tableeditor['tabledescription'], -1) == 'y' ? 'ie' : substr($tableeditor['tabledescription'], -1)) . 's</h1>';

?>

<?php

if($tableeditor_sql_firstletters != '')
{
	echo '<div class="row te_firstletters">';
	$letter_counter = 0;
	while($firstletters = mysqli_fetch_array($qry_letters))
	{
		if($letter_counter  > 0) echo ' | ';
		echo '<a href="index.php?action=' . $action->getCode() . '&amp;parentid=' . $parentid . '&amp;firstletter=' . $firstletter . '" ' . ($firstletters['first_letter'] == $firstletter ? 'class="selected"' : '') . '>' . $firstletters['first_letter'] . "</a>";
		$letter_counter++;
	}
	echo '</div>';
}

?>


<?php
if($tableeditor['enable_search'] == 1)
	{
	?>
		<div class="row spacer">
			<form method="get" action="">
				<input type="hidden" name="action" value="<?= $action->getCode() ?>" />
				<input type="hidden" name="parentid" value="<?= $parentid ?>" />
				<input type="hidden" name="firstletter" value="<?= $firstletter ?>"/>
				
				Search:
				<input type="text" name="searchvalue" value="<?= $searchvalue ?>" />
				<input type="submit" value="Search" />
			</form>
		</div>
	<?php
	}
	
if($tableeditor['enable_create'] == 1)
{
?>
	<div class="row spacer">
		<p>
			<a class="btn btn-primary" href="index.php?action=<?= $action->getCode() ?>&amp;mode=edit&amp;id=-1&amp;parentid=<?= $parentid ?>&amp;firstletter=<?= $firstletter ?>">
				<span class="glyphicon glyphicon-plus"></span>
				Add new entry
			</a>
		</p>
	</div>
<?php
}
?>

<div class="row">
	<table class="table">
		<thead>
			<tr>
				<th>ID</th>
				<?php
				mysqli_data_seek($qry_tableeditor_fields, 0);
				while($tableeditor_field = mysqli_fetch_array($qry_tableeditor_fields))
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
		while($item = mysqli_fetch_array($qry_overview))
		{
		?>
			<tr>
				<td><?= $item[$tableeditor['tableid']] ?></td>
				
				<?php
				mysqli_data_seek($qry_tableeditor_fields, 0);
				while($tableeditor_field = mysqli_fetch_array($qry_tableeditor_fields))
				{
					if($tableeditor_field['show_in_overview'] == 1)
					{
						echo '<td>';
						switch($tableeditor_field['fieldtype'])
						{
							case 'button':
								echo '<a class="btn btn-primary" href="' . $tableeditor_field['url'] . '&amp;parentId=' . $item[$tableeditor_field['fieldname']] . '&amp;firstletter=' . $firstletter . '">' . ($tableeditor_field['label'] == '' ? $item[$tableeditor_field['fielddescription']] : $tableeditor_field['label']) . '</a>';
								break;
							
							case 'url':
								echo '<a href="' . $tableeditor_field['url'] . '&amp;parentId=' . $item[$tableeditor_field['fieldname']] . '&amp;firstletter=' . $firstletter . '">' . ($tableeditor_field['label'] == '' ? $item[$tableeditor_field['url']] : $tableeditor_field['label']) . '</a>';
								break;
							
							default:
								echo $item[$tableeditor_field['fielddescription']];
						}
						echo '</td>';
					}
				}
				?>
				
				<?php
				if($tableeditor['enable_edit'] == 1)
				{
				?>
					<td>
						<a class="btn btn-primary" href="index.php?action=<?= $action->getCode() ?>&amp;mode=edit&amp;id=<?= $item[$tableeditor['tableid']] ?>&amp;parentid=<?= ($tableeditor['parentid'] == '' ? -1 : $item[$tableeditor['parentid']]) ?>&amp;firstletter=<?= $firstletter ?>">
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
						<a class="btn btn-danger btn-delete-grab" href="index.php?action=<?= $action->getCode() ?>&amp;mode=delete&amp;id=<?= $item[$tableeditor['tableid']] ?>&amp;parentid=<?= ($tableeditor['parentid'] == '' ? -1 : $item[$tableeditor['parentid']]) ?>&amp;firstletter=<?= $firstletter ?>" data-toggle="modal" data-target="#myModal">
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
</div>

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
