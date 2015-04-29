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

$result = [];
while($host = mysql_fetch_array($qry_hosts)){ 
	if($host['active'] == 1){
		
		$result[] = array(
			'id_host' => $host['id_host'],
			'is_new' => ($host['is_new'] == 1 ? '<i class="fa fa-star"></i>' : ''),
			'is_online' => ($host['is_online'] == 1 ? '<i class="fa fa-power-off green"></i>' : '<i class="fa fa-power-off red"></i>'),
			'date_last_seen' => $host['date_last_seen'],
			
			'downloaded_now' => $host['downloaded_now'] == 0 ? '' : '<i class="fa fa-arrow-down"></i>&nbsp;' . str_replace(' ', '&nbsp;', formatFileSize($host['downloaded_now'])),
			'uploaded_now' => $host['uploaded_now'] == 0 ? '' : '<i class="fa fa-arrow-up"></i>&nbsp;' . str_replace(' ', '&nbsp;', formatFileSize($host['uploaded_now'])),
			
			'downloaded_today' => $host['downloaded_today'] == 0 ? '' : '<i class="fa fa-arrow-down"></i>&nbsp;' . str_replace(' ', '&nbsp;', formatFileSize($host['downloaded_today'])),
			'uploaded_today' => $host['uploaded_today'] == 0 ? '' : '<i class="fa fa-arrow-up"></i>&nbsp;' . str_replace(' ', '&nbsp;', formatFileSize($host['uploaded_today'])),
			
			'downloaded_month' => $host['downloaded_month'] == 0 ? '' : '<i class="fa fa-arrow-down"></i>&nbsp;' . str_replace(' ', '&nbsp;', formatFileSize($host['downloaded_month'])),
			'uploaded_month' => $host['uploaded_month'] == 0 ? '' : '<i class="fa fa-arrow-up"></i>&nbsp;' . str_replace(' ', '&nbsp;', formatFileSize($host['uploaded_month']))
			
		);
	}
}

echo json_encode(array('data' => $result, 'date' => date('Y-m-d H:i:s', time())) );
?>