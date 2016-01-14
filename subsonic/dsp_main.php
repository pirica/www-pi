
<h1>Subsonic</h1>

<div class="row">
	
	<div class="col-xs-4 col-md-2">
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
		<div class="col-xs-4 col-md-2">
			<div class="thumbnail">
				<!--img data-src="holder.js/300x300" alt="..."-->
				<div class="caption">
					<h3><?= $playlist['name'] ?></h3>
					<p>
						<?php
						if($playlist['songcount'] == 0){
						?>
							0 songs
						<?php
						}
						else if($playlist['songcount'] == 1){
						?>
							1 song, <br>
							<?= secondsToTimeRange($playlist['duration']) ?>
						<?php
						}
						else {
						?>
							<?= $playlist['songcount'] ?> songs, <br>
							<?= secondsToTimeRange($playlist['duration']) ?>
						<?php
						}
						?>
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
				<h3>Search songs</h3>
				<p>Show and search all songs</p>
				<p><a href="?action=songs_search" class="btn btn-primary" role="button">Show</a></p>
			</div>
		</div>
	</div>
	
	<div class="col-xs-6 col-md-3">
		<div class="thumbnail">
			<!--img data-src="holder.js/300x300" alt="..."-->
			<div class="caption">
				<h3>Recent songs</h3>
				<p>Show the recently added songs</p>
				<p><a href="?action=songs_recent" class="btn btn-primary" role="button">Show</a></p>
			</div>
		</div>
	</div>
	
</div>

