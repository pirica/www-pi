
<div>
	<a href="?action=main">Overview</a>
	
	<?php
	/*if($action->getCode() == 'main' && $map != '')
	{
		?>
		- <a href="?action=main&amp;map=<?= $map ?>"><?= $map ?></a>
		<?php
	}*/
	
	if($action->getCode() == 'map')
	{
		$map_part = explode('/',$map)[0];
		?>
		- <a href="?action=main&amp;map=<?= $map_part ?>"><?= $map_part ?></a>
		<?php
	}
	
	?>
</div>
