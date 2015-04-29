

<ul class="nav nav-tabs">
	<li class=""><a href="index.php?action=setgrab&amp;id_grab=<?=$id_grab ?>">Grabber</a></li>
	<li class=""><a href="index.php?action=setgrab&amp;id_grab=<?=$id_grab ?>#counters">Counters</a></li>
	<li class="active"><a href="#counterdetail" data-toggle="tab">Counter detail</a></li>
</ul>

<div class="tab-content">

	<div class="tab-pane active" id="counterdetail">
		
		<?php
		
		if($id_grab_counter > 0){
			echo '<h1>Edit counter</h1>';
		}
		else {
			echo '<h1>Create counter</h1>';
		}
		
		?>
		
		<form id="frm-edit-counter" class="form-horizontal" method="post" action="index.php?action=do_setgrabcounter&amp;id_grab=<?=$id_grab?>&amp;id_grab_counter=<?=$id_grab_counter?>">
			<input type="hidden" name="action" value="do_setgrabcounter"/>
			<input type="hidden" name="id_grab" value="<?=$id_grab?>"/>
			<input type="hidden" name="id_grab_counter" value="<?=$id_grab_counter?>"/>
			
			
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
				<label for="counter_type">Type</label>
				<select id="counter_type" name="counter_type" class="form-control">
					<option value="date" <?php echo $counter_type == 'date' ? 'selected="selected"' : ''; ?>>Date</option>
					<option value="int" <?php echo $counter_type == 'int' ? 'selected="selected"' : ''; ?>>Integer</option>
					<option value="list" <?php echo $counter_type == 'list' ? 'selected="selected"' : ''; ?>>List</option>
				</select>
			</div>

			<div class="form-group">
				<label for="counter_field">Field</label>
				<input id="counter_field" name="counter_field" placeholder="" class="form-control" type="text" value="<?=$counter_field ?>">
			</div>
			
			
			<div class="form-group counter-fields date-counter-fields">
				<label for="counter_datefrom">Date from (as YYYY/MM/DD)</label>
				<input id="counter_datefrom" name="counter_datefrom" placeholder="" class="form-control" type="text" pattern="\d{1,2}/\d{1,2}/\d{4}" value="<?=$counter_datefrom ?>">
			</div>
			
			<div class="form-group counter-fields date-counter-fields">
				<label for="counter_dateto">Date to (as YYYY/MM/DD) (empty for current date)</label>
				<input id="counter_dateto" name="counter_dateto" placeholder="" class="form-control" type="text" pattern="\d{1,2}/\d{1,2}/\d{4}" value="<?=$counter_dateto ?>">
			</div>
			
			
			<div class="form-group counter-fields int-counter-fields">
				<label for="counter_intfrom">Integer from</label>
				<input id="counter_intfrom" name="counter_intfrom" placeholder="" class="form-control" type="text" pattern="\d*" value="<?=$counter_intfrom ?>">
			</div>
			
			<div class="form-group counter-fields int-counter-fields">
				<label for="counter_intto">Integer to</label>
				<input id="counter_intto" name="counter_intto" placeholder="" class="form-control" type="text" pattern="\d*" value="<?=$counter_intto ?>">
			</div>
			

			<div class="form-group counter-fields list-counter-fields">
				<label for="counter_listvalues">List values (comma-separated)</label>
				<textarea id="counter_listvalues" name="counter_listvalues" class="form-control"><?=$counter_listvalues ?></textarea>
			</div>
			
			
			<!--
			<div class="form-group">
				<label for="checkboxes">Inline Checkboxes</label>
				<div class="controls">
					<label class="checkbox inline" for="checkboxes-0">
					<input name="checkboxes" id="checkboxes-0" value="1234" type="checkbox">
					1234
					</label>
				</div>
			</div>
			-->
			
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
	
</div>


