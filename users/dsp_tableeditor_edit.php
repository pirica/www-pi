
<?php

echo '<h1>';
if($id > 0)
{
	echo 'Edit entry for ';
	echo strtolower(substr($tableeditor['tabledescription'], 0, -1) . (substr($tableeditor['tabledescription'], -1) == 'y' ? 'ie' : substr($tableeditor['tabledescription'], -1)));
	echo 's';
}
else 
{
	echo 'Create new ';
	echo strtolower($tableeditor['tabledescription']);
	echo ' entry';
}
echo '</h1>';

?>

<?php
if(
	($tableeditor['enable_create'] == 1 && $id <= 0)
	||
	($tableeditor['enable_edit'] == 1 && $id > 0)
)
{
?>

	<form id="frmTableeditor" class="form-horizontal" method="post" action="index.php?action=<?= $action->getCode() ?>&amp;mode=save&amp;id=<?= $id ?>">
		<input type="hidden" name="action" value="<?= $action->getCode() ?>"/>
		<input type="hidden" name="mode" value="save"/>
		<input type="hidden" name="id" value="<?= $id ?>"/>
		
		<?php
		mysql_data_seek($qry_tableeditor_fields, 0);
		while($tableeditor_field = mysql_fetch_array($qry_tableeditor_fields))
		{
			if($tableeditor_field['show_in_editor'] == 1)
			{
				if($tableeditor_field['id_tableeditor_lookup'] > 0)
				{
					?>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="tef_<?= $tableeditor_field['fieldname'] ?>"><?= $tableeditor_field['fielddescription'] ?></label>
							<div class="col-sm-3">
								<select id="tef_<?= $tableeditor_field['fieldname'] ?>" class="form-control">
									<?php
										$sql = "
											select 
												" . $tableeditor_field['lookup_idfield'] . " as id,
												" . $tableeditor_field['lookup_labelfield'] . " as description
											from " . ($tableeditor['database'] == '' ? '' : $tableeditor['database'] . ".") . $tableeditor_field['lookup_tablename'] . "
											order by " . $tableeditor_field['lookup_labelfield'] . "
											";
										//echo '<!--' . $sql . '-->';
										$tableeditor_lookup_data = mysql_query($sql, $conn_users);
										
										while($lookupdata = mysql_fetch_array($tableeditor_lookup_data))
										{
											echo '<option value="' . $lookupdata['id'] . '" ' . ($lookupdata['id'] == $tableentry[$tableeditor_field['fieldname']] ? 'selected="selected"' : '') . '>' . $lookupdata['description'] . '</option>';
										}
									?>
								</select>
							</div>
							<?php
								if($tableeditor_field['tooltip'] != ''){
									echo '<p class="help-block col-sm-11 col-sm-offset-1">' . $tableeditor_field['tooltip'] . '</p>';
								}
							?>
						</div>
					<?php
				}
				else {
					
					$input_type = 'text';
					
					switch($tableeditor_field['fieldtype'])
					{
						case 'bool':
						case 'boolean':
						case 'bit':
						case 'checkbox':
						case 'check':
							?>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="tef_<?= $tableeditor_field['fieldname'] ?>"><?= $tableeditor_field['fielddescription'] ?></label>
									<div class="col-sm-3">
										<input id="tef_<?= $tableeditor_field['fieldname'] ?>" name="tef_<?= $tableeditor_field['fieldname'] ?>" type="checkbox" 
											<?php if($tableentry[$tableeditor_field['fieldname']] == 1) { ?>checked<?php } ?>>
									</div>
									<?php
										if($tableeditor_field['tooltip'] != ''){
											echo '<p class="help-block col-sm-11 col-sm-offset-1">' . $tableeditor_field['tooltip'] . '</p>';
										}
									?>
								</div>
							<?php
							break;
							
						case 'int':
						case 'integer':
							$input_type = 'number';
						default:
							?>
							
							<div class="form-group">
								<label class="col-sm-3 control-label" for="tef_<?= $tableeditor_field['fieldname'] ?>"><?= $tableeditor_field['fielddescription'] ?></label>
								<?php
									if($tableeditor_field['fieldtype'] == 'int' || $tableeditor_field['fieldtype'] == 'integer'){
										$colsize = 3;
									}
									else {
										$colsize = 9;
									}
								?>
								<div class="col-sm-<?= $colsize ?>">
									<input id="tef_<?= $tableeditor_field['fieldname'] ?>" name="tef_<?= $tableeditor_field['fieldname'] ?>" type="<?= $input_type ?>" class="form-control" 
										value="<?= $tableentry[$tableeditor_field['fieldname']] ?>">
								</div>
								<?php
									if($tableeditor_field['tooltip'] != ''){
										echo '<p class="help-block col-sm-11 col-sm-offset-1">' . $tableeditor_field['tooltip'] . '</p>';
									}
								?>
							</div>
							<?php
					}
				}
			}
		}
		?>
		
		
		<div class="form-group">
			<!--label class="control-label" for="btnSaveTableeditor">Save</label-->
			<button id="btnSaveTableeditor" name="btnSaveTableeditor" class="btn btn-primary" type="submit" value="Save">Save</button>
		</div>

		
		<div class="form-group">
		<?php
		/*if($error == 1){
			echo '<div class="alert alert-danger">Some required fields are incorrect</div>';
		}*/
		?>
		</div>
		
		
	</form>

<?php
}
?>
