<?php

include_once '../users/sec-users.php';
sec_session_start();

include 'connection.php';

if (login_check($mysqli)){

	$qry_hosts = mysql_query("
		select
			h.id_host,
			h.ip_address,
			h.hostname,
			h.is_new,
			h.date_last_seen,
			
			h.authenticate,
			h.disabled
			
		from t_host h
		where
			h.active = 1
		
		", $conn);
		

	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		while($host = mysql_fetch_array($qry_hosts)){
			if(isset($_POST['authenticate' . $host['id_host']])){
				if($_POST['authenticate' . $host['id_host']] == 1){
					mysql_query("
						update t_host
						set
							authenticate = 1
						where
							id_host = " . $host['id_host'] . "
						", $conn);
				}
				else {
					mysql_query("
						update t_host
						set
							authenticate = 0
						where
							id_host = " . $host['id_host'] . "
						", $conn);
				}
			}
			if(isset($_POST['disabled' . $host['id_host']])){
				if($_POST['disabled' . $host['id_host']] == 1){
					mysql_query("
						update t_host
						set
							disabled = 1
						where
							id_host = " . $host['id_host'] . "
						");
				}
				else {
					mysql_query("
						update t_host
						set
							disabled = 0
						where
							id_host = " . $host['id_host'] . "
						", $conn);
				}
			}
			if(isset($_POST['is_new' . $host['id_host']])){
				if($_POST['is_new' . $host['id_host']] == 1){
					mysql_query("
						update t_host
						set
							is_new = 1
						where
							id_host = " . $host['id_host'] . "
						");
				}
				else {
					mysql_query("
						update t_host
						set
							is_new = 0
						where
							id_host = " . $host['id_host'] . "
						", $conn);
				}
			}
		}
	}


	$qry_hosts = mysql_query("
		select
			h.id_host,
			h.ip_address,
			h.hostname,
			h.is_online,
			h.is_new,
			h.date_last_seen,
			
			h.authenticate,
			h.date_authenticated,
			
			h.disabled,
			
			sum(case when r.id_rule is null then 0 else 1 end) as rules
			
		from t_host h
		left join t_host_rule hr on hr.id_host = h.id_host and hr.active = 1
		left join t_rule r on r.id_rule = hr.id_rule and r.active = 1
		
		where
			h.active = 1
			
		group by
			h.id_host,
			h.ip_address,
			h.hostname,
			h.is_online,
			h.is_new,
			h.date_last_seen,
			
			h.authenticate,
			h.date_authenticated,
			
			h.disabled
			
		order by
			h.hostname,
			h.ip_address
			
		", $conn);
		
	/* Main info */

	$id_host = -1;
	$hostname = '';
	if(isset($_POST['id_host']) && $_POST['id_host'] != '' && is_numeric($_POST['id_host'])){
		$id_host = $_POST['id_host'];
	}
	else if(isset($_GET['id_host']) && $_GET['id_host'] != '' && is_numeric($_GET['id_host'])){
		$id_host = $_GET['id_host'];
	}

	if($id_host > 0){
		
		mysql_query("
			insert into t_host_rule 
			(
				id_rule,
				id_host,
				active,
				params
			)
			select
				r.id_rule,
				" . $id_host . " as id_host,
				0 as active,
				null as params
				
			from t_rule r
			left join t_host_rule hr on hr.id_rule = r.id_rule and hr.id_host = " . $id_host . "
			
			where
				r.active = 1
				and hr.id_host_rule is null
			
			", $conn);
			
		
		$qry_rules = mysql_query("
			select
				hr.id_host_rule,
				hr.active,
				hr.id_rule,
				r.description,
				r.filter,
				r.action,
				ifnull(hr.params, r.params) as params,
				r.param_values,
				r.download_required,
				r.prevent_caching
				
			from t_rule r
			left join t_host_rule hr on hr.id_rule = r.id_rule and hr.id_host = " . $id_host . "
			
			where
				r.active = 1
			
			order by
				r.description,
				params
				
			", $conn);
		
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			while($rule = mysql_fetch_array($qry_rules)){
				$ruleparam = '';
				if(isset($_POST['rule' . $rule['id_rule']])){
					if(isset($_POST['ruleparam' . $rule['id_rule']])){
						$ruleparam = $_POST['ruleparam' . $rule['id_rule']];
					}
					mysql_query("
						update t_host_rule
						set
							active = 1,
							params = '" . mysql_real_escape_string($ruleparam) . "'
						where
							id_host = " . $id_host . "
							and id_rule = " . $rule['id_rule'] . "
						", $conn);
				}
				else {
					mysql_query("
						update t_host_rule
						set
							active = 0
						where
							id_host = " . $id_host . "
							and id_rule = " . $rule['id_rule'] . "
						", $conn);
				}
			}
		}
		
		$qry_rules = mysql_query("
			select
				hr.id_host_rule,
				hr.active,
				hr.id_rule,
				r.description,
				r.filter,
				r.action,
				ifnull(hr.params, r.params) as params,
				r.param_values,
				r.download_required,
				r.prevent_caching
				
			from t_rule r
			left join t_host_rule hr on hr.id_rule = r.id_rule and hr.id_host = " . $id_host . "
			
			where
				r.active = 1
			
			order by
				r.description,
				params
				
			", $conn);
		
	}
}


?><html>
<head>
<title>Host configuration</title>
<style type="text/css">
.single-button {height: 0; }
td {padding: 1px 5px;}
</style>
<?php
if (!login_check($mysqli)){
	echo '<script type="text/javascript" src="../users/scripts/sha512.js"></script>';
	echo '<script type="text/javascript" src="../users/scripts/forms.js"></script>';
}
?>
</head>

<body>
	
	<h2>Hosts</h2>
	
	<?php
	if (login_check($mysqli)){
	?>
		
		<table>
			<tr>
				<th align="left">&nbsp;</th>
				<th align="left">Host</th>
				<th align="left">IP</th>
				
				<th align="left">Is online</th>
				<th align="left">Is new</th>
				<th align="left">&nbsp;</th>
				
				<th align="left">Authenticate</th>
				<th align="left">&nbsp;</th>
				
				<th align="left">Disabled</th>
				<th align="left">&nbsp;</th>
				
				<th align="left">Date authenticated</th>
			</tr>
			<?php
			while($host = mysql_fetch_array($qry_hosts)){
				if($id_host == $host['id_host']){
					$hostname = $host['hostname'];
				}
			?>
				<tr>
					<td><?php if($id_host == $host['id_host']){ echo '-&gt;'; } ?></td>
					<td><a href="conf.php?id_host=<?=$host['id_host']?>"><?=$host['hostname']?></a></td>
					<td><?=$host['ip_address']?></td>
					
					<td><?php if($host['is_online'] == 1){ echo 'Online'; } ?></td>
					<td><?php if($host['is_new'] == 1){ echo 'Yes'; } ?></td>
					<td>
						<form class="single-button" method="post" action="conf.php">
							<?php if($host['is_new'] == 1){?>
								<input type="hidden" name="is_new<?=$host['id_host']?>" value="0">
								<input type="submit" value="Checked">
							<?php } /*else { ?>
								<input type="hidden" name="is_new<?=$host['id_host']?>" value="1">
								<input type="submit" value="Disable">
							<?php }*/ ?>
						</form>
					</td>
					
					<td><?php if($host['authenticate'] == 1){ echo 'Yes'; } ?></td>
					<td>
						<form class="single-button" method="post" action="conf.php">
							<?php if($host['authenticate'] == 1){?>
								<input type="hidden" name="authenticate<?=$host['id_host']?>" value="0">
								<input type="submit" value="De-authenticate">
							<?php } else { ?>
								<input type="hidden" name="authenticate<?=$host['id_host']?>" value="1">
								<input type="submit" value="Authenticate">
							<?php } ?>
						</form>
					</td>
					
					<td><?php if($host['disabled'] == 1){ echo 'Yes'; } ?></td>
					<td>
						<form class="single-button" method="post" action="conf.php">
							<?php if($host['disabled'] == 1){?>
								<input type="hidden" name="disabled<?=$host['id_host']?>" value="0">
								<input type="submit" value="Enable">
							<?php } else { ?>
								<input type="hidden" name="disabled<?=$host['id_host']?>" value="1">
								<input type="submit" value="Disable">
							<?php } ?>
						</form>
					</td>
					
					<td><?php if($host['date_authenticated'] != '0000-00-00 00:00:00'){ echo $host['date_authenticated']; } ?></td>
					
				</tr>
			<?php
			}
			?>
		</table>
		
		<?php
		if($id_host > 0){
		?>
		
		<h2>Rules for host '<?=$hostname?>'</h2>
		
		<form method="post" action="conf.php?id_host=<?=$id_host?>">
			<table>
				<tr>
					<th align="left">Rule</th>
					<th align="left">Enabled</th>
					<th align="left">Param</th>
				</tr>
				<?php
				while($rule = mysql_fetch_array($qry_rules)){
				?>
					<tr>
						<td><label for="rule<?=$rule['id_rule']?>"><?=$rule['description']?></label></td>
						<td><input id="rule<?=$rule['id_rule']?>" name="rule<?=$rule['id_rule']?>" type="checkbox" <?php if($rule['active'] == 1){ echo 'checked="checked"'; } ?>></td>
						
						<td>
							<?php
							if($rule['param_values'] != ''){
							?>
								<select id="ruleparam<?=$rule['id_rule']?>" name="ruleparam<?=$rule['id_rule']?>">
									<option value=""></option>
									<?php
										$param_values = json_decode('[' . $rule['param_values'] . ']', true);
										$param_values_len = count($param_values);
										for($v=0; $v<$param_values_len; $v++){
											echo '<option value="' . $param_values[$v]['v'] . '" ' . ($param_values[$v]['v'] == $rule['params'] ? 'selected="selected"' : '') . '>' . $param_values[$v]['l'] . '</option>';
										}
									?>
								</select>
							<?php
							}
							else {
							?>
								<input id="ruleparam<?=$rule['id_rule']?>" name="ruleparam<?=$rule['id_rule']?>" type="text" value="<?=$rule['params']?>">
							<?php
							}
							?>
						</td>
						
					</tr>
				<?php
				}
				?>
			</table>
			
			<input type="submit" value="Submit">
		</form>
		
		<?php
		}
		?>
		
	<?php
	} // /logincheck
	else {
		$_SESSION['url_after_login'] = get_url_after_login();
		include '../users/dsp_loginform.php';
	}
	?>
	
</body>
</html>