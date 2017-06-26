<?php
set_time_limit(3600);
require dirname(__FILE__).'/../connections.php';
require dirname(__FILE__).'/../functions.php';
require dirname(__FILE__).'/../../_core/appinit.php';

if(!$task->getIsRunning())
{
	$task->setIsRunning(true);
	
	mysqli_query($conn, "
		
		insert into t_grab_file (id_grab, full_url, full_path) 
		select 
			g.id_grab, 
			case when ff.url_original = 'null' then ff.url_large else ff.url_original end as full_url,  
			concat(g.path, ff.imageset, '/', ff.filename) as full_path 
		from t_flickr_file ff 
			join t_grab g on g.active = 1 and g.description like '%flickr%' 
			left join t_grab_file gf on gf.full_url = case when ff.url_original = 'null' then ff.url_large else ff.url_original end 
		where  
			case when ff.url_original = 'null' then ff.url_large else ff.url_original end <> 'null'  
			and gf.id_grab_file is null 
			and ff.filename <> '' 
		group by
			g.id_grab, 
			case when ff.url_original = 'null' then ff.url_large else ff.url_original end,  
			concat(g.path, ff.imageset, '/', ff.filename)
			
		");
		
	mysqli_query($conn, "truncate table t_grab_file_temp");
		
	$qry_grabs = mysqli_query($conn, "
		
		select
			g.id_grab,
			g.url,
			g.path,
			g.filename,
			g.description
		from t_grab g
		where
			g.active = 1
			and g.enabled = 1
			#and ifnull(g.files_building, 0) = 0
			
		");

	while ($grabs = mysqli_fetch_array($qry_grabs)) {
		
		echo 'Grab ' . $grabs['description'] . ' (ID:' .  $grabs['id_grab'] . ")\n";
		echo ' -> started on ' . date('Y-m-d H:i:s', time()) . "\n";
		
		// mark as 'files building'
		mysqli_query($conn, "update t_grab set files_building = 1 where id_grab = " . $grabs['id_grab'] . "");
		
		
		// generate new file records
		$qry_grab_counters = mysqli_query($conn, "
			
			select
				gc.id_grab_counter,
				
				case gc.type
					when 'int' then gc.intto - gc.intfrom
					when 'list' then (LENGTH(listvalues) - LENGTH(REPLACE(listvalues, ',', '')))/LENGTH(',') + 1
					when 'date' then ifnull(gc.dateto, now()) - gc.datefrom
				end as count,
				
				gc.type,
				gc.field,
				
				gc.datefrom,
				gc.dateto,
				gc.datefrom as datevalue,
				
				gc.intfrom,
				gc.intto,
				gc.intfrom as intvalue,
				
				gc.listvalues,
				0 as listindex
				
			from t_grab_counter gc
			where
				gc.id_grab = " . $grabs['id_grab'] . "
				and gc.active = 1
				
			order by
				ifnull(gc.sort_order, gc.id_grab_counter)
				
			");
		
		$grab_counters = [];
		$files = [];
		$totalcount = 1;
		while ($grab_counter = mysqli_fetch_array($qry_grab_counters)) {
			$grab_counters[] = $grab_counter;
			//if($grab_counters['count'] > 0){
				$totalcount *= $grab_counter['count'];
			//}
		}
		
		$grabcount = count($grab_counters);
		
		if($grabcount > 0){
			for($i=0; $i<$totalcount; $i++){
				$files[] = array(
					'full_url' => $grabs['url'],
					'full_path' => $grabs['path'] . $grabs['filename'],
					'excluded' => 0
				);
				
				for($c=0; $c<$grabcount; $c++){
					switch($grab_counters[$c]['type']){
						case 'date':
							$files[$i]['full_url'] = str_replace($grab_counters[$c]['field'], $grab_counters[$c]['datevalue'], $files[$i]['full_url']);
							$files[$i]['full_path'] = str_replace($grab_counters[$c]['field'], $grab_counters[$c]['datevalue'], $files[$i]['full_path']);
							break;
						
						case 'int':
							$files[$i]['full_url'] = str_replace($grab_counters[$c]['field'], $grab_counters[$c]['intvalue'], $files[$i]['full_url']);
							$files[$i]['full_path'] = str_replace($grab_counters[$c]['field'], $grab_counters[$c]['intvalue'], $files[$i]['full_path']);
							break;
						
						case 'list':
							$listvalues = explode(',', $grab_counters[$c]['listvalues']);
							
							$files[$i]['full_url'] = str_replace($grab_counters[$c]['field'], $listvalues[$grab_counters[$c]['listindex']], $files[$i]['full_url']);
							$files[$i]['full_path'] = str_replace($grab_counters[$c]['field'], $listvalues[$grab_counters[$c]['listindex']], $files[$i]['full_path']);
							break;
					}
					
				}
				
				increase_counter($grab_counters, 0);
				
			}
			
			echo ' -> ' . count($files) . " file URLs generated\n";
			//var_dump($files);

			$filecount = count($files);
			
			$insertcount = 0;
			$qry_insert = '';
			
			// generate files
			for($i=0; $i<$filecount; $i++){
				$qry_insert = "insert into t_grab_file_temp (id_grab, full_url, full_path) values ";
				$qry_insert .= "(" . $grabs['id_grab'] . ", '" . mysqli_real_escape_string($conn, $files[$i]['full_url']) . "', '" . mysqli_real_escape_string($conn, $files[$i]['full_path']) . "')";
				
				$insertcount++;
				
				mysqli_query($conn, $qry_insert);
				$qry_insert = '';
				
			}
			
			echo ' -> ' . $insertcount . " file URLs inserted\n";
			
		}
		else {
			echo " -> No counters present\n";
		}
		
		// unmark as 'files building'
		mysqli_query($conn, "update t_grab set files_building = 0 where id_grab = " . $grabs['id_grab'] . "");
		
		
		// update grab stats
		//include 'queries/pr_set_grab_stats.php';
		
		
		echo ' -> completed on ' . date('Y-m-d H:i:s', time()) . "\n";
		echo "\n";
		
	}



	mysqli_query($conn, "
		
		insert into t_grab_file (id_grab, full_url, full_path) 
		select 
			ff.id_grab, 
			ff.full_url,  
			ff.full_path 
		from t_grab_file_temp ff 
			left join t_grab_file gf on gf.full_url = ff.full_url and gf.id_grab = ff.id_grab
		where  
			gf.id_grab_file is null 
		
		");
	
	$task->setIsRunning(false);
	
}
?>