<?php
$actionsdata_count = count($actionsdata);
?>

<h1>Actions</h1>

<ul class="nav nav-tabs">
	<?php
		for ($i=0; $i<$actionsdata_count; $i++) {
			?>
				<li <?php if($i == 0){ ?> class="active" <?php } ?>><a href="#app<?= $actionsdata[$i][0]['id_app'] ?>" data-toggle="tab"><?= $actionsdata[$i][0]['appname'] ?></a></li>
			<?php
		}
	?>
</ul>


<div class="tab-content">
	<?php
		for ($i=0; $i<$actionsdata_count; $i++) {
			/*
			$id_app,
			$appname,
			
			$id_app_action,
			$code,
			$page_title,
			$login_required
			
			*/
			?>
				<div class="tab-pane <?php if($i == 0){ ?>active<?php } ?>" id="app<?= $actionsdata[$i][0]['id_app'] ?>">
					<div class="row clearfix">
						<form role="form" class="form-horizontal actions-form">
							<table border="0" cellpadding="3" cellspacing="0">
								<tr>
									<th>Code</th>
									<th>Page title</th>
									<th>Login required?</th>
								</tr>
								<?php
									$actionsdata_app_count = count($actionsdata[$i]);
									
									for ($j=0; $j<$actionsdata_app_count; $j++) {
										?>
										<tr>
											<td><?= $actionsdata[$i][$j]['code'] ?></td>
											<td>
												<input id="page_title<?= $actionsdata[$i][$j]['id_app_action'] ?>" type="text" 
													data-code="<?= $actionsdata[$i][$j]['code'] ?>" 
													data-field="page_title" 
													value="<?= $actionsdata[$i][$j]['page_title'] ?>">
											</td>
											<td>
												<input id="login_required<?= $actionsdata[$i][$j]['id_app_action'] ?>" type="checkbox" 
													data-code="<?= $actionsdata[$i][$j]['code'] ?>" 
													data-field="login_required" 
													<?php if($actionsdata[$i][$j]['login_required'] == 1) { ?>checked<?php } ?>>
											</td>
										</tr>
										
										<?php
									}
								?>
							</table>
						</form>
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
