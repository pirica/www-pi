

<?php
if($comic != '')
{
	?>
	<h1><?= $comic ?></h1>
	<?php
}
else 
{
	?>
	<h1>Comics overview</h1>
	<?php
}
?>
	
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
			<a href="index.php?action=comic&amp;comic=<?= $comic . '/' . $dirs[$i]['name'] ?>">
				<img src="thumb.php?src=<?= $comic . '/' . $dirs[$i]['name'] ?>" class="comic-img" /><br/>
				<?= $dirs[$i]['name'] ?>
			</a><br/>
		<?php
		}
	}
}
?>
</div>