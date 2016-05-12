
<?php

echo '<h1>Delete entry</h1>';

?>

<form method="post" action="index.php?action=<?= $action->getCode() ?>&amp;mode=dodelete&amp;id=<?= $id ?>">
	<input type="hidden" name="action" value="<?= $action->getCode() ?>"/>
	<input type="hidden" name="mode" value="dodelete"/>
	<input type="hidden" name="id" value="<?= $id ?>"/>

		Delete entry?
		
		
		<div class="form-group">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			 
			<!--label class="control-label" for="singlebutton">Save</label-->
			<button id="singlebutton" name="singlebutton" class="btn btn-primary" type="submit" value="Confirm">Confirm</button>
		</div>


</form>
