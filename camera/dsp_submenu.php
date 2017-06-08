
<ul class="nav nav-tabs">
	
	<?php 
	echo '<li class="' . ($action->getCode() == 'main' ? 'active' : '') . '"><a href="?action=main">Overview</a></li>';
	echo '<li class="' . ($action->getCode() == 'view' ? 'active' : '') . '"><a href="?action=view">View captures</a></li>';
	echo '<li class="' . ($action->getCode() == 'gifs' ? 'active' : '') . '"><a href="?action=gifs">View captures (gifs)</a></li>';
	echo '<li class="' . ($action->getCode() == 'thumbs' ? 'active' : '') . '"><a href="?action=thumbs">View captures (thumbs)</a></li>';
	echo '<li class="' . ($action->getCode() == 'archive' ? 'active' : '') . '"><a href="?action=archive">View archived</a></li>';
	?>
	
	<li class="dropdown pull-right <?= ($action->getCode() == 'camera' ? 'active' : '') ?>">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#">
			Cameras
			<span class="caret"></span>
		</a>
		<ul class="dropdown-menu" role="menu">
			<?php 
			echo '<li class="' . ($action->getCode() == 'camera' && $id_camera == -1 ? 'active' : '') . '"><a href="?action=camera&id_camera=-1">All</a></li>';
			echo '<li class="divider"></li>';
            for ($i = 0; $i < $cameracount; $i++) {
				echo '<li class="' . ($action->getCode() == 'camera' && $id_camera == $cameras[$i]['id_camera'] ? 'active' : '') . '"><a href="?action=camera&id_camera=' . $cameras[$i]['id_camera'] . '">' . $cameras[$i]['description'] . '</a></li>';
			}
			?>
		</ul>
	</li>
	
</ul>
