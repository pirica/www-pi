

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



$counter = 1;

while($themes = mysqli_fetch_array($qry_themes))
{
	if($counter == 1) echo '<h3>Themes</h3>';
	
	?>
	<div class="row">
		<div class="thumb-ctr col-md-12">
			<a href="?action=main&amp;themeId=<?= $themes['id'] ?>">
				<?php
				$sets = explode(',', $themes['sets']);
				$setcount = count($sets);
				for($i=0; $i<$setcount; $i++)
				{
				?>
					<div class="col-md-2">
						<!--<img src="thumb.php?src=<?= $sets[$i] . '/001.jpg' ?>" alt="<?= $themes['name'] ?>" title="<?= $themes['name'] ?>"/>-->
						<img src="thumbs/180prop/<?= $sets[$i] . '/001.jpg' ?>" alt="<?= $themes['name'] ?>" title="<?= $themes['name'] ?>"/>
					</div>
				<?php
				}
				?>
				<?= $themes['name'] ?>
			</a>
		</div>
	</div>
	<?php
	
	$counter++;
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
			<?php* /
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


if(mysqli_num_rows($qry_sets) > 0) echo '<h3>Sets</h3>';

echo '<div class="row">';

while($sets = mysqli_fetch_array($qry_sets))
{
	?>
	<div class="thumb-ctr col-md-2 col-sm-6">
		<a href="?action=view&amp;setId=<?= $sets['set_num'] ?>">
			<!--<img src="thumb.php?src=<?= $sets['set_num'] . '/001.jpg' ?>" alt="<?= $sets['name'] ?>" title="<?= $sets['name'] ?>"/><br/>-->
			<img src="thumbs/180prop/<?= $sets['set_num'] . '/001.jpg' ?>" alt="<?= $sets['name'] ?>" title="<?= $sets['name'] ?>"/><br/>
			<?= $sets['name'] ?>
		</a>
	</div>
	<?php
}

echo '</div>';


?>

</div>