
<h1>Feeds overview</h1>

<p>
	<a class="btn btn-primary" href="index.php?action=setfeed&amp;id_feed=-1">
		<span class="glyphicon glyphicon-plus"></span>
		Add new feed
	</a>
</p>


<table class="table">
	<thead>
		<tr>
			<th>ID</th>
			<th width="75%">Title</th>
			<th width="25%">URL</th>
			<th>Refresh rate</th>
			
			<th># Current entries</th>
			<th>Last entry date</th>
			
			<th>Last started</th>
			<th>Check?</th>
			
			<th>Edit</th>
			<th>Delete</th>
		</tr>
	</thead>
	
	<tbody>
	<?php 
	while($feed = mysql_fetch_array($qry_feeds)){
	?>
		<tr>
			<td><?= $feed['id_feed'] ?></td>
			<td><?= $feed['title'] ?></td>
			<td><a href="<?= $feed['url'] ?>" target="_blank"><?= $feed['url'] ?></a></td>
			<td><?= $feed['refresh'] ?></td>
			
			<td><?= $feed['entries'] ?></td>
			<td><?= ($feed['last_entry_date'] != '0000-00-00 00:00:00' ? $feed['last_entry_date'] : '') ?></td>
			
			<td><?= ($feed['date_start'] != '0000-00-00 00:00:00' ? $feed['date_start'] : '') ?></td>
			<td><?= ($feed['check_feed'] == 1 ? '<span class="fa fa-2x fa-exclamation-circle text-danger"></span>' : '') ?></td>
			
			<td>
				<a class="btn btn-primary" href="index.php?action=setfeed&amp;id_feed=<?=$feed['id_feed'] ?>">
					<span class="glyphicon glyphicon-edit"></span>
					Edit
				</a>
			</td>
			<td>
				<a class="btn btn-danger btn-delete-grab" href="index.php?action=delfeed&amp;id_feed=<?=$feed['id_feed'] ?>" data-toggle="modal" data-target="#myModal">
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
