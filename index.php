<?php
require '_core/appinit.php';

$app->setTitle('index');

require '_core/dsp_header.php';

?>

<div class="jumbotron">
	<h1>Nasberrypi</h1>
	<p>You've reached the index page of the nasberrypi server.</p>
	<!--p><a class="btn btn-primary btn-lg" role="button">Learn more</a></p-->
</div>

<div class="row">
	<?php
	$apps = $app->getMenuData();
	$appcount = count($apps);
	for($i = 0; $i<$appcount; $i++){
		if($apps[$i]['show_in_overview'] == 1){
		?>
			<div class="col-xs-6 col-md-3">
				<div class="thumbnail">
					<!--img data-src="holder.js/300x300" alt="..."-->
					<div class="caption">
						<h3><?= $apps[$i]['description'] ?></h3>
						<p><?= $apps[$i]['info'] ?></p>
						<p><a href="<?= $apps[$i]['relative_url'] ?>" class="btn btn-primary" role="button">Go to</a>
					</div>
				</div>
			</div>
		<?php
		}
	}
	?>
	
</div>

<?php
require '_core/dsp_footer.php';
?>