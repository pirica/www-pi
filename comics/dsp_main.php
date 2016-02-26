
<h1>Comics overview</h1>
	
<div>
<?php

for($i=0; $i<count($dirs); $i++)
{
	if($dirs[$i]['dir'] == 1)
	{
		if($comic == '')
		{
		?>
			<a href="index.php?action=main&amp;comic=<?= $dirs[$i]['name'] ?>"><?= $dirs[$i]['name'] ?></a><br/>
		<?php
		}
		else
		{
		?>
			<a href="index.php?action=comic&amp;comic=<?= $dirs[$i]['name'] ?>"><?= $dirs[$i]['name'] ?></a><br/>
		<?php
		}
	}
}
?>
</div>