

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

for($i=0; $i<count($files); $i++)
{
	if($files[$i]['dir'] == 1)
	{
		?>
		<a href="index.php?action=main&amp;map=<?= $map . '/' . $files[$i]['name'] ?>"><?= $files[$i]['name'] ?></a><br/>
		<?php
	}
}


echo '<div class="row">';
$counter = 1;
for($i=0; $i<count($files); $i++)
{
	if($files[$i]['dir'] == 0)
	{
		if(stripos($files[$i]['name'], '.jpg') > 0 || stripos($files[$i]['name'], '.jpeg') > 0 || stripos($files[$i]['name'], '.png') > 0)
		{
			?>
			<div class="thumb-ctr col-md-2 col-sm-6"><img src="thumb.php?src=<?= $map . '/' . $files[$i]['name'] ?>" alt="<?= $files[$i]['name'] ?>" title="<?= $files[$i]['name'] ?>"/></div>
			<?php
		}
		else if(stripos($files[$i]['name'], '.mp4') > 0)
		{
			?>
			<div class="thumb-ctr col-md-2 col-sm-6"><img src="thumb_movie.php?src=<?= $map . '/' . $files[$i]['name'] ?>" alt="<?= $files[$i]['name'] ?>" title="<?= $files[$i]['name'] ?>"/></div>
			<?php
		}
		else
		{
			?>
			<div class="thumb-ctr col-md-2 col-sm-6"><?= $files[$i]['name'] ?></div>
			<?php

		}
		
		/*if($counter % 4 == 0 && $counter > 1)
		{
			echo '</div><div class="row">';
		}*/
		
		$counter++;
	}
}
echo '</div>';
?>

</div>