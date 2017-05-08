

<div>
<?php

/*
for($i=0; $i<count($files); $i++)
{
	if($files[$i]['dir'] == 1)
	{
		?>
		<a href="index.php?action=main&amp;map=<?= $map . '/' . $files[$i]['name'] ?>"><?= $files[$i]['name'] ?></a><br/>
		<?php
	}
}
*/



echo '<div class="row">';
$counter = 1;

while($themes = mysqli_fetch_array($qry_themes))
{
	?>
	<div class="thumb-ctr col-md-12">
		<a href="?action=main&amp;themeId=<?= $themes['id'] ?>">
			<?php
			$sets = explode(',', $themes['sets']);
			$setcount = count($sets);
			for($i=0; $i<$setcount; $i++)
			{
			?>
				<div class="col-md-2">
					<img src="thumb.php?src=<?= $themes['id'] . '/' . $sets[$i] . '/001.jpg' ?>" alt="<?= $themes['name'] ?>" title="<?= $themes['name'] ?>"/>
				</div>
			<?php
			}
			?>
			<?= $themes['name'] ?>
		</a>
	</div>
	<?php
}

/*
for($i=0; $i<count($files); $i++)
{
	if($files[$i]['dir'] == 0)
	{
		if(stripos($files[$i]['name'], '.jpg') > 0 || stripos($files[$i]['name'], '.jpeg') > 0 || stripos($files[$i]['name'], '.png') > 0)
		{
			/*?>
			<div class="thumb-ctr col-md-2 col-sm-6"><img src="thumb.php?src=<?= $map . '/' . $files[$i]['name'] ?>" alt="<?= $files[$i]['name'] ?>" title="<?= $files[$i]['name'] ?>"/></div>
			<?php*/
			?>
			<div class="thumb-ctr col-md-2 col-sm-6"><img src="thumb.php?src=<?= $map . '/' . $files[$i]['name'] ?>" alt="<?= $files[$i]['name'] ?>" title="<?= $files[$i]['name'] ?>"/></div>
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
		}* /
		
		$counter++;
	}
}*/
echo '</div>';



echo '<div class="row">';

while($sets = mysqli_fetch_array($qry_sets))
{
	?>
	<div class="thumb-ctr col-md-2 col-sm-6">
		<a href="?action=view&amp;setId=<?= $sets['set_num'] ?>">
			<img src="thumb.php?src=<?= $sets['theme_id'] . '/' . $sets['set_num'] . '/001.jpg' ?>" alt="<?= $sets['name'] ?>" title="<?= $sets['name'] ?>"/><br/>
			<?= $sets['name'] ?>
		</a>
	</div>
	<?php
}

echo '</div>';


?>

</div>