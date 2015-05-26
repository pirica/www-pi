
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

$maxval = mysql_fetch_array($qry_max);


// hosts overview

$current_row = 0;
$current_host = -1;
$rows = mysql_num_rows($qry_hosts);

while($host = mysql_fetch_array($qry_hosts)){
	if($current_host != $host['id_host']){
		$current_host = $host['id_host'];
		if($current_row > 0){
			echo '	</ul>' . "\n";
			echo '</div>' . "\n";
		}
		echo '<div class="section">' . "\n";
		echo '	<div class="section-label">' . $host['hostname'] . '</div>' . "\n";
		echo '	<ul class="timeline">' . "\n";
	}
	
    //echo '<!--' . $host['total'] .'/'. $maxval['max_total'] . '-->';
	
	
	
	if($show == 'total' || $show == 'all'){
		echo '		<li class="usage-total total' . ($host['total'] == 0 ? -1 : round($host['total'], -1)) . '">' . "\n";
		echo '			<a href="?action='.$action->getCode().'&date=' . $host['date_usage'] . '" title="' . $host['date_usage'] . ': ' . formatFileSize($host['total']) . '">' . "\n";
		//if($current_row == 0 || $current_row % 5 == 0 || $current_row == $rows - 1) 
        {
			echo '				<span class="label">' . $host['date_usage_label'] . '</span>' . "\n";
		}
		echo '				<span class="count" style="height: ' . ($host['total'] * 100 / $maxval['max_'.$show]) . '%">(' . formatFileSize($host['total']) . ')</span>' . "\n";
		echo '			</a>' . "\n";
		echo '		</li>' . "\n";
	}
	if($show == 'down' || $show == 'both' || $show == 'all'){
		echo '		<li class="usage-down down' . round($host['downloaded'], -1) . ' ' . ($show == 'all' ? 'sm' : '') . '">' . "\n";
		echo '			<a href="?action='.$action->getCode().'&date=' . $host['date_usage'] . '" title="' . $host['date_usage'] . ': ' . formatFileSize($host['downloaded']) . '">' . "\n";
		//if($current_row == 0 || $current_row % 5 == 0 || $current_row == $rows - 1) 
        {
			echo '				<span class="label">' . $host['date_usage_label'] . '</span>' . "\n";
		}
		echo '				<span class="count" style="height: ' . ($host['downloaded'] * 100 / $maxval['max_'.$show]) . '%">(' . formatFileSize($host['downloaded']) . ')</span>' . "\n";
		echo '			</a>' . "\n";
		echo '		</li>' . "\n";
	}
	if($show == 'up' || $show == 'both' || $show == 'all'){
		echo '		<li class="usage-up up' . round($host['uploaded'], -1) . ' ' . ($show == 'all' ? 'sm' : '') . '">' . "\n";
		echo '			<a href="?action='.$action->getCode().'&date=' . $host['date_usage'] . '" title="' . $host['date_usage'] . ': ' . formatFileSize($host['uploaded']) . '">' . "\n";
		//if($current_row == 0 || $current_row % 5 == 0 || $current_row == $rows - 1) 
        {
			echo '				<span class="label">' . $host['date_usage_label'] . '</span>' . "\n";
		}
		echo '				<span class="count" style="height: ' . ($host['uploaded'] * 100 / $maxval['max_'.$show]) . '%">(' . formatFileSize($host['uploaded']) . ')</span>' . "\n";
		echo '			</a>' . "\n";
		echo '		</li>' . "\n";
	}
	
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
$rows = mysql_num_rows($qry_totals);

echo '<div class="section">' . "\n";
echo '	<div class="section-label"><strong>Totals</strong></div>' . "\n";
echo '	<ul class="timeline">' . "\n";
while($host = mysql_fetch_array($qry_totals)){
	$total_downloaded += $host['downloaded'];
	$total_uploaded += $host['uploaded'];
	$total_downloaded_tm += $host['downloaded_telemeter'];
	$total_uploaded_tm += $host['uploaded_telemeter'];
	
    //echo '<!--' . $host['total'] .'/'. $maxval['max_total'] . '-->';
	if($show == 'total' || $show == 'all'){
		echo '		<li class="usage-total total' . round($host['total'], -1) . '">' . "\n";
		echo '			<a href="?action='.$action->getCode().'&date=' . $host['date_usage'] . '" title="' . $host['date_usage'] . ': ' . formatFileSize($host['total']) . '">' . "\n";
		//if($current_row == 0 || $current_row % 5 == 0 || $current_row == $rows - 1) 
        {
			echo '				<span class="label">' . $host['date_usage_label'] . '</span>' . "\n";
		}
		echo '				<span class="count" style="height: ' . ($host['total'] * 100 / $maxval['max_'.$show]) . '%">(' . formatFileSize($host['total']) . ')</span>' . "\n";
		echo '			</a>' . "\n";
		echo '		</li>' . "\n";
	}
	if($show == 'down' || $show == 'both' || $show == 'all'){
		echo '		<li class="usage-down down' . round($host['downloaded'], -1) . ' ' . ($show == 'all' ? 'sm' : '') . '">' . "\n";
		echo '			<a href="?action='.$action->getCode().'&date=' . $host['date_usage'] . '" title="' . $host['date_usage'] . ': ' . formatFileSize($host['downloaded']) . '">' . "\n";
		//if($current_row == 0 || $current_row % 5 == 0 || $current_row == $rows - 1) 
        {
			echo '				<span class="label">' . $host['date_usage_label'] . '</span>' . "\n";
		}
		echo '				<span class="count" style="height: ' . ($host['downloaded'] * 100 / $maxval['max_'.$show]) . '%">(' . formatFileSize($host['downloaded']) . ')</span>' . "\n";
		echo '			</a>' . "\n";
		echo '		</li>' . "\n";
	}
	if($show == 'up' || $show == 'both' || $show == 'all'){
		echo '		<li class="usage-up up' . round($host['uploaded'], -1) . ' ' . ($show == 'all' ? 'sm' : '') . '">' . "\n";
		echo '			<a href="?action='.$action->getCode().'&date=' . $host['date_usage'] . '" title="' . $host['date_usage'] . ': ' . formatFileSize($host['uploaded']) . '">' . "\n";
		//if($current_row == 0 || $current_row % 5 == 0 || $current_row == $rows - 1) 
        {
			echo '				<span class="label">' . $host['date_usage_label'] . '</span>' . "\n";
		}
		echo '				<span class="count" style="height: ' . ($host['uploaded'] * 100 / $maxval['max_'.$show]) . '%">(' . formatFileSize($host['uploaded']) . ')</span>' . "\n";
		echo '			</a>' . "\n";
		echo '		</li>' . "\n";
	}
	
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
