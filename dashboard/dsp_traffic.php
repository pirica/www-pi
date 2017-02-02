

<h1>Traffic</h1>

<?php
//if(date("Y/m/d H:i", time()) != $timings_max['latest'])
echo $timings_max['latest'];
?>

<div class="row">
	
	<div class="col-xs-12 col-md-6">
		<div class="timings" style="position: relative;">
			<img src="images/ring_antw.jpg" style="width:100%" />
			
			<div class="to" style="position: absolute; left: 71%; top: 66%;">	<span><?= $timings['ao-az'] ?></span>	</div>
			<div class="to" style="position: absolute; left: 57%; top: 87%;">	<span><?= $timings['az-ac'] ?></span>	</div>

			<div class="from" style="position: absolute; left: 57%; top: 93%;">	<span><?= $timings['ac-az'] ?></span>	</div>
			<div class="from" style="position: absolute; left: 74%; top: 72%;">	<span><?= $timings['az-ao'] ?></span>	</div>
			
		</div>
	</div>
	
	<div class="col-xs-12 col-md-6">
		<div class="timings" style="position: relative;">
			<img src="images/e313.jpg" style="width:100%" />
			
			<div class="to" style="position: absolute; left: 76%; top: 50%;">	<span><?= $timings['go-gw'] ?></span>	</div>
			<div class="to" style="position: absolute; left: 67%; top: 50%;">	<span><?= $timings['gw-ho'] ?></span>	</div>
			<div class="to" style="position: absolute; left: 58%; top: 50%;">	<span><?= $timings['ho-hi'] ?></span>	</div>
			<div class="to" style="position: absolute; left: 49%; top: 50%;">	<span><?= $timings['hi-hw'] ?></span>	</div>
			<div class="to" style="position: absolute; left: 40%; top: 50%;">	<span><?= $timings['hw-m'] ?></span>	</div>
			<div class="to" style="position: absolute; left: 31%; top: 50%;">	<span><?= $timings['m-r'] ?></span>	</div>
			<div class="to" style="position: absolute; left: 22%; top: 50%;">	<span><?= $timings['r-w'] ?></span>	</div>
			<div class="to" style="position: absolute; left: 12%; top: 50%;">	<span><?= $timings['w-ao'] ?></span>	</div>

			<div class="from" style="position: absolute; left: 12%; top: 63%;">	<span><?= $timings['ao-w'] ?></span>	</div>
			<div class="from" style="position: absolute; left: 22%; top: 63%;">	<span><?= $timings['w-r'] ?></span>	</div>
			<div class="from" style="position: absolute; left: 31%; top: 63%;">	<span><?= $timings['r-m'] ?></span>	</div>
			<div class="from" style="position: absolute; left: 40%; top: 63%;">	<span><?= $timings['m-hw'] ?></span>	</div>
			<div class="from" style="position: absolute; left: 49%; top: 63%;">	<span><?= $timings['hw-hi'] ?></span>	</div>
			<div class="from" style="position: absolute; left: 58%; top: 63%;">	<span><?= $timings['hi-ho'] ?></span>	</div>
			<div class="from" style="position: absolute; left: 67%; top: 63%;">	<span><?= $timings['ho-gw'] ?></span>	</div>
			<div class="from" style="position: absolute; left: 76%; top: 63%;">	<span><?= $timings['gw-go'] ?></span>	</div>
		</div>
	</div>
	
</div>
