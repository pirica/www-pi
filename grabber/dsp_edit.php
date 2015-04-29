
<ul class="nav nav-tabs">
	<li class="active"><a href="#grabber" data-toggle="tab">Grabber</a></li>
	<li><a href="#counters" data-toggle="tab">Counters</a></li>
	<li class="disabled"><a href="#counterdetail" data-toggle="tab">Counter detail</a></li>
</ul>

<div class="tab-content">
	<div class="tab-pane active" id="grabber">

		<?php

		if($id_grab > 0){
			echo '<h1>Edit grabber</h1>';
		}
		else {
			echo '<h1>Create grabber</h1>';
		}

		?>

		<form id="frm-edit" class="form-horizontal" method="post" action="index.php?action=do_setgrab&amp;id_grab=<?=$id_grab?>">
			<input type="hidden" name="action" value="do_setgrab"/>
			<input type="hidden" name="id_grab" value="<?=$id_grab?>"/>
			

				<!--legend>
				<?php
				if($id_grab > 0){
					echo 'Edit grabber';
				}
				else {
					echo 'Create grabber';
				}
				?>
				</legend-->

				<div class="form-group">
					<label for="grab_description">Description</label>
					<input id="grab_description" name="grab_description" placeholder="" class="form-control" type="text" value="<?=$grab_description?>">
				</div>
				
				<div class="form-group">
					<label for="grab_url">URL</label>
					<input id="grab_url" name="grab_url" placeholder="" class="form-control" type="text" value="<?=$grab_url?>">
				</div>
				
				<div class="form-group">
					<label for="grab_path">Path</label>
					<input id="grab_path" name="grab_path" placeholder="" class="form-control" type="text" value="<?=$grab_path?>">
				</div>
				
				<div class="form-group">
					<label for="grab_filename">Filename</label>
					<input id="grab_filename" name="grab_filename" placeholder="" class="form-control" type="text" value="<?=$grab_filename?>">
				</div>
				
				<div class="form-group">
					<label for="grab_excluded">Exclude if content contains</label>
					<input id="grab_excluded" name="grab_excluded" placeholder="" class="form-control" type="text" value="<?=$grab_excluded?>">
				</div>
				
				<div class="form-group">
					<label for="grab_max_grabbers">Max nbr of grabbers (defaults to <?= $settings->val('grabber_maxgrabbers_default', 20) ?> if empty)</label>
					<input id="grab_max_grabbers" name="grab_max_grabbers" placeholder="" class="form-control" type="text" value="<?=$grab_max_grabbers?>">
				</div>
				
				
				
				<div class="form-group">
					<!--label class="control-label" for="singlebutton">Save</label-->
					<button id="singlebutton" name="singlebutton" class="btn btn-primary" type="submit" value="Save">Save</button>
				</div>


				
				<div class="form-group">
				<?php
				if($error == 1){
					echo '<div class="alert alert-danger">Some required fields are incorrect</div>';
				}
				?>
				</div>
				
			
			
		</form>
	</div>
	
	<div class="tab-pane" id="counters">

		<h2>Edit counters</h2>
		
		<?php

		if($id_grab <= 0){
			echo '<div class="alert alert-warning"><p>Grabber details must have been saved before counters can be added</p></div>';
		}
		else {
			?>
			
			<p>
				<a class="btn btn-primary" href="index.php?action=setgrabcounter&amp;id_grab=<?=$id_grab ?>&amp;id_grab_counter=-1">
					<span class="glyphicon glyphicon-plus"></span>
					Add new counter
				</a>
			</p>
			
			<table class="table">
				<thead>
					<tr>
						<th>ID</th>
						<th>Field</th>
						<th>Type</th>
						<th>From</th>
						<th>To</th>
						<th># Values</th>
						<th>Status</th>
						<th>Edit</th>
						<th>Delete</th>
					</tr>
				</thead>
				
				<tbody>
				<?php 
				while($grabcount = mysql_fetch_array($qry_grab_counts)){ 
				?>
					<tr>
						<td><?=$grabcount['id_grab_counter'] ?></td>
						<td><?=$grabcount['field'] ?></td>
						<td><?=$grabcount['type'] ?></td>
						<td <?php echo $grabcount['type'] == 'list' ? 'colspan="2"' : ''; ?>>
							<?php
								switch($grabcount['type']){
									case 'int':
										echo $grabcount['intfrom'];
										break;
									case 'date':
										echo $grabcount['datefrom'];
										break;
									case 'list':
										if(strlen($grabcount['listvalues']) > 110){
											echo substr($grabcount['listvalues'], 100) . '...';
										}
										else {
											echo $grabcount['listvalues'];
										}
										break;
								}
							?>
						</td>
						<?php if($grabcount['type'] != 'list'){ ?>
						<td>
							<?php
								switch($grabcount['type']){
									case 'int':
										echo $grabcount['intto'];
										break;
									case 'date':
										echo $grabcount['dateto'];
										break;
									case 'list':
										break;
								}
							?>
						</td>
						<?php } ?>
						<td><?=$grabcount['count'] ?></td>
						<td>
							<?php
							if($grabcount['active'] == 1){
								echo '';
								
							}
							else {
								echo '-Removed-';
							}
							?>
						</td>
						<td>
							<?php
							if($grabcount['active'] == 1){
							?>
								<a class="btn btn-primary" href="index.php?action=setgrabcounter&amp;id_grab=<?=$id_grab?>&amp;id_grab_counter=<?=$grabcount['id_grab_counter'] ?>">
									<span class="glyphicon glyphicon-edit"></span>
									Edit
								</a>
							<?php
							}
							?>
						</td>
						<td>
							<?php
							if($grabcount['active'] == 1){
							?>
								<a class="btn btn-danger" href="index.php?action=delgrabcounter&amp;id_grab=<?=$id_grab?>&amp;id_grab_counter=<?=$grabcount['id_grab_counter'] ?>" data-toggle="modal" data-target="#myModal">
									<span class="glyphicon glyphicon-remove"></span>
									Delete
								</a>
							<?php
							}
							?>
						</td>
					</tr>
				<?php 
				}
				?>
				</tbody>
				
			</table>
			<?php
		}

		?>
	
	</div>

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