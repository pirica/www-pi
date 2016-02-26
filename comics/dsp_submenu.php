
<div>
	<a href="?action=main">Overview</a>
	
	<?php
	if($action->getCode() == 'main' && $comic != '')
	{
		?>
		<a href="?action=main&amp;comic=<?= $comic ?>"><?= $comic ?></a>
		<?php
	}
	
	if($action->getCode() == 'comic')
	{
		?>
		<a href="?action=main&amp;comic=<?= $comic ?>"><?= $comic ?></a>
		<?php
	}
	
	?>
</div>
