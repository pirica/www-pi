
<h1>Comics overview</h1>
	
<div>
<?php

for($i=0; $i<count($dirs); $i++)
{
	if($dirs[$i]->dir == 1)
	{
	?>
		<a href="index.php?action=comic&amp;comic=<?= $dirs[$i]->name ?>"><?= $dirs[$i]->name ?></a>
	<?php
	}
}
?>
</div>