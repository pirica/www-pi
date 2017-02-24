<?php
set_time_limit(3600);

$path = dirname(__FILE__);

// directory handle
$dir = dir($path);

while(false !== ($entry = $dir->read()))
{
	if($entry != '.' && $entry != '..')
	{
		if(is_dir($path . '/' .$entry) && file_exists($path . '/' .$entry . '/build_menu_items.php'))
		{
			shell_exec('/usr/bin/php ' . $path . '/' .$entry . '/build_menu_items.php');
		}
	}
}

?>