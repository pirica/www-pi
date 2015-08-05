

<?php

	$dircontent = '/';
	$str_currentdir = '';
	$dirparts = explode('/', $currentdir['relative_directory']); //  /a/b/c/
	$dirparts_count = count($dirparts);
	for($i=1; $i<$dirparts_count-2; $i++){ // first and last item excluded, because empty anyway; second-to-last item also excluded, is current dir name
		if($dirparts[$i] != ''){
			$dircontent .= $dirparts[$i] . '/';
			$str_currentdir = $str_currentdir . '<a href="?action=details&amp;id_share='. $id_share .'&amp;all=' . $show_all . '&amp;dir='. $dircontent .'">'. $dirparts[$i] .'/</a>';// . "\r\n\t\t";
		}
	}
?>

<h1>Create new directory</h1>

<h2>
	Current directory: 
	<span class="details-dirlist">
		<a href="?action=details&amp;id_share=<?= $id_share ?>&amp;all=<?= $show_all ?>&amp;dir=/">/</a><?= $str_currentdir ?><?= $currentdir['filename'] ?>
	</span>
</h2>


<form id="frm-edit" class="form-horizontal" method="post" action="index.php?action=do_create_dir&amp;id_share=<?= $id_share ?>&amp;dir=<?= $dir ?>">
	<input type="hidden" name="action" value="do_create_dir"/>
	<input type="hidden" name="id_share" value="<?=$id_share?>"/>
	<input type="hidden" name="dir" value="<?=$dir?>"/>

	
	<div class="form-group">
		<label for="newdir">Directory name</label>
		<input id="newdir" name="newdir" placeholder="" class="form-control" type="text" value="">
	</div>
	
	
	<div class="form-group">
		<!--label class="control-label" for="singlebutton">Create</label-->
		<button id="singlebutton" name="singlebutton" class="btn btn-primary" type="submit" value="Create">Create</button>
	</div>


	
	<div class="form-group">
	<?php
	/*if($error == 1){
		echo '<div class="alert alert-danger">Some required fields are incorrect</div>';
	}*/
	?>
	</div>
	


</form>
   
