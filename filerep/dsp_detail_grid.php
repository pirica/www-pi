
<?php

	//include 'dsp_detail_grid_pager.php';
?>

<h2>
	Current directory: 
	<span>
		<a href="?action=details&amp;id_share=<?= $id_share ?>&amp;dir=/">/</a>
		<?php
			$dircontent = '/';
			$str_currentdir = '';
			$dirparts = explode('/', $currentdir['relative_directory']); //  /a/b/c/
			$dirparts_count = count($dirparts);
			for($i=1; $i<$dirparts_count-2; $i++){ // first and last item excluded, because empty anyway; second-to-last item also excluded, is current dir name
				if($dirparts[$i] != ''){
					$dircontent .= $dirparts[$i] . '/';
					$str_currentdir = '<a href="?action=details&amp;id_share='. $id_share .'&amp;dir='. $dircontent .'">'. $dirparts[$i] .'/</a> ' . $str_currentdir;
				}
			}
		?>
		<?= $str_currentdir ?>
	</span>
	<?= $currentdir['filename'] ?>
</h2>

<p>
	<form method="get" action="?action=<?= $action->getCode() ?>">
		<input type="hidden" name="action" value="<?= $action->getCode() ?>">
		<input type="hidden" name="id_share" value="<?= $id_share ?>">
		<input type="hidden" name="dir" value="<?= $dir ?>">
		
		<input type="checkbox" id="filter_all" name="all" value="1" <?= ($show_all == 1 ? 'checked="checked"' : '') ?>/>
		<label for="filter_all">Also show deleted files</label>
		
	<form>
</p>

<?php
/*
if($currentdir['relative_directory'] != '/'){
?>
	<p>
		<a href="?action=details&amp;id_share=<?= $id_share ?>&amp;dir=<?= $parentdir['relative_directory'] ?>"><span class="glyphicon glyphicon-chevron-up"></span> <?= $parentdir['filename'] ?></a>
	</p>
<?php
}
*/
?>

<table class="table">
	<thead>
		<tr>
			<th width="50">&nbsp;</th>
			<th sortfield="filename">
				Filename
				<span class="thsort thsort-filename-asc glyphicon glyphicon-arrow-up <?php if(!($sort == 'filename' && $sortorder == 'asc')){ ?>hidden<?php } ?>"></span>
				<span class="thsort thsort-filename-desc glyphicon glyphicon-arrow-down <?php if(!($sort == 'filename' && $sortorder == 'desc')){ ?>hidden<?php } ?>"></span>
			</th>
			<th width="100" sortfield="size">
				Size
				<span class="thsort thsort-size-asc glyphicon glyphicon-arrow-up <?php if(!($sort == 'size' && $sortorder == 'asc')){ ?>hidden<?php } ?>"></span>
				<span class="thsort thsort-size-desc glyphicon glyphicon-arrow-down <?php if(!($sort == 'size' && $sortorder == 'desc')){ ?>hidden<?php } ?>"></span>
			</th>
			<th width="200" sortfield="date_last_modified">
				Last modified
				<span class="thsort thsort-date_last_modified-asc glyphicon glyphicon-arrow-up <?php if(!($sort == 'date_last_modified' && $sortorder == 'asc')){ ?>hidden<?php } ?>"></span>
				<span class="thsort thsort-date_last_modified-desc glyphicon glyphicon-arrow-down <?php if(!($sort == 'date_last_modified' && $sortorder == 'desc')){ ?>hidden<?php } ?>"></span>
			</th>
			<th width="50">&nbsp;</th>
		</tr>
	</thead>
	
	<tbody>
	<?php 
	$i = 0;
	while($file = mysql_fetch_array($qry_files_subdirs)){ 
		if(($show_all == 0 && $file['active'] == 1) || $show_all == 1){
			$i++;
			?>
			<tr class="<?=($file['is_directory'] == 1 ? 'row-dir' : 'row-file') . '-' . ($i % 2 == 1 ? 'odd' : 'even') . '-' . ($file['active'] == 1 ? 'active' : 'inactive') ?>">
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
						echo '<a href="?action=details&amp;id_share=' . $id_share . '&amp;dir=' . $file['relative_directory'] . '">' . $file['filename'] . '</a>';
					}
					else {
						echo $file['filename'];
					}
				?></td>
				<td><?= formatFileSize($file['size']) ?></td>
				<td><?= $file['date_last_modified'] ?></td>
				<td><?= ($file['indexing'] == 1 ? '<span class="fa fa-bolt" title="Indexing..."></span>' : '') ?></td>
			</tr>
			<?php
		}		
	}
	while($file = mysql_fetch_array($qry_files)){ 
		if(($show_all == 0 && $file['active'] == 1) || $show_all == 1){
			$i++;
			?>
			<tr class="<?=($file['is_directory'] == 1 ? 'row-dir' : 'row-file') . '-' . ($i % 2 == 1 ? 'odd' : 'even') . '-' . ($file['active'] == 1 ? 'active' : 'inactive') ?>">
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
						echo '<a href="?action=details&amp;id_share=' . $id_share . '&amp;dir=' . $file['relative_directory'] . $file['filename'] . '">' . $file['filename'] . '</a>';
					}
					else {
						echo $file['filename'];
					}
				?></td>
				<td><?= formatFileSize($file['size']) ?></td>
				<td><?= $file['date_last_modified'] ?></td>
				<td><?= ($file['indexing'] == 1 ? '<span class="fa fa-bolt" title="Indexing..."></span>' : '') ?></td>
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
