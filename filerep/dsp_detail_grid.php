
<?php

	//include 'dsp_detail_grid_pager.php';
	
	$dircontent = '/';
	$str_currentdir = '';
	$dirparts = explode('/', $currentdir['relative_directory']); //  /a/b/c/
	$dirparts_count = count($dirparts);
	for($i=1; $i<$dirparts_count-2; $i++){ // first and last item excluded, because empty anyway; second-to-last item also excluded, is current dir name
		if($dirparts[$i] != ''){
			$dircontent .= $dirparts[$i] . '/';
			$str_currentdir = $str_currentdir . '<a href="?action=details&amp;id_share='. $id_share .'&amp;all=' . $show_all . '&amp;dir='. urlencode($dircontent) .'">'. $dirparts[$i] .'/</a>';// . "\r\n\t\t";
		}
	}
?>

<h2>
	Current directory: 
	<span class="details-dirlist">
		<a href="?action=details&amp;id_share=<?= $id_share ?>&amp;all=<?= $show_all ?>&amp;dir=/">/</a><?= $str_currentdir ?><?= $currentdir['filename'] ?>
	</span>
</h2>


<div class="row">
	<form method="get" action="?action=search">
		<input type="hidden" name="action" value="search">
		<input type="hidden" name="id_share" value="<?= $id_share ?>">
		<input type="hidden" name="dir" value="<?= $currentdir['relative_directory'] ?>">
		
		<div class="col-md-7">
			<input id="search" name="search" placeholder="" class="form-control" type="text" value="<?=$search ?>">
		</div>
		
		<div class="col-md-2">
			<button type="submit" class="btn btn-primary"><span class="fa fa-search"></span> Search</button>
		</div>
	</form>
	
	<div class="col-md-1"></div>
	
	
	<form method="get" action="?action=<?= $action->getCode() ?>">
		<input type="hidden" name="action" value="<?= $action->getCode() ?>">
		<input type="hidden" name="id_share" value="<?= $id_share ?>">
		<input type="hidden" name="dir" value="<?= $currentdir['relative_directory'] ?>">
		
		<div class="col-md-2">
			<div class="form-group">
				<input type="checkbox" id="filter_all" name="all" value="1" <?= ($show_all == 1 ? 'checked="checked"' : '') ?>/>
				<label for="filter_all">Also show deleted files</label>
			</div>
		</div>
	</form>
	
</div>


<p>
	<a href="index.php?action=upload&amp;id_share=<?= $id_share ?>&amp;dir=<?= urlencode($currentdir['relative_directory']) ?>"><i class="fa fa-lg fa-upload pull-right" title="Upload"></i></a>
	<!-- data-toggle="modal" data-target="#myModal"-->
	
	<!--<a href="#">
		<span class="fa-stack fa-lg pull-right">
			<i class="fa fa-lg fa-folder-o" title="Upload"></i>
			<i class="fa fa-plus" title="Upload"></i>
		</span>
	</a>-->
	
	<a href="index.php?action=create_dir&amp;id_share=<?= $id_share ?>&amp;dir=<?= urlencode($currentdir['relative_directory']) ?>"><i class="fa fa-lg fa-plus pull-right" title="Create new directory"></i></a>
	
</p>

