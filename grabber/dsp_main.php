
<h1>Overview</h1>

<p>
	<a class="btn btn-primary" href="index.php?action=setgrab&amp;id_grab=-1">
		<span class="glyphicon glyphicon-plus"></span>
		Add new grab
	</a>
</p>

<table class="table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Description</th>
			<th># Counters</th>
			<th># Files</th>
			<!--th># Done</th-->
			<th># Remaining</th>
			<th>Status</th>
			<th>Run</th>
			<th>Edit</th>
			<th>Delete</th>
		</tr>
	</thead>
	
	<tbody>
	<?php 
	while($grab = mysql_fetch_array($qry_grabs)){ 
		
		$files_ok = $grab['files_done'] + $grab['files_notfound'] + $grab['files_empty'] + $grab['files_excluded'];
		$files_fx = $grab['files_exist'];
		$files_err = $grab['files_error'] + $grab['files_timeout'];
				
		?>
		<tr>
			<td><?=$grab['id_grab'] ?></td>
			<td><a href="index.php?action=details&amp;id_grab=<?=$grab['id_grab'] ?>"><?=$grab['description'] ?></a></td>
			<td><?=$grab['counters_total'] ?></td>
			<td><?=$grab['files_total'] ?></td>
			<!--td><?=($files_ok + $files_fx + $files_err) ?></td-->
			<td><?=$grab['files_todo'] ?></td>
			<td>
				<?php
				if($grab['running'] == 1 || $grab['files_building'] == 1){
					if($grab['running'] == 1){
						echo 'Running';
					}
					if($grab['running'] == 1 && $grab['files_building'] == 1){
						echo ', ';
					}
					if($grab['files_building'] == 1){
						echo 'Files building';
					}
				}
				else if($grab['enabled'] == 1){
					echo 'Enabled';
				}
				/*else if($grab['done']){
					echo 'Done';
				}*/
				/*else if($grab['files_todo'] != $grab['files_total']){
					echo 'Not running';
				}*/
				else {
					echo 'Not started';
				}
				?>
			</td>
			<td>
				<?php if($grab['enabled'] == 1){ ?>
					<a class="btn btn-warning btn-xs" href="index.php?action=stop&amp;id_grab=<?=$grab['id_grab'] ?>" title="Stop">
						<span class="glyphicon glyphicon-stop"></span>
						Stop
					</a>
				<?php 
				} else { 
				?>
					<a class="btn btn-success btn-xs" href="index.php?action=start&amp;id_grab=<?=$grab['id_grab'] ?>" title="Start">
						<span class="glyphicon glyphicon-play"></span>
						Start
					</a>
				<?php } ?>
			
			</td>
			<td>
				<a class="btn btn-primary btn-xs" href="index.php?action=setgrab&amp;id_grab=<?=$grab['id_grab'] ?>">
					<span class="glyphicon glyphicon-edit"></span>
					Edit
				</a>
			</td>
			<td>
				<a class="btn btn-danger btn-xs btn-delete-grab" href="index.php?action=delgrab&amp;id_grab=<?=$grab['id_grab'] ?>" data-toggle="modal" data-target="#myModal">
					<span class="glyphicon glyphicon-remove"></span>
					Delete
				</a>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="2">
				<?php
				if($grab['files_total'] > 0){
					$pb_ok_width = 100 * $files_ok / $grab['files_total'];
					$pb_fx_width = 100 * $files_fx / $grab['files_total'];
					$pb_err_width = 100 * $files_err / $grab['files_total'];
				}
				else {
					$pb_ok_width = 0;
					$pb_fx_width = 0;
					$pb_err_width = 0;
				}
				$alttxt = '' . $files_ok . ' OK, ' . $files_fx . ' already existed, ' . $files_err . ' in error';
				?>
				<div class="progress" alt="<?=$alttxt ?>" title="<?=$alttxt ?>">
					<div style="width: <?=($pb_ok_width) ?>%;" class="progress-bar progress-bar-success" alt="<?=$alttxt ?>" title="<?=$alttxt ?>"></div>
					<div style="width: <?=($pb_fx_width) ?>%;" class="progress-bar progress-bar-warning" alt="<?=$alttxt ?>" title="<?=$alttxt ?>"></div>
					<div style="width: <?=($pb_err_width) ?>%;" class="progress-bar progress-bar-danger" alt="<?=$alttxt ?>" title="<?=$alttxt ?>"></div>
				</div>
				
				<!--
				<div class="progress">
					<div class="progress-bar progress-bar-success" style="width: 35%;"><span class="sr-only">35% Complete (success)</span></div>
					<div class="progress-bar progress-bar-warning" style="width: 20%;"><span class="sr-only">20% Complete (success)</span></div>
					<div class="progress-bar progress-bar-danger" style="width: 10%;"><span class="sr-only">10% Complete (success)</span></div>
				</div>
				-->
			</td>
			<td colspan="6" <?php if($grab['files_total'] == $files_ok + $files_fx + $files_err){ ?>class="greyed"<?php } ?>>
				ETA: <?=$grab['eta_rounded']?>
			</td>
		</tr>
		<!--tr>
			<td>&nbsp;</td>
			<td colspan="7">
		</tr-->
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
