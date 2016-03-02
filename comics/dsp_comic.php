
<h1>Comics overview</h1>
	
<div>
<?php

for($i=0; $i<count($comics); $i++)
{
	if($comics[$i]['dir'] == 0)
	{
	?>
		<img src="image.php?src=<?= $comic . '/' . $comics[$i]['name'] ?>" class="comic-img" /><br/>
	<?php
	}
}
?>
</div>