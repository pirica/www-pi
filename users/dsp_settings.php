<?php
$settingsdata_count = count($settingsdata);
?>

<h1>Settings</h1>

<ul class="nav nav-tabs">
	<?php
		for ($i=0; $i<$settingsdata_count; $i++) {
			?>
				<li <?php if($i == 0){ ?> class="active" <?php } ?>><a href="#app<?= $settingsdata[$i][0]['id_app'] ?>" data-toggle="tab"><?= $settingsdata[$i][0]['appname'] ?></a></li>
			<?php
		}
	?>
</ul>


<div class="tab-content">
	<?php
		for ($i=0; $i<$settingsdata_count; $i++) {
			/*
			$id_app,
			$appname,
			
			$id_setting,
			$code,
			$value,
			$description,
			$editable,
			$edittype,
			$extra,
			$category,
			$tooltip
			*/
			?>
				<div class="tab-pane <?php if($i == 0){ ?>active<?php } ?>" id="app<?= $settingsdata[$i][0]['id_app'] ?>">
					<div class="row clearfix">
						<div class="col-md-6 column">
							<form role="form" class="form-horizontal settings-form">
								<?php
									$settingsdata_app_count = count($settingsdata[$i]);
									for ($j=0; $j<$settingsdata_app_count; $j++) {
										/*
											edittype:
												text, string,
													email,
													password,
													size, filesize,
													
												int, integer,
												float, double,
												bool, boolean, bit, checkbox, check,
												select, 
												
											extra:
										*/
										
										$label = $settingsdata[$i][$j]['description'];
										if($label == ''){
											$label = $settingsdata[$i][$j]['code'];
										}
										
										switch($settingsdata[$i][$j]['edittype']){
										
											case 'select':
												?>
													<div class="form-group">
														<label class="col-sm-6 control-label" for="setting<?= $settingsdata[$i][$j]['id_setting'] ?>"><?= $label ?></label>
														<div class="col-sm-6">
															<select id="setting<?= $settingsdata[$i][$j]['id_setting'] ?>" class="form-control" 
																data-code="<?= $settingsdata[$i][$j]['code'] ?>" 
																data-edittype="<?= $settingsdata[$i][$j]['edittype'] ?>">
																<?php
																	$extra = json_decode($settingsdata[$i][$j]['extra']);
																	$options_count = count($extra['options']);
																	for ($o=0; $o<$options_count; $o++) {
																		echo '<option value="' . $extra['options'][$o]['code'] . '" ' . ($extra['options'][$o]['code'] == $settingsdata[$i][$j]['value'] ? 'selected' : '') . '>' . $extra['options'][$o]['value'] . '</option>';
																	}
																?>
															</select>
														</div>
													</div>
													
												<?php
												break;
											
											case 'bool':
											case 'boolean':
											case 'bit':
											case 'checkbox':
											case 'check':
												?>
													<div class="form-group">
														<div class="col-sm-offset-2 col-sm-10">
															<div class="checkbox">
																<label>
																	<input id="setting<?= $settingsdata[$i][$j]['id_setting'] ?>" type="checkbox" 
																		data-code="<?= $settingsdata[$i][$j]['code'] ?>" 
																		data-edittype="<?= $settingsdata[$i][$j]['edittype'] ?>" 
																		<?php if($settingsdata[$i][$j]['value'] == 1) { ?>checked<?php } ?>> 
																	<?= $label ?>
																</label>
															</div>
														</div>
													</div>
												<?php
												break;
											
											case 'size':
											case 'filesize':
												?>
													<div class="form-group">
														<label class="col-sm-6 control-label" for="setting<?= $settingsdata[$i][$j]['id_setting'] ?>"><?= $label ?></label>
														<div class="col-sm-6">
															<input id="setting<?= $settingsdata[$i][$j]['id_setting'] ?>" type="text" class="form-control" 
																data-code="<?= $settingsdata[$i][$j]['code'] ?>" 
																data-edittype="<?= $settingsdata[$i][$j]['edittype'] ?>" 
																value="<?= str_replace(' ', '', str_replace('B', '', strtoupper(formatFileSize($settingsdata[$i][$j]['value'],0)))) ?>">
														</div>
													</div>
													
												<?php
												break;
											
											case 'int':
											case 'integer':
											//...
											default:
												?>
													<div class="form-group">
														<label class="col-sm-6 control-label" for="setting<?= $settingsdata[$i][$j]['id_setting'] ?>"><?= $label ?></label>
														<div class="col-sm-6">
															<input id="setting<?= $settingsdata[$i][$j]['id_setting'] ?>" type="text" class="form-control" 
																data-code="<?= $settingsdata[$i][$j]['code'] ?>" 
																data-edittype="<?= $settingsdata[$i][$j]['edittype'] ?>" 
																value="<?= $settingsdata[$i][$j]['value'] ?>">
														</div>
													</div>
													
												<?php
										}
									}
								?>
							</form>
						</div>
					</div>
				</div>
			<?php
		}
	?>
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