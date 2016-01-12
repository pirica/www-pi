
<h1>Camera tracking</h1>

<div class="row">
	
	<div class="col-xs-6 col-md-3">
		<div class="thumbnail">
			<!--img data-src="holder.js/300x300" alt="..."-->
			<div class="caption">
				<h3>Playlists</h3>
				<p>Show all playlists</p>
				<p><a href="?action=playlists" class="btn btn-primary" role="button">Show</a></p>
			</div>
		</div>
	</div>
	
	<?php
	while($playlist = mysql_fetch_array($qry_playlists)){
	?>
		<div class="col-xs-6 col-md-3">
			<div class="thumbnail">
				<!--img data-src="holder.js/300x300" alt="..."-->
				<div class="caption">
					<h3><?= $playlist['name'] ?></h3>
					<p>
						<?= $playlist['songcount'] ?> songs, <br>
						totalling <?= secondsToTimeRange($playlist['duration']) ?>
					</p>
					<p><a href="?action=playlist&amp;playlistId=<?= $playlist['id'] ?>" class="btn btn-primary" role="button">Select</a></p>
				</div>
			</div>
		</div>
	<?php
	}
	?>
</div>


<div class="row">
	
	<div class="col-xs-6 col-md-3">
		<div class="thumbnail">
			<!--img data-src="holder.js/300x300" alt="..."-->
			<div class="caption">
				<h3>Songs</h3>
				<p>Show and search all songs</p>
				<p><a href="?action=songs" class="btn btn-primary" role="button">Show</a></p>
			</div>
		</div>
	</div>
	
</div>

