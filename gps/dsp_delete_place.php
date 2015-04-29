
<?php

echo '<h1>Delete place</h1>';

?>

<form method="post" action="index.php?action=do_delplace&amp;id_place=<?=$id_place?>">
	<input type="hidden" name="action" value="do_delplace"/>
	<input type="hidden" name="id_place" value="<?=$id_place?>"/>
	

		Delete place "<?=$place_description?>"?
		
		
		<div class="form-group">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			 
			<!--label class="control-label" for="singlebutton">Save</label-->
			<button id="singlebutton" name="singlebutton" class="btn btn-primary" type="submit" value="Confirm">Confirm</button>
		</div>


</form>
