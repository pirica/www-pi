
<h1>Data usage (per <?= $date_period ?>)</h1>
<h2>Period from <?= $range_start ?> to <?= $range_end ?></h2>
<p>
	<a href="?action=<?= $action->getCode() ?>&date=<?= $date_prev ?>" <?= ($date_prev == '' ? 'class="disabled"' : '') ?>><i class="fa fa-arrow-left"></i> Previous</a>
	<a href="?action=<?= $action->getCode() ?>&date=<?= $date_next ?>" <?= ($date_next == '' ? 'class="disabled"' : '') ?>>Next <i class="fa fa-arrow-right"></i></a>
</p>
<p>
	<form method="get" action="?action=<?= $action->getCode() ?>&amp;date=<?= date("Y-m-d", $date) ?>">
		<input type="hidden" name="action" value="<?= $action->getCode() ?>">
		<input type="hidden" name="date" value="<?= date("Y-m-d", $date) ?>">
		
		<label for="filter_show">Show</label>
		<select id="filter_show" name="show">
			<option value="total" <?= ($show == 'total' ? 'selected="selected"' : '') ?>>Total</option>
			<option value="down" <?= ($show == 'down' ? 'selected="selected"' : '') ?>>Downloaded</option>
			<option value="up" <?= ($show == 'up' ? 'selected="selected"' : '') ?>>Uploaded</option>
			<option value="both" <?= ($show == 'both' ? 'selected="selected"' : '') ?>>Both down/up</option>
			<option value="all" <?= ($show == 'all' ? 'selected="selected"' : '') ?>>All</option>
		</select>
		
		<input type="checkbox" id="filter_telemeter" name="tm" value="1" <?= ($tm == 1 ? 'checked="checked"' : '') ?>/>
		<label for="filter_telemeter">Include Telemeter</label>
		
	<form>
</p>

<?php 

$maxval = mysqli_fetch_array($qry_max);


// hosts overview

$current_row = 0;
$current_host = -1;
$rows = mysqli_num_rows($qry_hosts);

