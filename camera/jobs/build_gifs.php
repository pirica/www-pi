<?php
set_time_limit(0);

require dirname(__FILE__).'/../../_core/appinit.php';

require dirname(__FILE__).'/../connection.php';
require dirname(__FILE__).'/../functions.php';

require dirname(__FILE__).'/../../_core/components/images/GIFEncoder.class.php';

if(!$task->getIsRunning())
{
	$task->setIsRunning(true);
	
	if(!is_dir("/dev/shm/palette")) mkdir("/dev/shm/palette");
	
	
	$qry_camera_log = mysqli_query($conn, "
			
			select
				cl.id_camera_log,
				cl.date,
				cl.time,
				cl.hour_lbl,
				cl.time_value,
				cl.time_value_gif,
				cl.name,
				cl.camera
				
			from t_camera_log cl
			where
				cl.name like '%.avi'
			
			order by
				cl.date,
				cl.time_value_gif,
				cl.name
				
				
			");

	$thumbWidth = 320;

	$gifname = '';
	$pallete = '';

	while($camera_log = mysqli_fetch_array($qry_camera_log)){
		$extarr = explode('.', $camera_log['name']);
		$extension = '.' . $extarr[count($extarr) - 1];
		
		$gifname = $main_dir . $camera_log['date'] . '/' . str_replace('.avi', '.gif', $camera_log['name']);
		$pallete = $camera_log['name'] . '.png';
		
		if(strtolower($extension) == '.avi'){
			if(!file_exists($gifname)){
				
				shell_exec('ffmpeg -y -i ' . $main_dir . $camera_log['date'] . '/' . $camera_log['name'] . ' -vf fps=5,scale='.$thumbWidth.':-1:flags=lanczos,palettegen /dev/shm/palette/pallete_'.$pallete.'.png');
				shell_exec('ffmpeg -i ' . $main_dir . $camera_log['date'] . '/' . $camera_log['name'] . ' -i /dev/shm/palette/pallete_'.$pallete.'.png'.' -filter_complex "fps=5,scale='.$thumbWidth.':-1:flags=lanczos[x];[x][1:v]paletteuse" ' . $thumbnail);
				
				
			}
		}
	}

	$task->setIsRunning(false);
}

?>