

<?php
if($map != '')
{
	?>
	<h1><?= $map ?></h1>
	<?php
}
else 
{
	?>
	<h1>Photos overview</h1>
	<?php
}
?>
	
<div>
<?php

for($i=0; $i<count($dirs); $i++)
{
	if($dirs[$i]['dir'] == 1)
	{
		if($map == '')
		{
		?>
			<a href="index.php?action=main&amp;map=<?= $dirs[$i]['name'] ?>"><?= $dirs[$i]['name'] ?></a><br/>
		<?php
		}
		else
		{
		?>
			<a href="index.php?action=map&amp;map=<?= $map . '/' . $dirs[$i]['name'] ?>"><?= $dirs[$i]['name'] ?></a><br/>
		<?php
		}
	}
}
?>
</div>