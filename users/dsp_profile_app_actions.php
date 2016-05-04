<?php
$profiledata_count = count($profiledata);
?>

<h1>Profile - Apps access</h1>

<ul class="nav nav-tabs">
	<?php
		for ($i=0; $i<$profiledata_count; $i++) {
			?>
				<li <?php if($i == 0){ ?> class="active" <?php } ?>><a href="#profile<?= $profiledata[$i][0]['id_profile'] ?>" data-toggle="tab"><?= $profiledata[$i][0]['profilename'] ?></a></li>
			<?php
		}
	?>
</ul>


<div class="tab-content">
	<?php
		for ($i=0; $i<$profiledata_count; $i++) {
			?>
				<div class="tab-pane <?php if($i == 0){ ?>active<?php } ?>" id="profile<?= $profiledata[$i][0]['id_profile'] ?>">
					<form role="form" class="form-horizontal profile-app-actions-form">
						<div class="row clearfix">
							<div class="col-sm-2"><h4>Action</h4></div>
							<div class="col-sm-2"><h4>Allowed?</h4></div>
						</div>
						
						<?php
							$profiledata_app_count = count($profiledata[$i]);
							
							for ($j=0; $j<$profiledata_app_count; $j++) {
								?>
								<div class="row clearfix">
									<div class="col-sm-2">
										<?= $profiledata[$i][$j]['appcode'] ?>
										<?= ($profiledata[$i][$j]['page_title'] == '' ? '' : '(' . $profiledata[$i][$j]['page_title'] . ')') ?>
									</div>
									<div class="col-sm-2">
										<input id="allowed<?= $profiledata[$i][$j]['id_profile_app_action'] ?>" type="checkbox" 
											data-id_app_action="<?= $profiledata[$i][$j]['id_app_action'] ?>" 
											data-field="allowed" 
											<?php if($profiledata[$i][$j]['allowed'] == 1 || $profiledata[$i][$j]['full_access'] == 1) { ?>checked<?php } ?>
											<?php if($profiledata[$i][$j]['full_access'] == 1) { ?>disabled<?php } ?>
											>
									</div>
								</div>
								<?php
							}
						?>
					</form>
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
