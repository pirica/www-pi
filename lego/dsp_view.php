
<h1>Set "<?= $set['name'] ?>" (<?= $set['set_num'] ?>)</h1>
	
<div>
<?php

for($i=0; $i<count($files); $i++)
{
	if($files[$i]['dir'] == 0 && $files[$i]['name'] != '')
	{
	?>
		<img src="image.php?src=<?= $set['set_num'] . '/' . $files[$i]['name'] ?>" class="set-img" /><br/>
	<?php
	}
}
?>
</div>