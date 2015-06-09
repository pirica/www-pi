
<h1><?= $app->getTitle() ?></h1>



<?php 

for ($i = 0; $i < $cameracount; $i++) {
	if($id_camera <= 0 || $id_camera == $cameras[$i]['id_camera']){
		switch($cameras[$i]['type']){
			case 'raspi':
			case 'raspicam':
			case 'raspimjpg':
			case 'raspimjpeg':
				if($id_camera <= 0){
					echo $cameras[$i]['description'] . '<br>';
				}
				echo '<img class="raspimjpeg" data-address="' . $cameras[$i]['address'] . '" />';
				break;
			
			//case 'mjpg':
			//case 'mjpeg':
			default:
				if($id_camera <= 0){
					echo $cameras[$i]['description'] . '<br>';
				}
				echo '<img class="mjpeg" src="' . $cameras[$i]['address'] . '">';
		}
	}
}

?>

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