while($host = mysqli_fetch_array($qry_hosts)){
	if($current_host != $host['id_host']){
		$current_host = $host['id_host'];
		if($current_row > 0){
			echo '	</ul>' . "\n";
			echo '</div>' . "\n";
		}
		echo '<div class="section" style="width:' . $section_width . 'px;">' . "\n";
		echo '	<div class="section-label"><a id="host' . $host['id_host'] . '"></a>' . $host['hostname'] . '</div>' . "\n";
		echo '	<ul class="timeline">' . "\n";
	}
	
    //echo '<!--' . $host['total'] .'/'. $maxval['max_total'] . '-->';
	
	$pct_total	= $host['total'] * 100 / $maxval['max_'.$show];
	$pct_down	= $host['downloaded'] * 100 / $maxval['max_'.$show];
	$pct_up		= $host['uploaded'] * 100 / $maxval['max_'.$show];
	
	$pct_total_tm	= $host['total_telemeter'] * 100 / $maxval['max_'.$show];
	$pct_down_tm	= $host['downloaded_telemeter'] * 100 / $maxval['max_'.$show];
	$pct_up_tm		= $host['uploaded_telemeter'] * 100 / $maxval['max_'.$show];
	
	$class_total	= 'total' . ($host['total'] == 0 ? -1 : round($pct_total, -1)/10);
	$class_down		= 'down' . ($host['downloaded'] == 0 ? -1 : round($pct_down, -1)/10) . ' ' . ($show == 'all' ? 'sm' : '');
	$class_up		= 'up' . ($host['uploaded'] == 0 ? -1 : round($pct_up, -1)/10) . ' ' . ($show == 'all' ? 'sm' : '');
	
	if($show == 'total' || $show == 'all'){
		echo '		<li class="usage-total ' . $class_total . '">' . "\n";
		echo '			<a href="?action='.$subaction.'&date=' . $host['date_usage'] . $subdate . '&host=' . $host['id_host'] . '" title="Total on ' . $host['date_usage'] . ': ' . formatFileSize($host['total']) . '">' . "\n";
		//if($current_row == 0 || $current_row % 5 == 0 || $current_row == $rows - 1) 
        {
			echo '				<span class="label">' . $host['date_usage_label'] . '</span>' . "\n";
		}
		echo '				<span class="count" style="height: ' . $pct_total . '%">(' . formatFileSize($host['total']) . ')</span>' . "\n";
		echo '			</a>' . "\n";
		echo '		</li>' . "\n";
		
		if($tm == 1){
			echo '		<li class="usage-total ' . $class_total . '">' . "\n";
			echo '			<a href="?action='.$subaction.'&date=' . $host['date_usage'] . $subdate . '&host=' . $host['id_host'] . '" title="Telemeter total on ' . $host['date_usage'] . ': ' . formatFileSize($host['total_telemeter']) . '">' . "\n";
			//if($current_row == 0 || $current_row % 5 == 0 || $current_row == $rows - 1) 
			{
				echo '				<span class="label">' . $host['date_usage_label'] . '</span>' . "\n";
			}
			echo '				<span class="count" style="height: ' . $pct_total_tm . '%">(' . formatFileSize($host['total_telemeter']) . ')</span>' . "\n";
			echo '			</a>' . "\n";
			echo '		</li>' . "\n";
		}
	}
	if($show == 'down' || $show == 'both' || $show == 'all'){
		echo '		<li class="usage-down ' . $class_down . '">' . "\n";
		echo '			<a href="?action='.$subaction.'&date=' . $host['date_usage'] . $subdate . '&host=' . $host['id_host'] . '" title="Downloaded on ' . $host['date_usage'] . ': ' . formatFileSize($host['downloaded']) . '">' . "\n";
		//if($current_row == 0 || $current_row % 5 == 0 || $current_row == $rows - 1) 
		if($show != 'all')
        {
			echo '				<span class="label">' . $host['date_usage_label'] . '</span>' . "\n";
		}
		echo '				<span class="count" style="height: ' . $pct_down . '%">(' . formatFileSize($host['downloaded']) . ')</span>' . "\n";
		echo '			</a>' . "\n";
		echo '		</li>' . "\n";
		
		if($tm == 1){
			echo '		<li class="usage-down ' . $class_down . '">' . "\n";
			echo '			<a href="?action='.$subaction.'&date=' . $host['date_usage'] . $subdate . '&host=' . $host['id_host'] . '" title="Telemeter downloaded on ' . $host['date_usage'] . ': ' . formatFileSize($host['downloaded_telemeter']) . '">' . "\n";
			//if($current_row == 0 || $current_row % 5 == 0 || $current_row == $rows - 1) 
			if($show != 'all')
			{
				echo '				<span class="label">' . $host['date_usage_label'] . '</span>' . "\n";
			}
			echo '				<span class="count" style="height: ' . $pct_down_tm . '%">(' . formatFileSize($host['downloaded_telemeter']) . ')</span>' . "\n";
			echo '			</a>' . "\n";
			echo '		</li>' . "\n";
		}
	}
	if($show == 'up' || $show == 'both' || $show == 'all'){
		echo '		<li class="usage-up ' . $class_up . '">' . "\n";
		echo '			<a href="?action='.$subaction.'&date=' . $host['date_usage'] . $subdate . '&host=' . $host['id_host'] . '" title="Uploaded on ' . $host['date_usage'] . ': ' . formatFileSize($host['uploaded']) . '">' . "\n";
		//if($current_row == 0 || $current_row % 5 == 0 || $current_row == $rows - 1) 
		if($show != 'all')
        {
			echo '				<span class="label">' . $host['date_usage_label'] . '</span>' . "\n";
		}
		echo '				<span class="count" style="height: ' . $pct_up . '%">(' . formatFileSize($host['uploaded']) . ')</span>' . "\n";
		echo '			</a>' . "\n";
		echo '		</li>' . "\n";
		
		if($tm == 1){
			echo '		<li class="usage-up ' . $class_up . '">' . "\n";
			echo '			<a href="?action='.$subaction.'&date=' . $host['date_usage'] . $subdate . '&host=' . $host['id_host'] . '" title="Telemeter uploaded on ' . $host['date_usage'] . ': ' . formatFileSize($host['uploaded_telemeter']) . '">' . "\n";
			//if($current_row == 0 || $current_row % 5 == 0 || $current_row == $rows - 1) 
			if($show != 'all')
			{
				echo '				<span class="label">' . $host['date_usage_label'] . '</span>' . "\n";
			}
			echo '				<span class="count" style="height: ' . $pct_up_tm . '%">(' . formatFileSize($host['uploaded_telemeter']) . ')</span>' . "\n";
			echo '			</a>' . "\n";
			echo '		</li>' . "\n";
		}
	}
	
	if($show == 'both' || $show == 'all' || $tm == 1){
		echo '		<li class="usage-spacer">' . "\n";
	}
	
	/*if(date resetted){
		echo '		<li class="usage-period-spacer">' . "\n";
	}*/
	
	$current_row++;
}
echo		 '	</ul>' . "\n";
echo		 '</div>' . "\n";


