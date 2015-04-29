
<ul class="nav nav-tabs">
	
	<?php 
	echo '<li class="' . ($action->getCode() == 'main' ? 'active' : '') . '"><a href="?action=main>Overview</a></li>';
	/*
	for ($i = 0; $i < $dircount; $i++) {
		//echo '<li class="' . ($date == $dirs[$i]['name'] ? 'active' : '') . '"><a href="?action=main&date=' . $dirs[$i]['name'] . '">' . $dirs[$i]['name'] . '</a></li>';
		echo '<li class="' . ($date == $dirs[$i] ? 'active' : '') . '"><a href="?action=main&date=' . $dirs[$i] . '">' . $dirs[$i] . '</a></li>';
	}*/
	?>
	
	<li class="dropdown pull-right <?= ($action == 'camera' ? 'active' : '') ?>">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#">
			Cameras
			<span class="caret"></span>
		</a>
		<ul class="dropdown-menu" role="menu">
			<?php 
			echo '<li class="' . ($action == 'camera' && $id_camera == -1 ? 'active' : '') . '"><a href="?action=camera&id_camera=-1">All</a></li>';
			echo '<li class="divider"></li>';
            for ($i = 0; $i < $cameracount; $i++) {
				echo '<li class="' . ($action == 'camera' && $id_camera == $cameras[$i]['id_camera'] ? 'active' : '') . '"><a href="?action=camera&id_camera=' . $cameras[$i]['id_camera'] . '">' . $cameras[$i]['description'] . '</a></li>';
			}
			?>
		</ul>
	</li>
	
</ul>


<?php
if(isset($files) && $filecount > 0){
?>

<ul class="nav nav-tabs">
	
	<?php 
	for ($i = 0; $i < $filecount; $i++) {
		echo '<li class="' . ($time == $files[$i]['hour_lbl'] ? 'active' : '') . '"><a href="?action=main&date=' . $date . '&time=' . $files[$i]['hour_lbl'] . '">' . $files[$i]['hour_lbl'] . '</a></li>';
		//echo '<li class="' . ($time == $files[$i] ? 'active' : '') . '"><a href="?action=main&date=' . $date . '&time=' . $files[$i] . '">' . $files[$i] . '</a></li>';
	}
	?>
	
</ul>
<?php
}
?>