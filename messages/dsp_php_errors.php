

<h1>PHP errors</h1>

<table class="table" id="hosts-grid">
	<thead>
		<tr>
			<th>Date</th>
			<th>Severity</th>
			<th>Message</th>
			<th>Location</th>
			<th>Line nbr</th>
		</tr>
	</thead>
	
	<tbody>
	<?php
	$datacount = count($data);
	for($i=0; $i<$datacount; $i++){ 
		?>
			<tr>
				<td><?= $data[$i]['date'] ?></td>
				<td><?= $data[$i]['severity'] ?></td>
				<td><?= $data[$i]['message'] ?></td>
				<td><?= $data[$i]['location'] ?></td>
				<td><?= $data[$i]['linenbr'] ?></td>
				
			</tr>
		<?php 
	}
	?>
	</tbody>
	
</table>
