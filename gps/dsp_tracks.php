
<h1>Tracks overview</h1>

<!--<p>
	<a class="btn btn-primary" href="index.php?action=settrack&amp;id_track=-1">
		<span class="glyphicon glyphicon-plus"></span>
		Add new track
	</a>
</p>-->

<table class="table">
	<thead>
		<tr>
			<th>ID</th>
			<th width="75%">Description</th>
			<th width="25%">Prefix</th>
			
			<!--<th>Edit</th>-->
			<th>Delete</th>
		</tr>
	</thead>
	
	<tbody>
	<?php 
	while($track = mysql_fetch_array($qry_tracks)){ 
	?>
		<tr>
			<td><?= $track['id_track'] ?></td>
			<td><?= $track['description'] ?></td>
			<td><?= $track['pre_description'] ?></td>
			
			<!--<td>
				<a class="btn btn-primary" href="index.php?action=settrack&amp;id_track=<?=$track['id_track'] ?>">
					<span class="glyphicon glyphicon-edit"></span>
					Edit
				</a>
			</td>-->
			<td>
				<a class="btn btn-danger btn-delete-grab" href="index.php?action=deltrack&amp;id_track=<?=$track['id_track'] ?>" data-toggle="modal" data-target="#myModal">
					<span class="glyphicon glyphicon-remove"></span>
					Delete
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
