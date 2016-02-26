

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

/*
for($i=0; $i<count($files); $i++)
{
	if($files[$i]['dir'] == 1)
	{
		if($map == '')
		{
		?>
			<a href="index.php?action=main&amp;map=<?= $files[$i]['name'] ?>"><?= $files[$i]['name'] ?></a><br/>
		<?php
		}
		else
		{
		?>
			<a href="index.php?action=main&amp;map=<?= $map . '/' . $files[$i]['name'] ?>"><?= $files[$i]['name'] ?></a><br/>
		<?php
		}
	}
}
*/


for($i=0; $i<count($files); $i++)
{
	if($files[$i]['dir'] == 1)
	{
	?>
		<a href="index.php?action=main&amp;map=<?= $map . '/' . $files[$i]['name'] ?>"><?= $files[$i]['name'] ?></a><br/>
	<?php
	}
}

for($i=0; $i<count($files); $i++)
{
	if($files[$i]['dir'] == 0)
	{
	?>
		<img src="thumb.php?src=<?= $map . '/' . $files[$i]['name'] ?>" /><br/>
	<?php
	}
}
?>

</div>