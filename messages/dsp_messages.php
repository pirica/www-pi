

<h1>Overview</h1>

<table class="table" id="hosts-grid">
	<thead>
		<tr>
			<th>Sent on</th>
			<th>Host</th>
			<th>Channel</th>
			<th>Title</th>
			<th>Message</th>
			<th>By</th>
		</tr>
	</thead>
	
	<tbody>
	<?php 
	/*
		id_log_message,
		date_sent,
		type,
		host,
		channel,
		title,
		message,
		success
	*/
	
	while($log = mysql_fetch_array($qry_log_messages)){ 
		?>
			<tr>
				<td><?= $log['date_sent'] ?></td>
				<td><?= $log['host'] ?></td>
				<td><?= $log['channel'] ?></td>
				<td><?= $log['title'] ?></td>
				<td><?= $log['message'] ?></td>
				<td><?php
					echo '<!--'.($log['success'] & 2).'-->';
					if(strpos(strtolower($log['type']), 'nma') !== false ){
						echo '<span style="color:#' . (($log['success'] & 2) == 2 ? '0f0' : 'f00') . ';">NMA</span>';
					}
					if(strpos(strtolower($log['type']), 'ortc') !== false ){
						if(strpos(strtolower($log['type']), 'nma') !== false ){
							echo ', ';
						}
						echo '<span style="color:#' . (($log['success'] & 1) == 1 ? '0f0' : 'f00') . ';">ORTC</span>';
					}
				?></td>
				
			</tr>
		<?php 
	}
	?>
	</tbody>
	
</table>
