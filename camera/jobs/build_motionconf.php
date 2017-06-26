<?php

require dirname(__FILE__).'/../../_core/appinit.php';

require 'connection.php';
require 'functions.php';


if(!$task->getIsRunning())
{
	$task->setIsRunning(true);
	
	$motionconf = '';
	$currentconf = '';
	
	$current_camera = '';
	$cameras = 0;
	
	$qry_conf = mysqli_query($conn, "
		select
			c.description,
			mc.code,
			replace(ifnull(cmc.value, mc.default_value),'%camera%', c.description) as value
			
		from t_motion_config mc
			cross join t_camera c
			left join t_camera_motion_config cmc on cmc.code = mc.code and c.id_camera = cmc.id_camera

		where
			c.enable_motion = 1
			and ifnull(cmc.value, mc.default_value) <> ''

		order by
			c.description,
			mc.code
			
		");
	
	while($conf = mysqli_fetch_array($qry_conf)){
		
		if($current_camera != $conf['description'])
		{
			if($currentconf != '' && $current_camera != '')
			{
				file_put_contents('/etc/motion/' . $current_camera . '.conf', $currentconf);
			}
			
			$current_camera = $conf['description'];
			$currentconf = '';
			$motionconf .= 'thread /etc/motion/' . $conf['description'] . '.conf' . "\n";
			$cameras++;
			
		}
		
		$currentconf .= $conf['code'] . ' ' . $conf['value'] . "\n";
		
	}
	if($cameras > 1 && $currentconf != '' && $current_camera != '')
	{
		file_put_contents('/etc/motion/' . $current_camera . '.conf', $currentconf);
	}
	
	if($cameras == 1 && $currentconf != '')
	{
		$motionconf = $currentconf;
	}
	
	if($cameras > 0 && $motionconf != '' )
	{
		file_put_contents('/etc/motion/motion.conf', $motionconf);
		
		shell_exec('service motion restart');
	}
	
	$task->setIsRunning(false);
}

?>