
<?php

if($id_feed > 0){
	echo '<h1>Edit feed</h1>';
}
else {
	echo '<h1>Create feed</h1>';
}

?>

<form id="frm-edit" class="form-horizontal" method="post" action="index.php?action=do_setfeed&amp;id_feed=<?=$id_feed?>">
	<input type="hidden" name="action" value="do_setfeed"/>
	<input type="hidden" name="id_feed" value="<?=$id_feed?>"/>
	

		<!--legend>
		<?php
		if($id_feed > 0){
			echo 'Edit feed';
		}
		else {
			echo 'Create feed';
		}
		?>
		</legend-->

		<div class="form-group">
			<label for="feed_url">URL</label>
			<input id="feed_url" name="feed_url" placeholder="" class="form-control" type="text" value="<?=$feed_url?>">
		</div>
		
		<div class="form-group">
			<label for="feed_title">Title <i class="fa fa-spinner fa-spin hidden"></i></label>
			<input id="feed_title" name="feed_title" placeholder="" class="form-control" type="text" value="<?=$feed_title?>">
		</div>
		
		<div class="form-group">
			<label for="feed_refresh">Refresh rate (minutes, empty for default (= <?= $settings->val('default_interval_check_feeds_minutes') ?>))</label>
			<div>Current: <div id="feed_refresh_lbl"><?= minutesToTimeRange($feed_refresh) ?></div></div>
			<input id="feed_refresh" name="feed_refresh" placeholder="" class="form-control" type="text" value="<?=$feed_refresh?>">
		</div>
		
		<div class="form-group">
			<label for="feed_parser">Custom feed parser</label>
			<input id="feed_parser" name="feed_parser" placeholder="" class="form-control" type="text" value="<?=$feed_parser?>">
		</div>
		
		<div class="form-group">
			<label for="feed_parse_max_items">Max. number of feed items to parse (empty for all)</label>
			<input id="feed_parse_max_items" name="feed_parse_max_items" placeholder="" class="form-control" type="text" value="<?=$feed_parse_max_items?>">
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