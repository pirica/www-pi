
<h1>Queue</h1>

<table class="table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Date added</th>
			<th>Url</th>
			<th>Status</th>
			
			<th>Filename</th>
			<th>Directory</th>
			<th>Playlist</th>
			
			<th>Confirm</th>
			<th>Decline</th>
		</tr>
	</thead>
	
	<tbody>
	<?php 
	while($queue = mysqli_fetch_array($qry_queue)){ 
		?>
		<tr>
			<td><?=$queue['id_queue'] ?></td>
			<td><?=$queue['date_added'] ?></td>
			<td><?=$queue['url'] ?></td>
			<td>
				<?php
				switch($queue['status']){
					case 'N': echo 'New'; break;
					case 'Y': echo 'Youtube-dl'; break;
					case 'F': echo 'Regular'; break;
				}
				?>
			</td>
			
			<td>
				<input type="text" id="queue_filename_<?=$queue['id_queue'] ?>" class="queue_filename" value="<?=$queue['filename'] ?>" data-id_queue="<?=$queue['id_queue'] ?>" />
			</td>
			<td>
				<input type="text" id="queue_directory_<?=$queue['id_queue'] ?>" class="queue_directory" value="<?=$queue['directory'] ?>" data-id_queue="<?=$queue['id_queue'] ?>" />
			</td>
			<td>
				<select id="queue_playlist_<?=$queue['id_queue'] ?>" class="queue_playlist" value="<?=$queue['directory'] ?>" data-id_queue="<?=$queue['id_queue'] ?>">
					<option value="0"></option>
					<?php 
					while($playlist = mysqli_fetch_array($qry_playlists)){
						?>
						<option value="<?= $playlist['id'] ?>" <?= ($playlist['id'] == $queue['playlistId'] ? 'selected="selected"' : '') ?>><?= $playlist['name'] ?></option>
						<?php 
					}
					?>
				</select>
			</td>
			
			<td>
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
			</td>
			<td>
				<a class="btn btn-danger btn-xs btn-delete-grab" href="index.php?action=edit_queue&sub=decline&amp;id_queue=<?=$queue['id_queue'] ?>">
					<span class="fa fa-ban"></span>
					Decline
				</a>
			</td>
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
