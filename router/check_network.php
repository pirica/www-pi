<?php

set_time_limit(0);
require dirname(__FILE__).'/../_core/appinit.php';

include 'connection.php';

//include dirname(__FILE__).'/../messages/ortc_calls.php';
//include dirname(__FILE__).'/../messages/nma_calls.php';
require dirname(__FILE__).'/../messages/functions.php';


$output = 0;
//$output = 1;


$send_newhost_messages = $settings->val('send_newhost_messages', 1);
$send_hoststatus_messages = $settings->val('send_hoststatus_messages', 1);

/**
 *	parse DHCP leases file and log all new IP-MAc combo's in a database table
 *	file format: 
1389123677 b8:27:eb:f8:7d:11 192.168.1.10 nasberrypi *

 *	
 */

$leases = @file_get_contents("http://192.168.1.1/cgi-bin/dhcp.leases", "r");
if($leases !== false){
	$lines = explode("\n", $leases);
	
	echo "Begin leases<br>\n";
    //while (($linestr = fgets($handle)) !== false) {
	$c = count($lines);
    for ($i = 0; $i < $c; $i++) {
		$linestr = $lines[$i];
        if($output == 1) echo "\tLine: " . $linestr . "<br>\n";
		
		if($linestr != ''){
			
			$line = explode(' ', $linestr);
			
			$timestamp = $line[0];
			$mac = $line[1];
			$ip = $line[2];
			$host = $line[3];
			
			$qry_health_check = mysql_query("
				select
					mac_address,
					ip_address,
					hostname
				from 
					t_host
				where
					active = 1
				", $conn);
				
			$qry_check = mysql_query("
				select
					mac_address,
					ip_address,
					hostname
				from 
					t_host
				where
					mac_address = '" . mysql_real_escape_string($mac) . "'
					and ip_address = '" . mysql_real_escape_string($ip) . "'
					and active = 1
				", $conn);
				
			if(mysql_num_rows($qry_health_check) > 0 && mysql_num_rows($qry_check) == 0){
				
				mysql_query("
					insert into t_host
					(
						ip_address,
						mac_address,
						hostname,
						date_last_seen
					)
					values
					(
						'" . mysql_real_escape_string($ip) . "',
						'" . mysql_real_escape_string($mac) . "',
						'" . mysql_real_escape_string($host) . "',
						'" . date('Y-m-d H:i:s', time()) . "'
					)
					", $conn);
				
				if($send_newhost_messages == 1){
					$msg = 'New host detected: Name=' . $host . ', IP=' . $ip . ', MAC=' . $mac;
					$channel = 'router';
					$title = '';
					$priority = $settings->val('messages_priority_newhost', 2);
					send_msg($channel, $title, $msg, $priority);
				}
				echo "\t\t- New host added<br>\n";
			}
		}
    }
	echo "End leases<br>\n";
}


	
mysql_query("update t_host set check_is_online = 0", $conn);

//$shell_nmap = shell_exec('nmap -sn 192.168.1.0/24');
$shell_nmap = shell_exec('nmap -sP -PA22,25,3389,139 192.168.1.0/24');
/*outputs:
Starting Nmap 6.00 ( http://nmap.org ) at 2014-10-06 14:41 CEST
Nmap scan report for OpenWrt.lan (192.168.1.1)
Host is up (0.0040s latency).
MAC Address: 48:F8:B3:31:E5:44 (Unknown)									=> when sudo'ed
Nmap scan report for nasberrypi.lan (192.168.1.101)
Host is up (0.0018s latency).
Nmap done: 256 IP addresses (2 hosts up) scanned in 2.91 seconds
*/
echo "<!--\n";
echo $shell_nmap;
echo "-->\n";

$lines = explode("\n", $shell_nmap);

echo "Begin IP check<br>\n";
$c = count($lines);
$ip = '';
for ($i = 0; $i < $c; $i++) {
	$line = $lines[$i];
	if($output == 1) echo "\tLine: " . $line . "<br>\n";
	$line = strtolower($line);
	
	if($line != '' && strpos($line, 'starting nmap') === false && strpos($line, 'nmap done') === false && strpos($line, 'mac address') === false){
		
		if($ip == ''){
		//if(strpos($line, 'nmap scan report for') !== false ){
			$ip = explode(")", explode("(", $line)[1])[0];
		}
		else {
            if($output == 1) echo "\tIP: " . $ip . "<br>\n";
			if( strpos($line, 'host is up') !== false ){
				mysql_query("
					update t_host
					set
						check_is_online = 1
					where
						ip_address = '" . mysql_real_escape_string($ip) . "'
						and active = 1
					
					", $conn);
			}
			$ip = '';
		}
		
	}
}


// check each presumed offline again to make sure
$qry_hosts_status = mysql_query("
	select
		h.id_host,
		h.ip_address,
		h.hostname
	from 
		t_host h
	where
		h.active = 1
		#and h.announce_status = 1
		and (
			(h.is_online = 1 and h.check_is_online = 0)
			#or
			#(h.is_online = 0 and h.check_is_online = 1)
		)
	
	", $conn);
	
while ($hoststatus = mysql_fetch_array($qry_hosts_status)) {
	$shell_nmap = shell_exec('nmap -sP -PA22,25,3389,139 ' . $hoststatus['ip_address']);
	echo "<!--\n";
	echo $shell_nmap;
	echo "-->\n";

	if( strpos(strtolower($shell_nmap), 'host is up') !== false ){
		mysql_query("
			update t_host
			set
				check_is_online = 1
			where
				ip_address = '" . mysql_real_escape_string($hoststatus['ip_address']) . "'
				and active = 1
			
			", $conn);
	}
}


mysql_query("update t_host set date_last_seen = now() where check_is_online = 1", $conn);

mysql_query("
	insert into t_host_status
	(
		id_host,
		date_online,
		log
	)
	select
		h.id_host,
		now(),
		'" . mysql_real_escape_string($shell_nmap) . "'
	from 
		t_host h
	where
		h.active = 1
		and h.is_online = 0
		and h.check_is_online = 1
	
	", $conn);

mysql_query("
	insert into t_host_status
	(
		id_host,
		date_offline,
		log
	)
	select
		h.id_host,
		now(),
		'" . mysql_real_escape_string($shell_nmap) . "'
	from 
		t_host h
	where
		h.active = 1
		and h.is_online = 1
		and h.check_is_online = 0
	
	", $conn);

	
if($send_hoststatus_messages == 1){
	$qry_hosts_status = mysql_query("
		select
			h.id_host,
			h.ip_address,
			h.hostname,
			ifnull(h.hostname_friendly,h.hostname) as hostname_lbl,
			case when h.is_online = 0 and h.check_is_online = 1 then 1 else 0 end as went_online
		from 
			t_host h
		where
			h.active = 1
			and h.announce_status = 1
			and (
				(h.is_online = 1 and h.check_is_online = 0)
				or
				(h.is_online = 0 and h.check_is_online = 1)
			)
			#and ifnull(h.date_last_seen, now() ) < date_add(now(), interval -5 minute )
		
		", $conn);
		
	while ($hoststatus = mysql_fetch_array($qry_hosts_status)) {
		$msg = 'Host "' . $hoststatus['hostname_lbl'] . '" ' . ($hoststatus['went_online'] == 1 ? 'online' : 'offline') . ' (' . $hoststatus['ip_address'] . ')';
		$channel = 'router';
		$title = '';
		$priority = $settings->val('messages_priority_hoststatus', -1);
		
		/* check if sent this minute */
		$date_check = date("Y-m-d H:M:00");

		if(!check_msg_already_sent($channel, $title, $msg, $date_check)){
			send_msg($channel, $title, $msg, $priority);
		}
	}
}

mysql_query("update t_host set is_online = check_is_online", $conn);

echo "End IP check<br>\n";

echo "<br>\n";
echo "<br>\n";


?>