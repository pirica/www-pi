<?php
set_time_limit(0);

require dirname(__FILE__).'/../../_core/appinit.php';

require dirname(__FILE__).'/../connection.php';
require dirname(__FILE__).'/../functions.php';

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
				and date >= date_format(now() - interval 1 day, '%Y%m%d')
			
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
		
		$gifname = $main_dir . $camera_log['date'] . '' . str_replace('.avi', '.gif', $camera_log['name']);
		$pallete = '/dev/shm/palette' . str_replace('.avi', '.png', $camera_log['name']);
		
		if(strtolower($extension) == '.avi'){
			if(
				!file_exists($gifname)
				||
				(filemtime($gifname) < filemtime($main_dir . $camera_log['date'] . '/' . $camera_log['name'])
			){
				
				shell_exec('ffmpeg -y -i ' . $main_dir . $camera_log['date'] . '/' . $camera_log['name'] . ' -vf fps=2,scale='.$thumbWidth.':-1:flags=lanczos,palettegen '.$pallete);
				shell_exec('ffmpeg -i ' . $main_dir . $camera_log['date'] . '/' . $camera_log['name'] . ' -i '.$pallete.' -filter_complex "fps=2,scale='.$thumbWidth.':-1:flags=lanczos[x];[x][1:v]paletteuse" ' . $gifname);
				
				//gifsicle -i /var/docs/motion/20170629/20170629_133203_backdoor_09.gif -O3 -o /var/docs/motion/20170629/20170629_133203_backdoor_09b.gif
				//gifsicle -i /var/docs/motion/20170629/20170629_133203_backdoor_09.gif -O3 --colors 256 -o /var/docs/motion/20170629/20170629_133203_backdoor_09b.gif
				
				//ffmpeg -i /var/docs/motion/20170629/20170629_133203_backdoor_09.avi -r 10 -f image2pipe -vcodec ppm - | convert -delay 10 - gif:- | convert -layers Optimize - /var/docs/motion/20170629/20170629_133203_backdoor_09d.gif
				
			}
		}
	}

	$task->setIsRunning(false);
}

?>