
<ul class="nav nav-tabs">
	<li class="active"><a href="#grabber" data-toggle="tab">Grabber</a></li>
	<li><a href="#counters" data-toggle="tab">Counters</a></li>
	<li class="disabled"><a href="#counterdetail" data-toggle="tab">Counter detail</a></li>
</ul>

<h1>Add a new playlist</h1>

<form id="frm-edit" class="form-horizontal" method="post" action="index.php?action=add_playlist">
	<input type="hidden" name="action" value="add_playlist"/>
	

	<div class="form-group">
		<label for="description">Description</label>
		<input id="description" name="description" placeholder="" class="form-control" type="text" value="<?=$playlist_description?>">
	</div>
	
	
	<div class="form-group">
		<!--label class="control-label" for="singlebutton">Save</label-->
		<button id="singlebutton" name="singlebutton" class="btn btn-primary" type="submit" value="Save">Save</button>
	</div>


	
	<div class="form-group">
	<?php
	if($error == 1){
		echo '<div class="alert alert-danger">Some required fields are incorrect</div>';
	}
	?>
	</div>
	
	
	
</form>

