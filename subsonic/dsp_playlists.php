
<h1>Playlists</h1>

<p>
	<a class="btn btn-primary" href="index.php?action=setgrab&amp;id_grab=-1">
		<span class="glyphicon glyphicon-plus"></span>
		Add new playlist
	</a>
	
</p>

<table class="table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Description</th>
			<th>Length</th>
			
			<th>Owner</th>
			<th>Public</th>
			
			<th>Remove</th>
		</tr>
	</thead>
	
	<tbody>
	<?php 
	while($playlist = mysql_fetch_array($qry_playlists)){
		
		?>
		<tr>
			<td><?= $playlist['id'] ?></td>
			<td><?= $playlist['name'] ?></td>
			<td><?= secondsToTimeRange($playlist['duration']) ?></td>
			
			<td><?= $playlist['owner'] ?></td>
			<td><?= $playlist['public'] ?></td>
			
			<td>
				<a class="btn btn-danger btn-xs btn-delete-grab" href="index.php?action=delete_playlist&amp;playlistId=<?= $playlist['playlistId'] ?>" data-toggle="modal" data-target="#myModal">
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
