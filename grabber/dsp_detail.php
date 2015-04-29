
<h1>Grab details</h1>

<p>
	<!--a class="btn btn-primary" href="index.php?action=setgrab&amp;id_grab=-1">
		<span class="glyphicon glyphicon-plus"></span>
		Add new grab
	</a-->
	
	<form class="form-horizontal" id="files-form">
		<fieldset>
			
			<div class="col-md-9">
				<div class="form-group">
					<label for="search">Search</label>
					<input id="search" name="search" placeholder="" class="form-control" type="text" value="<?=$search ?>">
				</div>
			</div>
			
			<div class="col-md-1"></div>
			
			<div class="col-md-2">
				<div class="form-group">
					<label for="status">Status</label>
					<select id="status" name="status" class="form-control">
						<option value="*" <?php echo $status == '*' ? 'selected="selected"' : ''; ?>>All</option>
						<option value="n" <?php echo $status == 'n' ? 'selected="selected"' : ''; ?>>New, unprocessed</option>
						<option value="p" <?php echo $status == 'p' ? 'selected="selected"' : ''; ?>>Processing</option>
						<option value="ok" <?php echo $status == 'ok' ? 'selected="selected"' : ''; ?>>Processed</option>
						<option value="nf" <?php echo $status == 'nf' ? 'selected="selected"' : ''; ?>>Not found</option>
						<option value="to" <?php echo $status == 'to' ? 'selected="selected"' : ''; ?>>Time-out</option>
						<option value="fe" <?php echo $status == 'fe' ? 'selected="selected"' : ''; ?>>File empty</option>
						<option value="fx" <?php echo $status == 'fx' ? 'selected="selected"' : ''; ?>>File exists</option>
						<option value="x" <?php echo $status == 'x' ? 'selected="selected"' : ''; ?>>Excluded</option>
						<option value="e" <?php echo $status == 'e' ? 'selected="selected"' : ''; ?>>In error</option>
					</select>
				</div>
			</div>
			
			<div class="col-md-6">
				<div class="form-group">
					<label class="control-label" for="date_inserted">Date inserted:</label>
					<div class="controls">
						<div class="input-prepend input-group">
							<span class="input-group-addon">
								<input type="checkbox">
							</span>
							<span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
							<input type="text" style="width: 400px" name="date_inserted" id="date_inserted" class="form-control" value="08/01/2013 1:00 PM - 08/01/2013 1:30 PM"  class="span4"/>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-md-6">
				<div class="form-group">
					<label class="control-label" for="date_modified">Date modified:</label>
					<div class="controls">
						<div class="input-prepend input-group">
							<span class="input-group-addon">
								<input type="checkbox">
							</span>
							<span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
							<input type="text" style="width: 400px" name="date_modified" id="date_modified" class="form-control" value="08/01/2013 1:00 PM - 08/01/2013 1:30 PM"  class="span4"/>
						</div>
					</div>
				</div>
			</div>
		</fieldset>
	</form>
	
	<script type="text/javascript">
		$(document).ready(function() {
			$('#date_inserted').daterangepicker({
				timePicker: true,
				timePickerIncrement: 30,
				format: 'DD/MM/YYYY h:mm A'
			},
			function(start, end, label) {
				console.log(start.toISOString(), end.toISOString(), label);
			});
			
			$('#date_modified').daterangepicker({
				timePicker: true,
				timePickerIncrement: 30,
				format: 'DD/MM/YYYY h:mm A'
			},
			function(start, end, label) {
				console.log(start.toISOString(), end.toISOString(), label);
			});
		});
	</script>
	
</p>

<div id="files-grid">
	<?php
		include 'dsp_detail_grid.php';
	?>
</div>
