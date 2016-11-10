
<h1>Overview</h1>

<div class="row">

	<div class="col-xs-12 col-md-2">

		<div id="MainMenu">
			<div class="list-group panel">
				<?php
				foreach ($camera_log_menu_data as $log_date => $log_times) {
					$subcount = count($log_times);
					
					$log_date_lbl = date_create_from_format("Ymd",$log_date)->format($settings->val('menu_date_format', 'Ymd'));
				?>
					<a href="#date<?= $log_date ?>" class="list-group-item <?= $log_date == $date && $time == 'all' ? 'active' : '' ?>" data-toggle="collapse" data-parent="#MainMenu" title="<?= $subcount ?> events"><?= $log_date_lbl ?> <span class="badge"><?= $subcount ?></span></a>
					<div class="<?= $log_date == $date ? '' : 'collapse' ?>" id="date<?= $log_date ?>">
						<a href="?action=<?= $action->getCode() ?>&date=<?= $log_date ?>&time=all" class="list-group-item list-group-item-sub">All</a>
						<?php
						for ($j = 0; $j < $subcount; $j++) {
						?>
							<a href="?action=<?= $action->getCode() ?>&date=<?= $log_date ?>&time=<?= $log_times[$j]['hour_lbl'] ?>" class="list-group-item list-group-item-sub <?= $log_date == $date && $time == $log_times[$j]['hour_lbl'] ? 'active' : '' ?>" title="<?= $log_times[$j]['nbr_images'] ?> images, <?= $log_times[$j]['nbr_videos'] ?> videos"><?= $log_times[$j]['hour_lbl'] ?> <span class="badge"><?= $log_times[$j]['nbr_images'] ?></span></a>
						<?php
						}
						?>
					</div>
				<?php
				}
				?>
			</div>
		</div>
		
	</div><!--.col-->

	<div class="col-xs-12 col-md-10">

		<?php 
		if($date != ''){
			$prev_hour_lbl = '';
			$prev_time_value = -1;
			$prev_image = '';
			while($camera_log = mysqli_fetch_array($qry_camera_log)){
				if($prev_hour_lbl != $camera_log['hour_lbl']){
					echo '<h4>' . $camera_log['hour_lbl'] . '</h4>';
					echo '<p>';
					echo '<a href="?action=do_archive&date=' . $date . '&time=' . $camera_log['hour_lbl'] . '"">Archive these</a>';
					echo '<a href="?action=do_delete&date=' . $date . '&time=' . $camera_log['hour_lbl'] . '"">Delete these</a>';
					echo '</p>';
					$prev_hour_lbl = $camera_log['hour_lbl'];
					
				}
				
				$extarr = explode('.', $camera_log['name']);
				$extension = '.' . $extarr[count($extarr) - 1];
				
				$thumb_image_arr = explode('_', $camera_log['name']);
				$thumb_image = $thumb_image_arr[0] . '_' . $thumb_image_arr[1] . '_' . $thumb_image_arr[2] . '.jpg';
				
				if($thumbs == 0 || ($thumbs == 1 && $prev_image != $thumb_image)){
					$prev_image = $thumb_image;
					
					if($settings->val('captures_show_gifs', 0) == 0 && strtolower($extension) == '.jpg'){
						//echo '<p><img src="image.php?src=' . $date . '/' . $camera_log['name'] . '" title="' . $camera_log['name'] . '" /><br/>';
						if($archived == 1)
						{
							echo '<p><img src="captures_archive/' . $date . '/' . $camera_log['name'] . '" title="' . $camera_log['name'] . '" /><br/>';
							echo $camera_log['name'] . '</p>';
						}
						else if($thumbs == 1)
						{
							echo '<p class="thumb"><img src="captures_thumbs/' . $date . '/' . $thumb_image . '" title="' . $thumb_image . '" /><br/>';
							echo $thumb_image . '</p>';
						}
						else 
						{
							echo '<p><img src="captures/' . $date . '/' . $camera_log['name'] . '" title="' . $camera_log['name'] . '" /><br/>';
							echo $camera_log['name'] . '</p>';
						}
					}
					
					if($settings->val('captures_show_gifs', 0) == 1 && strtolower($extension) == '.gif'){
						//echo '<p><img src="image.php?src=' . $date . '/' . $camera_log['name'] . '" title="' . $camera_log['name'] . '" /><br/>';
						if($archived == 1)
						{
							echo '<p><img src="captures_archive/' . $date . '/' . $camera_log['name'] . '" title="' . $camera_log['name'] . '" /><br/>';
						}
						else 
						{
							echo '<p><img src="captures/' . $date . '/' . $camera_log['name'] . '" title="' . $camera_log['name'] . '" /><br/>';
						}
						echo $camera_log['name'] . '</p>';
					}
					
					if(strtolower($extension) == '.mp4' || strtolower($extension) == '.avi'){
						//echo '<p><a href="video.php?src=' . $date . '/' . $camera_log['name'] . '">' . $camera_log['name'] . '</a></p>';
						if($archived == 1)
						{
							echo '<p><a href="captures_archive/' . $date . '/' . $camera_log['name'] . '">' . $camera_log['name'] . '</a></p>';
						}
						else 
						{
							echo '<p><a href="captures/' . $date . '/' . $camera_log['name'] . '">' . $camera_log['name'] . '</a></p>';
						}
						
					}
					
				}
				
			}
			
		}
		?>


	</div><!--.col-->
</div><!--.row-->

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
