<?php
if(!$for_action){
?>
	<h1>Playlists</h1>

	<p>
		<a class="btn btn-primary" href="index.php?action=add_playlist">
			<span class="glyphicon glyphicon-plus"></span>
			Add new playlist
		</a>
		
	</p>
<?php
}
?>

<table class="table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Description</th>
			<th>Length</th>
			
			<th>Owner</th>
			<th>Public</th>
			<?php
			if(!$for_action){
			?>
				<th>Remove</th>
			<?php
			}
			?>
		</tr>
	</thead>
	
	<tbody>
	<?php 
	while($playlist = mysql_fetch_array($qry_playlists)){
		
		?>
		<tr>
			<td><?= $playlist['id'] ?></td>
			<td>
				<?php
				if(!$for_action){
				?>
					<a href="index.php?action=playlist&amp;playlistId=<?= $playlist['id'] ?>"><?= $playlist['name'] ?></a>
				<?php
				}
				else {
				?>
					<a class="add-playlist-entry" data-playlistId="<?= $playlist['id'] ?>" href="index.php?action=do_add_playlist_entry&amp;playlistId=<?= $playlist['id'] ?>&amp;songId=<?= $songId ?>"><?= $playlist['name'] ?></a>
				<?php
				}
				?>
			</td>
			<td><?= secondsToTimeRange($playlist['duration']) ?></td>
			
			<td><?= $playlist['owner'] ?></td>
			<td><?= $playlist['public'] ?></td>
			
			<?php
			if(!$for_action){
			?>
				<td>
					<a class="btn btn-danger btn-xs btn-delete-grab" href="index.php?action=del_playlist&amp;playlistId=<?= $playlist['id'] ?>"><!-- data-toggle="modal" data-target="#myModal"-->
						<span class="glyphicon glyphicon-remove"></span>
						Remove
					</a>
				</td>
			<?php
			}
			?>
		</tr>
	<?php 
	}
	?>
	</tbody>
	
</table>

<?php
if(!$for_action){
?>
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
<?php
}
?>
