
<h1><?= $app->getTitle() ?></h1>

	<form method="get" action="?action=<?= $action->getCode() ?>">
		<input type="hidden" name="action" value="<?= $action->getCode() ?>">
		<input type="hidden" name="playlistId" value="<?= $playlistId ?>">
		
		
		
		<div class="row">
			<div class="col-md-6">
				<input id="search" name="search" placeholder="" class="form-control" type="text" value="<?=$search ?>">
			</div>
			
			<div class="col-md-2">
				<button type="submit" class="btn btn-primary"><span class="fa fa-search"></span> Search</button>
			</div>
			
			<div class="col-md-1"></div>
			
			<div class="col-md-1">
				<?php
				if($action->getCode() == 'songs_search'){
				?>
					<div class="form-group">
						<label for="playlist">In playlist</label>
						<select id="playlist" name="playlist" class="form-control">
							<option value="a" <?php echo strtolower($playlist) == 'a' ? 'selected="selected"' : ''; ?>>Any</option>
							<option value="n" <?php echo strtolower($playlist) == 'n' ? 'selected="selected"' : ''; ?>>None</option>
							<option value="in" <?php echo strtolower($playlist) == 'in' ? 'selected="selected"' : ''; ?>>In selected</option>
							<option value="ex" <?php echo strtolower($playlist) == 'ex' ? 'selected="selected"' : ''; ?>>Not in selected</option>
						</select>
					</div>
				<?php
				}
				?>
			</div>
			
			<div class="col-md-2">
				<?php
				if($action->getCode() == 'songs_search'){
				?>
					<div class="form-group">
						<label for="playlistId">&nbsp;</label>
						<select id="playlistId" name="playlistId" class="form-control">
							<?php
							while($playlist = mysqli_fetch_array($qry_playlists)){
							?>
								<option value="<?= $playlist['id'] ?>" <?php echo $playlist['id'] == $playlistId ? 'selected="selected"' : ''; ?>><?= $playlist['name'] ?></option>
							<?php
							}
							?>
						</select>
					</div>
				<?php
				}
				?>
			</div>
			
							<?php
							if($action->getCode() == 'songs_search'){
								while($playlist = mysqli_fetch_array($qry_playlists)){
									
								}
							}
							?>
		</div>
	</form>
	
<?php
include 'dsp_songs_pager.php';
?>

<form id="frmSongs">
	<table class="table">
		<thead>
			<tr>
				<th><input type="checkbox" id="allsongs" value="-1" alt="Check all" title="Check all"></th>
				<th>Description</th>
				<th>Album</th>
				<th>Length</th>
				
				<th>Add</th>
				<th>Remove</th>
			</tr>
		</thead>
		
		<tbody>
		<?php 
		while($song = mysqli_fetch_array($qry_songs)){
			
			?>
			<tr>
				<td><input type="checkbox" name="song" value="<?= $song['songId'] ?>" id="song<?= $song['songId'] ?>"></td>
				<td><label for="song<?= $song['songId'] ?>"><?= $song['label'] ?></label></td>
				<td><?= $song['album'] ?></td>
				<td><?= secondsToTimeRange($song['duration']) ?></td>
				
				<td>
					<a class="btn btn-danger btn-xs" href="index.php?action=add_playlist_entry&amp;songId=<?=$song['songId'] ?>" data-toggle="modal" data-target="#myModal">
						<span class="glyphicon glyphicon-plus"></span>
						Add
					</a>
				</td>
				<td>
					<a class="btn btn-danger btn-xs" href="index.php?action=delete_playlist_entry&amp;songId=<?=$song['songId'] ?>" data-toggle="modal" data-target="#myModal">
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
</form>

<?php
include 'dsp_songs_pager.php';
?>

<div>
	With selected:
	
	<a id="btnAddAll" class="btn btn-danger" href="index.php?action=add_playlist_entry&amp;songId=" data-toggle="modal" data-target="#myModal">
		<span class="glyphicon glyphicon-plus"></span>
		Add to playlist
	</a>

	<a id="btnRemoveAll" class="btn btn-danger" href="index.php?action=delete_playlist_entry&amp;songId=" data-toggle="modal" data-target="#myModal">
		<span class="glyphicon glyphicon-remove"></span>
		Remove from playlist
	</a>
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