// total overview

$total_downloaded = 0;
$total_uploaded = 0;
$total_downloaded_tm = 0;
$total_uploaded_tm = 0;

$current_row = 0;
$rows = mysqli_num_rows($qry_totals);

echo '<div class="section" style="width:' . $section_width . 'px;">' . "\n";
echo '	<div class="section-label"><a id="host-1"></a><strong>Totals</strong></div>' . "\n";
echo '	<ul class="timeline">' . "\n";
while($host = mysqli_fetch_array($qry_totals)){
	$total_downloaded += $host['downloaded'];
	$total_uploaded += $host['uploaded'];
	$total_downloaded_tm += $host['downloaded_telemeter'];
	$total_uploaded_tm += $host['uploaded_telemeter'];
	
	$pct_total	= $host['total'] * 100 / $maxval['max_'.$show];
	$pct_down	= $host['downloaded'] * 100 / $maxval['max_'.$show];
	$pct_up		= $host['uploaded'] * 100 / $maxval['max_'.$show];
	
	$pct_total_tm	= $host['total_telemeter'] * 100 / $maxval['max_'.$show];
	$pct_down_tm	= $host['downloaded_telemeter'] * 100 / $maxval['max_'.$show];
	$pct_up_tm		= $host['uploaded_telemeter'] * 100 / $maxval['max_'.$show];
	
	$class_total	= 'total' . ($host['total'] == 0 ? -1 : round($pct_total, -1)/10);
	$class_down		= 'down' . ($host['downloaded'] == 0 ? -1 : round($pct_down, -1)/10) . ' ' . ($show == 'all' ? 'sm' : '');
	$class_up		= 'up' . ($host['uploaded'] == 0 ? -1 : round($pct_up, -1)/10) . ' ' . ($show == 'all' ? 'sm' : '');
	
    //echo '<!--' . $host['total'] .'/'. $maxval['max_total'] . '-->';
	if($show == 'total' || $show == 'all'){
		echo '		<li class="usage-total ' . $class_total . '">' . "\n";
		echo '			<a href="?action='.$subaction.'&date=' . $host['date_usage'] . $subdate . '&host=-1" title="Total on ' . $host['date_usage'] . ': ' . formatFileSize($host['total']) . '">' . "\n";
		//if($current_row == 0 || $current_row % 5 == 0 || $current_row == $rows - 1) 
		if($show != 'all')
        {
			echo '				<span class="label">' . $host['date_usage_label'] . '</span>' . "\n";
		}
		echo '				<span class="count" style="height: ' . $pct_total . '%">(' . formatFileSize($host['total']) . ')</span>' . "\n";
		echo '			</a>' . "\n";
		echo '		</li>' . "\n";
		
		if($tm == 1){
			echo '		<li class="usage-total ' . $class_total . '">' . "\n";
			echo '			<a href="?action='.$subaction.'&date=' . $host['date_usage'] . $subdate . '&host=-1" title="Telemeter total on ' . $host['date_usage'] . ': ' . formatFileSize($host['total_telemeter']) . '">' . "\n";
			//if($current_row == 0 || $current_row % 5 == 0 || $current_row == $rows - 1) 
			if($show != 'all')
			{
				echo '				<span class="label">' . $host['date_usage_label'] . '</span>' . "\n";
			}
			echo '				<span class="count" style="height: ' . $pct_total_tm . '%">(' . formatFileSize($host['total_telemeter']) . ')</span>' . "\n";
			echo '			</a>' . "\n";
			echo '		</li>' . "\n";
		}
	}
	if($show == 'down' || $show == 'both' || $show == 'all'){
		echo '		<li class="usage-down ' . $class_down . '">' . "\n";
		echo '			<a href="?action='.$subaction.'&date=' . $host['date_usage'] . $subdate . '&host=-1" title="Downloaded on ' . $host['date_usage'] . ': ' . formatFileSize($host['downloaded']) . '">' . "\n";
		//if($current_row == 0 || $current_row % 5 == 0 || $current_row == $rows - 1) 
		if($show != 'all')
        {
			echo '				<span class="label">' . $host['date_usage_label'] . '</span>' . "\n";
		}
		echo '				<span class="count" style="height: ' . $pct_down . '%">(' . formatFileSize($host['downloaded']) . ')</span>' . "\n";
		echo '			</a>' . "\n";
		echo '		</li>' . "\n";
		
		if($tm == 1){
			echo '		<li class="usage-down ' . $class_down . '">' . "\n";
			echo '			<a href="?action='.$subaction.'&date=' . $host['date_usage'] . $subdate . '&host=-1" title="Telemeter downloaded on ' . $host['date_usage'] . ': ' . formatFileSize($host['downloaded_telemeter']) . '">' . "\n";
			//if($current_row == 0 || $current_row % 5 == 0 || $current_row == $rows - 1) 
			if($show != 'all')
			{
				echo '				<span class="label">' . $host['date_usage_label'] . '</span>' . "\n";
			}
			echo '				<span class="count" style="height: ' . $pct_down_tm . '%">(' . formatFileSize($host['downloaded_telemeter']) . ')</span>' . "\n";
			echo '			</a>' . "\n";
			echo '		</li>' . "\n";
		}
	}
	if($show == 'up' || $show == 'both' || $show == 'all'){
		echo '		<li class="usage-up ' . $class_up . '">' . "\n";
		echo '			<a href="?action='.$subaction.'&date=' . $host['date_usage'] . $subdate . '&host=-1" title="Uploaded on ' . $host['date_usage'] . ': ' . formatFileSize($host['uploaded']) . '">' . "\n";
		//if($current_row == 0 || $current_row % 5 == 0 || $current_row == $rows - 1) 
        {
			echo '				<span class="label">' . $host['date_usage_label'] . '</span>' . "\n";
		}
		echo '				<span class="count" style="height: ' . $pct_up	. '%">(' . formatFileSize($host['uploaded']) . ')</span>' . "\n";
		echo '			</a>' . "\n";
		echo '		</li>' . "\n";
		
		if($tm == 1){
			echo '		<li class="usage-up ' . $class_up . '">' . "\n";
			echo '			<a href="?action='.$subaction.'&date=' . $host['date_usage'] . $subdate . '&host=-1" title="Telemeter uploaded on ' . $host['date_usage'] . ': ' . formatFileSize($host['uploaded_telemeter']) . '">' . "\n";
			//if($current_row == 0 || $current_row % 5 == 0 || $current_row == $rows - 1) 
			{
				echo '				<span class="label">' . $host['date_usage_label'] . '</span>' . "\n";
			}
			echo '				<span class="count" style="height: ' . $pct_up_tm	. '%">(' . formatFileSize($host['uploaded_telemeter']) . ')</span>' . "\n";
			echo '			</a>' . "\n";
			echo '		</li>' . "\n";
		}
	}
	
	if($show == 'both' || $show == 'all' || $tm == 1){
		echo '		<li class="usage-spacer">' . "\n";
	}
	
	/*if(date resetted){
		echo '		<li class="usage-period-spacer">' . "\n";
	}*/
	
	$current_row++;
}
echo '	</ul>' . "\n";
echo '</div>' . "\n";

?>

<div class="clearfix"></div>

<h4>Total (in period): </h4>
<p>
Down:	<?= formatFileSize($total_downloaded) ?><br/>
Up:	<?= formatFileSize($total_uploaded) ?><br/>
Total:	<?= formatFileSize($total_downloaded+$total_uploaded) ?><br/>
------<br/>
Telemeter Down:	<?= formatFileSize($total_downloaded_tm) ?><br/>
Telemeter Up:	<?= formatFileSize($total_uploaded_tm) ?><br/>
Telemeter total:	<?= formatFileSize($total_downloaded_tm+$total_uploaded_tm) ?><br/>
</p>
