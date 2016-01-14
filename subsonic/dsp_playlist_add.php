
<h1>Add a new playlist</h1>

<form id="frm-edit" class="form-horizontal" method="post" action="index.php?action=add_playlist">
	<input type="hidden" name="action" value="add_playlist"/>
	

	<div class="form-group">
		<label for="playlist_description">Description</label>
		<input id="playlist_description" name="playlist_description" placeholder="" class="form-control" type="text" value="<?=$playlist_description?>">
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

