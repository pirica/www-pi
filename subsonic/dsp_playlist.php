
<h1>Playlist "<?= $playlist['name'] ?>"</h1>

<table class="table">
	<thead>
		<tr>
			<th>Index</th>
			<th>Description</th>
			<th>Album</th>
			<th>Length</th>
			
			<th>Add</th>
			<th>Move</th>
			<th>Remove</th>
		</tr>
	</thead>
	
	<tbody>
	<?php 
	while($entries = mysqli_fetch_array($qry_playlist_entries)){
		
		?>
		<tr>
			<td><?= $entries['songIndex'] ?></td>
			<td><?= $entries['label'] ?></td>
			<td><?= $entries['album'] ?></td>
			<td><?= secondsToTimeRange($entries['duration']) ?></td>
			
			<td>
				<a class="btn btn-danger btn-xs" href="index.php?action=add_playlist_entry&amp;songId=<?=$entries['songId'] ?>" data-toggle="modal" data-target="#myModal">
					<span class="glyphicon glyphicon-plus"></span>
					Add
				</a>
			</td>
			<td>
				<a class="btn btn-danger btn-xs" href="index.php?action=move_playlist_entry&amp;oldPlaylistId=<?=$playlistId ?>&amp;songId=<?=$entries['songId'] ?>" data-toggle="modal" data-target="#myModal">
					<span class="glyphicon glyphicon-plus"></span>
					Move
				</a>
			</td>
			<td>
				<a class="btn btn-danger btn-xs btn-delete-grab" href="index.php?action=do_delete_playlist_entry&amp;playlistId=<?= $entries['playlistId'] ?>&amp;songId=<?=$entries['songId'] ?>">
					<span class="glyphicon glyphicon-remove"></span>
					Remove
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
