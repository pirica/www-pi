

<h1>Overview</h1>

<table class="table" id="hosts-grid">
	<thead>
		<tr>
			<th colspan="6">Status:</th>
			
			<th colspan="4">Traffic:</th>
            
			<th colspan="2"><div id="date-last-update"><?= date('Y-m-d H:i:s', time()) ?></div></th>
		</tr>
		
		<tr>
			<th>Host</th>
			<th>Category</th>
			<th>IP Address</th>
			<th>MAC Address</th>
			<th>Online</th>
			<th>Last seen</th>
			
			<th colspan="2">Now</th>
			<th colspan="2">Today</th>
			<th colspan="2">This month</th>
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
		
		h.downloaded_now,
		h.uploaded_now,
		h.downloaded_today,
		h.uploaded_today,
		h.downloaded_month,
		h.uploaded_month
		
		h.id_category,
		c.description as category
		
	*/
	while($host = mysql_fetch_array($qry_hosts)){ 
		if($host['active'] == 1 && $host['show_overview'] == 1){
		?>
			<tr class="tr-host<?=$host['id_host'] ?>" data-id_host="<?=$host['id_host'] ?>">
				<td><?php
					echo $host['hostname_lbl'];
					echo '<span class="is_new"><i class="fa ' . ($host['is_new'] == 1 ? 'fa-star' : '') . '"></i></span>';
				?></td>
				<td><?=$host['category'] ?></td>
				<td><?=$host['ip_address'] ?></td>
				<td><?=$host['mac_address'] ?></td>
				<td class="is_online"><?php
					echo '<i class="fa ' . ($host['is_online'] == 1 ? 'fa-power-off green' : 'fa-power-off red') . '"></i>';
				?></td>
				<td class="date_last_seen"><?= $host['date_last_seen'] ?></td>
				
				<td class="downloaded "><span class="downloaded_now"><?= $host['downloaded_now'] == 0 ? '' : '<i class="fa fa-arrow-down"></i>&nbsp;' . str_replace(' ', '&nbsp;', formatFileSize($host['downloaded_now'])) ?></span></td>
				<td class="uploaded "><span class="uploaded_now"><?= $host['uploaded_now'] == 0 ? '' : '<i class="fa fa-arrow-up"></i>&nbsp;' . str_replace(' ', '&nbsp;', formatFileSize($host['uploaded_now'])) ?></span></td>
				
				<td class="downloaded "><span class="downloaded_today"><?= $host['downloaded_today'] == 0 ? '' : '<i class="fa fa-arrow-down"></i>&nbsp;' . str_replace(' ', '&nbsp;', formatFileSize($host['downloaded_today'])) ?></span></td>
				<td class="uploaded "><span class="uploaded_today"><?= $host['uploaded_today'] == 0 ? '' : '<i class="fa fa-arrow-up"></i>&nbsp;' . str_replace(' ', '&nbsp;', formatFileSize($host['uploaded_today'])) ?></span></td>
				
				<td class="downloaded "><span class="downloaded_month"><?= $host['downloaded_month'] == 0 ? '' : '<i class="fa fa-arrow-down"></i>&nbsp;' . str_replace(' ', '&nbsp;', formatFileSize($host['downloaded_month'])) ?></span></td>
				<td class="uploaded "><span class="uploaded_month"><?= $host['uploaded_month'] == 0 ? '' : '<i class="fa fa-arrow-up"></i>&nbsp;' . str_replace(' ', '&nbsp;', formatFileSize($host['uploaded_month'])) ?></span></td>
				
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
