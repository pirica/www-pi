
<?php
	include 'dsp_detail_grid_pager.php';
?>

<table class="table">
	<thead>
		<tr>
			<th sortfield="full_url">
				URL
				<span class="thsort thsort-full_url-asc glyphicon glyphicon-arrow-up <?php if(!($sort == 'full_url' && $sortorder == 'asc')){ ?>hidden<?php } ?>"></span>
				<span class="thsort thsort-full_url-desc glyphicon glyphicon-arrow-down <?php if(!($sort == 'full_url' && $sortorder == 'desc')){ ?>hidden<?php } ?>"></span>
			</th>
			<th sortfield="full_path">
				Path
				<span class="thsort thsort-full_path-asc glyphicon glyphicon-arrow-up <?php if(!($sort == 'full_path' && $sortorder == 'asc')){ ?>hidden<?php } ?>"></span>
				<span class="thsort thsort-full_path-desc glyphicon glyphicon-arrow-down <?php if(!($sort == 'full_path' && $sortorder == 'desc')){ ?>hidden<?php } ?>"></span>
			</th>
			<th sortfield="status">
				Status
				<span class="thsort thsort-status-asc glyphicon glyphicon-arrow-up <?php if(!($sort == 'status' && $sortorder == 'asc')){ ?>hidden<?php } ?>"></span>
				<span class="thsort thsort-status-desc glyphicon glyphicon-arrow-down <?php if(!($sort == 'status' && $sortorder == 'desc')){ ?>hidden<?php } ?>"></span>
			</th>
			<th sortfield="date_inserted">
				Created
				<span class="thsort thsort-date_inserted-asc glyphicon glyphicon-arrow-up <?php if(!($sort == 'date_inserted' && $sortorder == 'asc')){ ?>hidden<?php } ?>"></span>
				<span class="thsort thsort-date_inserted-desc glyphicon glyphicon-arrow-down <?php if(!($sort == 'date_inserted' && $sortorder == 'desc')){ ?>hidden<?php } ?>"></span>
			</th>
			<th sortfield="date_modified">
				Processed
				<span class="thsort thsort-date_modified-asc glyphicon glyphicon-arrow-up <?php if(!($sort == 'date_modified' && $sortorder == 'asc')){ ?>hidden<?php } ?>"></span>
				<span class="thsort thsort-date_modified-desc glyphicon glyphicon-arrow-down <?php if(!($sort == 'date_modified' && $sortorder == 'desc')){ ?>hidden<?php } ?>"></span>
			</th>
		</tr>
	</thead>
	
	<tbody>
	<?php 
	while($grabfile = mysql_fetch_array($qry_grab_files)){ 
	?>
		<tr>
			<td><?=$grabfile['full_url'] ?></td>
			<td><?=$grabfile['full_path'] ?></td>
			<td>
				<?php
				switch($grabfile['status']){
					case 'OK':	// ok
						echo 'Done';
						break;
					
					case 'NF':	// not found
						echo 'File not found';
						break;
					
					case 'TO':	// timeout
						echo 'Time-out';
						break;
					
					case 'FE':	// file empty
						echo 'File empty';
						break;
					
					case 'FX':	// file exists
						echo 'File existed';
						break;
					
					case 'E':	// error
						echo 'Error';
						break;
					
					case 'P':	// processing
						echo 'Processing';
						break;
					
					case 'X':	// excluded
						echo 'Excluded';
						break;
					
					default:	// new, to be processed
						echo 'New';
						
				}
				?>
			</td>
			<td><?=$grabfile['date_inserted'] ?></td>
			<td><?=$grabfile['date_modified'] ?></td>
		</tr>
	<?php 
	}
	?>
	</tbody>
	
</table>

<?php
	include 'dsp_detail_grid_pager.php';
?>
