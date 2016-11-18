
<h1>Shares overview</h1>

<?php
/*
<p>
	<a class="btn btn-primary" href="index.php?action=setgrab&amp;id_share=-1">
		<span class="glyphicon glyphicon-plus"></span>
		Add new grab
	</a>
</p>
*/
?>

<table class="table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Description</th>
			<th># Files</th>
			<th># Dirs</th>
			<th># Hosts</th>
			<th>Total size</th>
			<th>Last modification</th>
			<th>% Indexed</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	
	<tbody>
	<?php 
	while($stat = mysqli_fetch_array($qry_share_stats)){ 
	?>
		<tr>
			<td><?= $stat['id_share'] ?></td>
			<td><a href="index.php?action=details&amp;id_share=<?= $stat['id_share'] ?>"><?=$stat['name'] ?></a></td>
			<td><?= $stat['nbr_files'] ?></td>
			<td><?= $stat['nbr_dirs'] ?></td>
			<td><?= $stat['hosts_linked'] ?></td>
			<td><?= formatFileSize($stat['total_file_size']) ?></td>
			<td><?= $stat['max_date_last_modified'] ?></td>
			<td><?= ($stat['nbr_dirs'] == 0 ? 0 : round(100 * $stat['dirs_checked'] / $stat['nbr_dirs'],1)) ?> %</td>
			
			<td>
				<a class="btn btn-primary" href="index.php?action=search&amp;id_share=<?=$stat['id_share'] ?>" title="Search in files">
					<span class="fa fa-search"></span>
				</a>
			</td>
			
		</tr>
		
	<?php 
	}
	?>
	</tbody>
	
</table>

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
