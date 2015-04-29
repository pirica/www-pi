
<?php

echo '<h1>Delete counter</h1>';

?>

<form method="post" action="index.php?action=do_delgrabcounter&amp;id_grab=<?=$id_grab?>&amp;id_grab_counter=<?=$id_grab_counter?>">
	<input type="hidden" name="action" value="do_delgrabcounter"/>
	<input type="hidden" name="id_grab" value="<?=$id_grab?>"/>
	<input type="hidden" name="id_grab_counter" value="<?=$id_grab_counter?>"/>
	

		Delete counter "<?=$counter_field?>" on grabber "<?=$grab_description?>"?
		
		
		<div class="form-group">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			 
			<!--label class="control-label" for="singlebutton">Save</label-->
			<button id="singlebutton" name="singlebutton" class="btn btn-primary" type="submit" value="Confirm">Confirm</button>
		</div>


</form>
