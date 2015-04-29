
<?php

echo '<h1>Delete grabber</h1>';

?>

<form method="post" action="index.php?action=do_delgrab&amp;id_grab=<?=$id_grab?>">
	<input type="hidden" name="action" value="do_delgrab"/>
	<input type="hidden" name="id_grab" value="<?=$id_grab?>"/>
	

		Delete grabber "<?=$grab_description?>"?
		
		
		<div class="form-group">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			 
			<!--label class="control-label" for="singlebutton">Save</label-->
			<button id="singlebutton" name="singlebutton" class="btn btn-primary" type="submit" value="Confirm">Confirm</button>
		</div>


</form>
