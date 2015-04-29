<?php
$result = [];
while($cam = mysql_fetch_array($qry_cameras)){ 
	
	$result[] = array(
		'id_camera' => $cam['id_camera'],
		'address' => $cam['address'],
        'address_fallback' => $cam['address_fallback'],
        'type' => $cam['type'],
        'description' => $cam['description'],
        'is_local' => $cam['is_local']
		
	);
	
}

echo json_encode(array('data' => $result, 'date' => date('Y-m-d H:i:s', time())) );
?>