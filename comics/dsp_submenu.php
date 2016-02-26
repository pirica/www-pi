
<div>
	<a href="?action=main">Overview</a>
	
	<?php
	/*if($action->getCode() == 'main' && $comic != '')
	{
		?>
		- <a href="?action=main&amp;comic=<?= $comic ?>"><?= $comic ?></a>
		<?php
	}*/
	
	if($action->getCode() == 'comic')
	{
		$comic_part = explode('/',$comic)[0];
		?>
		- <a href="?action=main&amp;comic=<?= $comic_part ?>"><?= $comic_part ?></a>
		<?php
	}
	
	?>
</div>
