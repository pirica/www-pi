
<h1>Overview</h1>

<div class="row">

	<div class="col-xs-12 col-md-2">
		
		<!--
		<div id="MainMenu">
		  <div class="list-group panel">
			<a href="#demo3" class="list-group-item list-group-item-success" data-toggle="collapse" data-parent="#MainMenu">Item 3</a>
			<div class="collapse" id="demo3">
			  <a href="#SubMenu1" class="list-group-item" data-toggle="collapse" data-parent="#SubMenu1">Subitem 1 <i class="fa fa-caret-down"></i></a>
			  <div class="collapse list-group-submenu" id="SubMenu1">
				<a href="#" class="list-group-item" data-parent="#SubMenu1">Subitem 1 a</a>
				<a href="#" class="list-group-item" data-parent="#SubMenu1">Subitem 2 b</a>
				<a href="#SubSubMenu1" class="list-group-item" data-toggle="collapse" data-parent="#SubSubMenu1">Subitem 3 c <i class="fa fa-caret-down"></i></a>
				<div class="collapse list-group-submenu list-group-submenu-1" id="SubSubMenu1">
				  <a href="#" class="list-group-item" data-parent="#SubSubMenu1">Sub sub item 1</a>
				  <a href="#" class="list-group-item" data-parent="#SubSubMenu1">Sub sub item 2</a>
				</div>
				<a href="#" class="list-group-item" data-parent="#SubMenu1">Subitem 4 d</a>
			  </div>
			  <a href="javascript:;" class="list-group-item">Subitem 2</a>
			  <a href="javascript:;" class="list-group-item">Subitem 3</a>
			</div>
			
			<a href="#demo4" class="list-group-item list-group-item-success" data-toggle="collapse" data-parent="#MainMenu">Item 4</a>
			<div class="collapse" id="demo4">
			  <a href="" class="list-group-item">Subitem 1</a>
			  <a href="" class="list-group-item">Subitem 2</a>
			  <a href="" class="list-group-item">Subitem 3</a>
			</div>
		  </div>
		</div>
		-->


		<div id="MainMenu">
			<div class="list-group panel">
				<?php
				foreach ($camera_log_menu_data as $log_date => $log_times) {
					$subcount = count($log_times);
				?>
					<a href="#date<?= $log_date ?>" class="list-group-item <?= $log_date == $date && $time == 'all' ? 'active' : '' ?>" data-toggle="collapse" data-parent="#MainMenu" title="<?= $subcount ?> events"><?= $log_date ?> <span class="badge"><?= $subcount ?></span></a>
					<div class="<?= $log_date == $date ? '' : 'collapse' ?>" id="date<?= $log_date ?>">
						<a href="?action=view&date=<?= $log_date ?>&time=all" class="list-group-item list-group-item-sub">All</a>
						<?php
						for ($j = 0; $j < $subcount; $j++) {
						?>
							<a href="?action=view&date=<?= $log_date ?>&time=<?= $log_times[$j]['hour_lbl'] ?>" class="list-group-item list-group-item-sub <?= $log_date == $date && $time == $log_times[$j]['hour_lbl'] ? 'active' : '' ?>" title="<?= $log_times[$j]['nbr_images'] ?> images, <?= $log_times[$j]['nbr_videos'] ?> videos"><?= $log_times[$j]['hour_lbl'] ?> <span class="badge"><?= $log_times[$j]['nbr_images'] ?></span></a>
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
			while($camera_log = mysql_fetch_array($qry_camera_log)){
				if($prev_hour_lbl != $camera_log['hour_lbl']){
					echo '<h4>' . $camera_log['hour_lbl'] . '</h4>';
					echo '<p><a href="?action=do_archive&date=' . $date . '&time=' . $camera_log['hour_lbl'] . '"">Archive these</a></p>';
				}
				
				$extarr = explode('.', $camera_log['name']);
				$extension = '.' . $extarr[count($extarr) - 1];
				
				if(strtolower($extension) == '.jpg'){
					echo '<p><img src="image.php?src=' . $date . '/' . $camera_log['name'] . '" title="' . $camera_log['name'] . '" /><br/>';
					echo $camera_log['name'] . '</p>';
				}
				
				if(strtolower($extension) == '.mp4'){
					echo '<p><a href="video.php?src=' . $date . '/' . $camera_log['name'] . '">' . $camera_log['name'] . '</a></p>';
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
