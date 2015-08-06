
<h1>share details</h1>

<div id="files-grid">
	
	<h2>
		Search files
	</h2>

	<form method="get" action="?action=<?= $action->getCode() ?>">
		<input type="hidden" name="action" value="<?= $action->getCode() ?>">
		<input type="hidden" name="id_share" value="<?= $id_share ?>">
		<input type="hidden" name="dir" value="<?= $currentdir['relative_directory'] ?>">
		
		
		
		<div class="row">
			<div class="col-md-7">
				<input id="search" name="search" placeholder="" class="form-control" type="text" value="<?=$search ?>">
			</div>
			
			<div class="col-md-2">
				<input class="btn btn-default" type="submit" value="Search">
			</div>
			
			<div class="col-md-1"></div>
			
			<div class="col-md-2">
				<div class="form-group">
					<input type="checkbox" id="filter_all" name="all" value="1" <?= ($show_all == 1 ? 'checked="checked"' : '') ?>/>
					<label for="filter_all">Also show deleted files</label>
				</div>
			</div>
		</div>
	</form>

	<table class="table">
		<thead>
			<tr>
				<th width="50">&nbsp;</th>
				<th sortfield="relative_directory">
					Dir
					<span class="thsort thsort-relative_directory-asc glyphicon glyphicon-arrow-up <?php if(!($sort == 'relative_directory' && $sortorder == 'asc')){ ?>hidden<?php } ?>"></span>
					<span class="thsort thsort-relative_directory-desc glyphicon glyphicon-arrow-down <?php if(!($sort == 'relative_directory' && $sortorder == 'desc')){ ?>hidden<?php } ?>"></span>
				</th>
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
				<th width="80">&nbsp;</th>
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
							echo '<a href="?action=details&amp;id_share=' . $id_share . '&amp;all=' . $show_all . '&amp;dir=' . $file['relative_directory'] . '">' . $file['parent_directory'] . '</a>';
						}
						else {
							echo $file['parent_directory'];
						}
					?></td>
					<td><?php
						if($file['is_directory'] == 1){
							echo '<a href="?action=details&amp;id_share=' . $id_share . '&amp;all=' . $show_all . '&amp;dir=' . $file['relative_directory'] . '">' . $file['filename'] . '</a>';
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
								echo '<a href="#" class="act-dir-reindex hover" data-dir="'. $file['relative_directory'] .'"><span class="fa fa-bolt green" title="Force reindexing of directory"></span></a>';
							}
							
							// download
							echo '<span class="fa"></span>';
							// view
							echo '<span class="fa"></span>';
						?>
					</td>
				</tr>
				<?php
			}		
		}
		while($file = mysql_fetch_array($qry_files)){ 
			if(($show_all == 0 && $file['active'] == 1) || $show_all == 1){
				$i++;
				?>
				<tr class="<?=($file['is_directory'] == 1 ? 'row-dir' : 'row-file') . '-' . ($i % 2 == 1 ? 'odd' : 'even') . '-' . ($file['active'] == 1 ? 'active' : 'inactive') ?>">
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
					<td>
						<?php
							if($file['is_directory'] == 1){
								echo '<a href="?action=details&amp;id_share=' . $id_share . '&amp;all=' . $show_all . '&amp;dir=' . $file['relative_directory'] . $file['filename'] . '">' . $file['relative_directory'] . '</a>';
							}
							else {
								echo '<a href="?action=details&amp;id_share=' . $id_share . '&amp;all=' . $show_all . '&amp;dir=' . $file['relative_directory'] . '">' . $file['relative_directory'] . '</a>';
							}
						?>
					</td>
					<td>
						<?php
							if($file['is_directory'] == 1){
								echo '<a href="?action=details&amp;id_share=' . $id_share . '&amp;all=' . $show_all . '&amp;dir=' . $file['relative_directory'] . $file['filename'] . '">' . $file['filename'] . '</a>';
							}
							else {
								echo $file['filename'];
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
								echo '<a href="?action=downloadfile&amp;id_file=' . $file['id_file'] . '"><span class="fa fa-download title="Download"></span></a>';
							}
							else {
								echo '<span class="fa"></span>';
							}
							
							if($file['can_view'] == 1){
								echo '<a href="?action=viewfile&amp;id_file=' . $file['id_file'] . '"><span class="fa fa-eye title="View"></span></a>';
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

</div>


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
