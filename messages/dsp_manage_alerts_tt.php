

<h1>Track &amp; Trace alerts</h1>

<table class="table" id="hosts-grid">
	<thead>
		<tr>
			<th>ID</th>
			<th>Courier</th>
			<th>Tracking code</th>
			<th>Description</th>
			<th>Enabled</th>
			
			<th>History</th>
			<th>Date</th>
		</tr>
	</thead>
	
	<tbody>
	<?php 
	$prev_id = -1;
	while($alert = mysqli_fetch_array($qry_alerts_tt)){ 
		?>
			<tr>
				<?php 
				if($prev_id != $alert['id_tracktrace']){
					$prev_id = $alert['id_tracktrace'];
				
					?>
					<td><?= $alert['id_tracktrace'] ?></td>
					<td><?= $alert['description'] ?></td>
					<td><?= $alert['tracking_code'] ?></td>
					<td><?= $alert['title'] ?></td>
					<td><?= ('' . ($alert['enabled'] == 1 ? '<i class="fa fa-check"></i>' : '') . '') ?></td>
					<?php
				}
				else {
					?>
					<td><?= '&nbsp;' //$alert['id_tracktrace'] ?></td>
					<td><?= '&nbsp;' //$alert['description'] ?></td>
					<td><?= '&nbsp;' //$alert['tracking_code'] ?></td>
					<td><?= '&nbsp;' //$alert['title'] ?></td>
					<td><?= '&nbsp;' //('' . ($alert['enabled'] == 1 ? '<i class="fa fa-check"></i>' : '') . '') ?></td>
					<?php
				}
				?>
				
				
				<td><?= $alert['result'] ?></td>
				<td><?= $alert['date_result'] ?></td>
				
			</tr>
		<?php 
	}
	?>
	</tbody>
	
</table>
