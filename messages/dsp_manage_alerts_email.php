

<h1>Email alerts</h1>

<table class="table" id="hosts-grid">
	<thead>
		<tr>
			<th>ID</th>
			<th>Description</th>
			<th>When from</th>
			<th>When subject</th>
			<th>Enabled</th>
			
			<th>Matches</th>
			<th>Date</th>
		</tr>
	</thead>
	
	<tbody>
	<?php 
	$prev_id = -1;
	while($alert = mysql_fetch_array($qry_alerts_email)){ 
		?>
			<tr>
				<?php 
				if($prev_id != $alert['id_alert_email']){
					$prev_id = $alert['id_alert_email'];
				
					?>
					<td><?= $alert['id_alert_email'] ?></td>
					<td><?= $alert['description'] ?></td>
					<td><?= $alert['when_from'] ?></td>
					<td><?= $alert['when_subject'] ?></td>
					<td><?= ('' . ($alert['enabled'] == 1 ? '<i class="fa fa-check"></i>' : '') . '') ?></td>
					<?php
				}
				else {
					?>
					<td><?= '&nbsp;' //$alert['id_alert_email'] ?></td>
					<td><?= '&nbsp;' //$alert['description'] ?></td>
					<td><?= '&nbsp;' //$alert['when_from'] ?></td>
					<td><?= '&nbsp;' //$alert['when_subject'] ?></td>
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
