
<?php

echo '<h1>Delete feed</h1>';

?>

<form method="post" action="index.php?action=do_delfeed&amp;id_feed=<?=$id_feed?>">
	<input type="hidden" name="action" value="do_delfeed"/>
	<input type="hidden" name="id_feed" value="<?=$id_feed?>"/>
	

		Delete feed "<?=$feed_title?>"?
		
		
		<div class="form-group">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			 
			<!--label class="control-label" for="singlebutton">Save</label-->
			<button id="singlebutton" name="singlebutton" class="btn btn-primary" type="submit" value="Confirm">Confirm</button>
		</div>


</form>
