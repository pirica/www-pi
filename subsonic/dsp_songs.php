
<h1><?= $app->getTitle() ?></h1>

	<form method="get" action="?action=<?= $action->getCode() ?>">
		<input type="hidden" name="action" value="<?= $action->getCode() ?>">
		<input type="hidden" name="playlistId" value="<?= $playlistId ?>">
		
		
		
		<div class="row">
			<div class="col-md-7">
				<input id="search" name="search" placeholder="" class="form-control" type="text" value="<?=$search ?>">
			</div>
			
			<div class="col-md-2">
				<button type="submit" class="btn btn-primary"><span class="fa fa-search"></span> Search</button>
			</div>
			
			<div class="col-md-1"></div>
			
			<div class="col-md-2">
				<!--<div class="form-group">
					<input type="checkbox" id="filter_all" name="all" value="1" <?= ''//($show_all == 1 ? 'checked="checked"' : '') ?>/>
					<label for="filter_all">Also show deleted files</label>
				</div>
			</div>-->
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
