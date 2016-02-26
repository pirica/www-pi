
<h1>Photos overview</h1>
	
<div>
<?php

for($i=0; $i<count($maps); $i++)
{
	if($maps[$i]['dir'] == 1)
	{
	?>
		<a href="index.php?action=map&amp;map=<?= $map . '/' . $maps[$i]['name'] ?>"><?= $maps[$i]['name'] ?></a><br/>
	<?php
	}
}

for($i=0; $i<count($maps); $i++)
{
	if($maps[$i]['dir'] == 0)
	{
	?>
		<img src="image.php?src=<?= $map . '/' . $maps[$i]['name'] ?>" /><br/>
	<?php
	}
}
?>
</div>