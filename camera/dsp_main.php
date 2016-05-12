
<h1>Camera tracking</h1>

<div class="row">
	
	<div class="col-xs-6 col-md-3">
		<div class="thumbnail">
			<!--img data-src="holder.js/300x300" alt="..."-->
			<div class="caption">
				<h3>Show captures</h3>
				<p>Show all captured images/videos</p>
				<p><a href="?action=view" class="btn btn-primary" role="button">Show</a></p>
			</div>
		</div>
	</div>
	
	<div class="col-xs-6 col-md-3">
		<div class="thumbnail">
			<!--img data-src="holder.js/300x300" alt="..."-->
			<div class="caption">
				<h3>Show archived</h3>
				<p>Show all archived images/videos</p>
				<p><a href="?action=archive" class="btn btn-primary" role="button">Show</a></p>
			</div>
		</div>
	</div>
	
</div>


<div class="row">
	<div class="col-xs-6 col-md-3">
		<div class="thumbnail">
			<!--img data-src="holder.js/300x300" alt="..."-->
			<div class="caption">
				<h3>View all cameras</h3>
				<p>Current camera view (all)</p>
				<p>
					<a href="?action=camera" class="btn btn-primary" role="button">View all</a>
				</p>
			</div>
		</div>
	</div>
	
	<?php
	for ($i = 0; $i < $cameracount; $i++) {
	?>
		<div class="col-xs-6 col-md-3">
			<div class="thumbnail">
				<!--img data-src="holder.js/300x300" alt="..."-->
				<div class="caption">
					<h3>View camera &quot;<?= $cameras[$i]['description'] ?>&quot;</h3>
					<p>Current camera view for <?= $cameras[$i]['description'] ?></p>
					<p>
						<a href="?action=camera&id_camera=<?= $cameras[$i]['id_camera'] ?>" class="btn btn-primary" role="button">View</a>
					</p>
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
				<h3>Edit cameras</h3>
				<p>Create and edit cameras</p>
				<p>
					<a href="?action=editcameras" class="btn btn-primary" role="button">Edit</a>
				</p>
			</div>
		</div>
	</div>
</div>
