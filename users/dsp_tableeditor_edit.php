
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
	
	<div class="alertContainer"></div>
	
	<form id="frmTableeditor" class="form-horizontal" method="post" action="index.php?action=<?= $action->getCode() ?>&amp;mode=save&amp;id=<?= $id ?>">
		<input type="hidden" name="action" value="<?= $action->getCode() ?>"/>
		<input type="hidden" name="mode" value="save"/>
		<input type="hidden" name="id" value="<?= $id ?>"/>
		
		<?php
		mysqli_data_seek($qry_tableeditor_fields, 0);
		while($tableeditor_field = mysqli_fetch_array($qry_tableeditor_fields))
		{
			if($tableeditor_field['show_in_editor'] == 1)
			{
				if($tableeditor_field['id_tableeditor_lookup'] > 0)
				{
					?>
						<div class="form-group <?= $tableeditor_field['required'] == 1 ? 'required' : '' ?>">
							<label class="col-sm-3 control-label" for="tef_<?= $tableeditor_field['fieldname'] ?>"><?= $tableeditor_field['fielddescription'] ?></label>
							<div class="col-sm-3">
								<select id="tef_<?= $tableeditor_field['fieldname'] ?>" name="tef_<?= $tableeditor_field['fieldname'] ?>" class="form-control"
									<?= $tableeditor_field['required'] == 1 ? 'required="required"' : '' ?>
								>
									<option value=""></option>
									<?php
										
										$cache_name = "tef_" . $tableeditor_field['lookup_tablename'];
										$array_lookupdata = $cache->get($cache_name);

										if($array_lookupdata == null || $tableeditor_field['lookup_cache'] == 0) {
											
											$sql = "
												select 
													" . $tableeditor_field['lookup_idfield'] . " as id,
													" . $tableeditor_field['lookup_labelfield'] . " as description
												from " . ($tableeditor['database'] == '' ? '' : $tableeditor['database'] . ".") . $tableeditor_field['lookup_tablename'] . "
												order by " . $tableeditor_field['lookup_labelfield'] . "
												";
											//echo '<!--' . $sql . '-->';
											$tableeditor_lookup_data = mysqli_query($conn_users, $sql);
											
											$array_lookupdata = array();
											while($lookupdata = mysqli_fetch_array($tableeditor_lookup_data))
											{
												$array_lookupdata[] = array(
													'id' => $lookupdata['id'],
													'description' => $lookupdata['description']
												);
											}
											
											if($tableeditor_field['lookup_cache'] > 0)
											{
												$cache->set($cache_name, $array_lookupdata, $tableeditor_field['lookup_cache'] * 60);
											}
										}
										
										$array_lookupdata_count = count($array_lookupdata);
										
										for($i=0; $i<$array_lookupdata_count; $i++)
										{
											echo '<option value="' . $array_lookupdata[$i]['id'] . '" ' . ($array_lookupdata[$i]['id'] == $tableentry[$tableeditor_field['fieldname']] ? 'selected="selected"' : '') . '>' . $array_lookupdata[$i]['description'] . '</option>';
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
								<div class="form-group <?= $tableeditor_field['required'] == 1 ? 'required' : '' ?>">
									<label class="col-sm-3 control-label" for="tef_<?= $tableeditor_field['fieldname'] ?>"><?= $tableeditor_field['fielddescription'] ?></label>
									<div class="col-sm-3">
										<input id="tef_<?= $tableeditor_field['fieldname'] ?>" name="tef_<?= $tableeditor_field['fieldname'] ?>" type="checkbox" value="1"
											<?php if($tableentry[$tableeditor_field['fieldname']] == 1) { ?>checked<?php } ?>
											<?= $tableeditor_field['required'] == 1 ? 'required="required"' : '' ?>
										>
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
							
							<div class="form-group <?= $tableeditor_field['required'] == 1 ? 'required' : '' ?>">
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
										value="<?= $tableentry[$tableeditor_field['fieldname']] ?>"
										<?= $tableeditor_field['required'] == 1 ? 'required="required"' : '' ?>
									>
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
