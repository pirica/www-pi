<div class="wide">

<h1>Configure hosts</h1>
<p>
	<form method="get" action="?action=<?= $action->getCode() ?>">
		<input type="hidden" name="action" value="<?= $action->getCode() ?>">
		
		<input type="checkbox" id="filter_all" name="all" value="1" <?= ($show_all == 1 ? 'checked="checked"' : '') ?>/>
		<label for="filter_all">Show all</label>
		
	<form>
</p>

<table class="table" id="hosts-config-grid">
	<thead>
		<tr>
			<th>Host</th>
			<th>Category</th>
			<th>IP Address</th>
			<th>MAC Address</th>
			
			<th>Show in overview</th>
			<th>Announce status</th>
			<th>Confirm new</th>
			
			<th>Active</th>
			
			<th>Alert daily usage</th>
			<th>Alert monthly usage</th>
		</tr>
	</thead>
	
	<tbody>
	<?php 
	/*
		h.id_host,
		h.ip_address,
		h.mac_address,
		h.hostname,
		h.hostname_friendly,
		
		h.is_online,
		h.date_last_seen,
		
		h.is_new,
		
		h.device_type,
		h.os,
		h.os_details,
		
		h.id_category,
		c.description as category
		
	*/
	while($host = mysqli_fetch_array($qry_hosts)){ 
		if($host['active'] == 1 || $show_all == 1){
		?>
			<tr class="tr-host<?=$host['id_host'] ?>" data-id_host="<?=$host['id_host'] ?>">
				<td>
					<?php
						echo '<input type="text" name="hostname_friendly" value="' . $host['hostname_lbl'] . '">';
						//echo '<span class="is_new"><i class="fa ' . ($host['is_new'] == 1 ? 'fa-star' : '') . '"></i></span>';
					?>
				</td>
				<td>
					<select name="id_category">
						<option value="-1">...</option>
						<?php
							while($cat = mysqli_fetch_array($qry_categories)){ 
								echo '<option value="' .  $cat['id_category'] . '" ' . ($cat['id_category'] == $host['id_category'] ? 'selected="selected"' : '') . '>' . $cat['category'] . '</option>';
							}
							mysqli_data_seek($qry_categories, 0);
						?>
					</select>
				</td>
				<td><?=$host['ip_address'] ?></td>
				<td><?=$host['mac_address'] ?></td>
				
				<td>
					<?php
						echo '<input type="checkbox" name="show_overview" ' . ($host['show_overview'] == 1 ? 'checked="checked"' : '') . '>';
					?>
				</td>
				<td>
					<?php
						echo '<input type="checkbox" name="announce_status" ' . ($host['announce_status'] == 1 ? 'checked="checked"' : '') . '>';
					?>
				</td>
				<td>
					<?php
						echo '<input type="checkbox" name="is_new" ' . ($host['is_new'] == 1 ? 'checked="checked"' : '') . '>';
					?>
				</td>
				
				<td>
					<?php
						echo '<input type="checkbox" name="active" ' . ($host['active'] == 1 ? 'checked="checked"' : '') . '>';
					?>
				</td>
				
				<td>
					<?php
						echo '<input type="text" name="alert_when_traffic_exceeds_daily" value="' . str_replace(' ', '', str_replace('B', '', strtoupper(formatFileSize($host['alert_when_traffic_exceeds_daily'],0)))) . '">';
					?>
				</td>
				<td>
					<?php
						echo '<input type="text" name="alert_when_traffic_exceeds_monthly" value="' . str_replace(' ', '', str_replace('B', '', strtoupper(formatFileSize($host['alert_when_traffic_exceeds_monthly'],0)))) . '">';
					?>
				</td>
				
			</tr>
		<?php 
		}
	}
	?>
	</tbody>
	
</table>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>


</div>