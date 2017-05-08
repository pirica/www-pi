
<div>
	<a href="?action=main">Overview</a>
	
	<?php
	/*if($action->getCode() == 'main' && $map != '')
	{
		?>
		- <a href="?action=main&amp;map=<?= $map ?>"><?= $map ?></a>
		<?php
	}*/
	
	while($theme = mysqli_fetch_array($qry_theme))
	{
		?>
		- <a href="?action=main&amp;themeId=<?= $theme['id'] ?>"><?= $theme['name'] ?></a>
		<?php
	}
	
	?>
</div>