<table class="table">
	<thead>
		<tr>
			<th width="50">&nbsp;</th>
			<th sortfield="filename">
				Filename
				<span class="thsort thsort-filename-asc glyphicon glyphicon-arrow-up <?php if(!($sort == 'filename' && $sortorder == 'asc')){ ?>hidden<?php } ?>"></span>
				<span class="thsort thsort-filename-desc glyphicon glyphicon-arrow-down <?php if(!($sort == 'filename' && $sortorder == 'desc')){ ?>hidden<?php } ?>"></span>
			</th>
			<th width="80" sortfield="size">
				Size
				<span class="thsort thsort-size-asc glyphicon glyphicon-arrow-up <?php if(!($sort == 'size' && $sortorder == 'asc')){ ?>hidden<?php } ?>"></span>
				<span class="thsort thsort-size-desc glyphicon glyphicon-arrow-down <?php if(!($sort == 'size' && $sortorder == 'desc')){ ?>hidden<?php } ?>"></span>
			</th>
			<th width="160" sortfield="date_last_modified">
				Last modified
				<span class="thsort thsort-date_last_modified-asc glyphicon glyphicon-arrow-up <?php if(!($sort == 'date_last_modified' && $sortorder == 'asc')){ ?>hidden<?php } ?>"></span>
				<span class="thsort thsort-date_last_modified-desc glyphicon glyphicon-arrow-down <?php if(!($sort == 'date_last_modified' && $sortorder == 'desc')){ ?>hidden<?php } ?>"></span>
			</th>
			<th width="120">&nbsp;</th>
		</tr>
	</thead>
	
	<tbody>
	<?php 
	$i = 0;
	
	$i++;
	?>
	<tr class="row-dir <?=($currentdir['active'] == 1 ? 'row-dir-active' : 'row-dir-inactive') ?>">
		<td><?php
			if($currentdir['fontawesome'] != ''){
				echo '<span class="fa ' . $currentdir['fontawesome'] . '"></span>';
			}
			else if($currentdir['glyphicon'] != ''){
				echo '<span class="glyphicon ' . $currentdir['glyphicon'] . '"></span>';
			}
			else {
				echo '&nbsp;';
			}
		?></td>
		<td><?php
			echo '<a href="?action=details&amp;id_share=' . $id_share . '&amp;all=' . $show_all . '&amp;dir=' . urlencode($currentdir['relative_directory']) . '">.</a>';
			
		?></td>
		<td><?= formatFileSize($currentdir['size']) ?></td>
		<td><?= $currentdir['date_last_modified'] ?></td>
		<td>
			<?php
				if($currentdir['indexing'] == 1){
					echo '<span class="fa fa-bolt red" title="Indexing..."></span>';
				}
				else if($currentdir['can_reindex'] == 1){
					echo '<a href="#" class="act-dir-reindex hover" data-dir="'. urlencode($currentdir['relative_directory']) .'"><span class="fa fa-bolt green" title="Force reindexing of directory"></span></a>';
				}
				
				// download
				echo '<span class="fa"></span>';
				// view
				echo '<span class="fa"></span>';
				// delete
				echo '<span class="fa"></span>';
				// undelete
				echo '<span class="fa"></span>';
			?>
		</td>
	</tr>
	<?php
	
	if(isset($parentdir['relative_directory'])){
		$i++;
		?>
		<tr class="row-dir <?=($parentdir['active'] == 1 ? 'row-dir-active' : 'row-dir-inactive') ?>">
			<td><?php
				if($parentdir['fontawesome'] != ''){
					echo '<span class="fa ' . $parentdir['fontawesome'] . '"></span>';
				}
				else if($parentdir['glyphicon'] != ''){
					echo '<span class="glyphicon ' . $parentdir['glyphicon'] . '"></span>';
				}
				else {
					echo '&nbsp;';
				}
			?></td>
			<td><?php
				echo '<a href="?action=details&amp;id_share=' . $id_share . '&amp;all=' . $show_all . '&amp;dir=' . urlencode($parentdir['relative_directory']) . '">..</a>';
				
			?></td>
			<td><?= formatFileSize($parentdir['size']) ?></td>
			<td><?= $parentdir['date_last_modified'] ?></td>
			<td>
				<?php
					if($parentdir['indexing'] == 1){
						echo '<span class="fa fa-bolt red" title="Indexing..."></span>';
					}
					else if($parentdir['can_reindex'] == 1){
						echo '<a href="#" class="act-dir-reindex hover" data-dir="'. urlencode($parentdir['relative_directory']) .'"><span class="fa fa-bolt green" title="Force reindexing of directory"></span></a>';
					}
					
					// download
					echo '<span class="fa"></span>';
					// view
					echo '<span class="fa"></span>';
					// delete
					echo '<span class="fa"></span>';
					// undelete
					echo '<span class="fa"></span>';
				?>
			</td>
		</tr>
		<?php
	}
	
	while($file = mysqli_fetch_array($qry_files_subdirs)){ 
		if(($show_all == 0 && $file['active'] == 1) || $show_all == 1){
			$i++;
			?>
			<tr class="<?=($file['is_directory'] == 1 ? 'row-dir' : 'row-file') . ' ' . ($file['is_directory'] == 1 ? 'row-dir' : 'row-file') . '-' . ($file['active'] == 1 ? 'active' : 'inactive') ?>">
				<td><?php
					if($file['fontawesome'] != ''){
						echo '<span class="fa ' . $file['fontawesome'] . '"></span>';
					}
					else if($file['glyphicon'] != ''){
						echo '<span class="glyphicon ' . $file['glyphicon'] . '"></span>';
					}
					else {
						echo '&nbsp;';
					}
				?></td>
				<td><?php
					if($file['is_directory'] == 1){
						echo '<a href="?action=details&amp;id_share=' . $id_share . '&amp;all=' . $show_all . '&amp;dir=' . urlencode($file['relative_directory']) . '">' . $file['filename'] . '</a>';
					}
					else {
						echo $file['filename'];
					}
				?></td>
				<td><?= formatFileSize($file['size']) ?></td>
				<td><?= $file['date_last_modified'] ?></td>
				<td>
					<?php
						if($file['indexing'] == 1){
							echo '<span class="fa fa-bolt red" title="Indexing..."></span>';
						}
						else if($file['can_reindex'] == 1){
							echo '<a href="#" class="act-dir-reindex hover" data-dir="'. urlencode($file['relative_directory']) .'"><span class="fa fa-bolt green" title="Force reindexing of directory"></span></a>';
						}
						
						// download
						echo '<span class="fa"></span>';
						// view
						echo '<span class="fa"></span>';
						// delete
						echo '<span class="fa"></span>';
						// undelete
						echo '<span class="fa"></span>';
					?>
				</td>
			</tr>
			<?php
		}		
	}
	while($file = mysqli_fetch_array($qry_files)){ 
		if(($show_all == 0 && $file['active'] == 1) || $show_all == 1){
			$i++;
			?>
			<tr class="<?=($file['is_directory'] == 1 ? 'row-dir' : 'row-file') . ' ' . ($file['is_directory'] == 1 ? 'row-dir' : 'row-file') . '-' . ($file['active'] == 1 ? 'active' : 'inactive') ?>" data-file="<?= $file['id_file'] ?>">
				<td>
					<?php
						if($file['fontawesome'] != ''){
							echo '<span class="fa ' . $file['fontawesome'] . '"></span>';
						}
						else if($file['glyphicon'] != ''){
							echo '<span class="glyphicon ' . $file['glyphicon'] . '"></span>';
						}
						else {
							echo '&nbsp;';
						}
					?>
				</td>
				<td class="filename">
					<?php
						if($file['is_directory'] == 1){
							echo '<a href="?action=details&amp;id_share=' . $id_share . '&amp;all=' . $show_all . '&amp;dir=' . urlencode($file['relative_directory'] . $file['filename']) . '">' . $file['filename'] . '</a>';
						}
						else {
							$rename_to = '';
							if($file['rename_to'] == ''){
								echo '<span class="orig">' . $file['filename'] . '</span>';
								$rename_to = $file['filename'];
							}
							else {
								echo '<span class="orig renamed">' . $file['filename'] . '</span>';
								$rename_to = $file['rename_to'];
								
							}
							echo '<span class="rename_to">' . $file['rename_to'] . '</span>';
							echo '<input type="text" class="rename_to hidden" value="' . $rename_to . '" />';
						}
					?>
				</td>
				<td><?= formatFileSize($file['size']) ?></td>
				<td><?= $file['date_last_modified'] ?></td>
				<td>
					<?php
						echo '<span class="fa"></span>';
						/*if($file['indexing'] == 1){
							echo '<span class="fa fa-bolt red" title="Indexing..."></span>';
						}
						else if($file['can_reindex'] == 1){
							echo '<a href="#" class="act-dir-reindex hover" data-dir="'. $file['relative_directory'] .'"><span class="fa fa-bolt green" title="Force reindexing of directory"></span></a>';
						}*/
						
						if($file['can_download'] == 1){
							echo '<a href="?action=downloadfile&amp;id_file=' . $file['id_file'] . '"><span class="fa fa-download" title="Download"></span></a>';
						}
						else {
							echo '<span class="fa"></span>';
						}
						
						if($file['can_view'] == 1){
							echo '<a href="?action=viewfile&amp;id_file=' . $file['id_file'] . '"><span class="fa fa-eye" title="View"></span></a>';
						}
						else {
							echo '<span class="fa"></span>';
						}
						
						if($file['can_delete'] == 1){
							echo '<a href="?action=deletefile&amp;id_file=' . $file['id_file'] . '&amp;active=' . $file['active'] . '&amp;id_share=' . $id_share . '&amp;all=' . $show_all . '&amp;dir=' . urlencode($file['relative_directory']) . '"><span class="fa fa-trash-o" title="Delete"></span></a>';
						}
						else {
							echo '<span class="fa"></span>';
						}
						
						if($file['can_delete'] == 1 && $file['active'] == 0){
							echo '<a href="?action=undeletefile&amp;id_file=' . $file['id_file'] . '&amp;id_share=' . $id_share . '&amp;all=' . $show_all . '&amp;dir=' . urlencode($file['relative_directory']) . '"><span class="fa-stack fa-stack-small" title="Undelete"><i class="fa fa-trash-o fa-stack-1x"></i><i class="fa fa-ban fa-stack-1x"></i></span></a>';
						}
						else {
							echo '<span class="fa"></span>';
						}
						
					?>
				</td>
			</tr>
			<?php
		}
	}
	?>
	</tbody>
	
</table>

<?php
	//include 'dsp_detail_grid_pager.php';
?>
