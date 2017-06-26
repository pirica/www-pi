
<h1>Queue</h1>

<div class="row">
	
	<?php 
	while($queue = mysqli_fetch_array($qry_queue)){ 
		?>
		<div class="col-md-12">
			<p>
				ID: <?=$queue['id_queue'] ?>, <?=$queue['date_added'] ?><br>
				URL: <?=$queue['url'] ?>
			</p>
			
			<div class="form-group">
				<label class="col-sm-3 control-label" for="queue_status_<?=$queue['id_queue'] ?>">Status: </label>
				<select id="queue_status_<?=$queue['id_queue'] ?>" name="status" class="form-control col-sm-3 queue_status" data-id_queue="<?=$queue['id_queue'] ?>">
					<option value="E" <?= ($queue['status'] == 'E' ? 'selected="selected"' : '') ?>></option>
					<option value="F" <?= ($queue['status'] == 'F' ? 'selected="selected"' : '') ?>>Regular</option>
					<option value="Y" <?= ($queue['status'] == 'Y' ? 'selected="selected"' : '') ?>>Youtube-dl</option>
				</select>
			</div>
			
			<div class="form-group">
				<label class="col-sm-3 control-label" for="queue_directory_<?=$queue['id_queue'] ?>">File: </label>
				<input type="text" id="queue_directory_<?=$queue['id_queue'] ?>" name="directory" class="form-control col-sm-5 queue_directory" value="<?=$queue['directory'] ?>" data-id_queue="<?=$queue['id_queue'] ?>" />
				<input type="text" id="queue_filename_<?=$queue['id_queue'] ?>" name="filename" class="form-control col-sm-4 queue_filename" value="<?=$queue['filename'] ?>" data-id_queue="<?=$queue['id_queue'] ?>" />
			</div>
			
			<div class="form-group">
				<label class="col-sm-3 control-label" for="queue_playlist_<?=$queue['id_queue'] ?>">Playlist: </label>
				<select id="queue_playlist_<?=$queue['id_queue'] ?>" name="playlistId" class="form-control col-sm-3 queue_playlist" data-id_queue="<?=$queue['id_queue'] ?>">
					<option value="0"></option>
					<?php 
					while($playlist = mysqli_fetch_array($qry_playlists)){
						?>
						<option value="<?= $playlist['id'] ?>" <?= ($playlist['id'] == $queue['playlistId'] ? 'selected="selected"' : '') ?>><?= $playlist['name'] ?></option>
						<?php 
					}
					mysqli_data_seek($qry_playlists, 0);
					?>
				</select>
			</div>
			
			<p>
				<?php
				if($queue['status'] != 'N')
				{
				?>
					<a class="btn btn-primary btn-xs" href="index.php?action=edit_queue&sub=confirm&amp;id_queue=<?=$queue['id_queue'] ?>">
						<span class="fa fa-check"></span>
						Confirm
					</a>
				<?php
				}
				?>
				<a class="btn btn-danger btn-xs btn-delete-grab" href="index.php?action=edit_queue&sub=decline&amp;id_queue=<?=$queue['id_queue'] ?>">
					<span class="fa fa-ban"></span>
					Decline
				</a>
			</p>
		</div>
		
		<hr />
		
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
