<?php
 
set_time_limit(0);
include 'connection.php';

//include dirname(__FILE__).'/../messages/ortc_calls.php';
//include dirname(__FILE__).'/../messages/nma_calls.php';

$output = 0;
//$output = 1;


// OS detection
/*

sudo nmap -O 192.168.1.101


Starting Nmap 6.00 ( http://nmap.org ) at 2014-10-28 15:28 CET
Nmap scan report for herman.lan (192.168.1.101)
Host is up (0.0026s latency).
Not shown: 991 closed ports
PORT      STATE SERVICE
135/tcp   open  msrpc
139/tcp   open  netbios-ssn
445/tcp   open  microsoft-ds
5357/tcp  open  wsdapi
49152/tcp open  unknown
49153/tcp open  unknown
49154/tcp open  unknown
49156/tcp open  unknown
49157/tcp open  unknown
MAC Address: 74:E5:43:6C:FD:B0 (Unknown)
Device type: general purpose
Running: Microsoft Windows 7|2008
OS CPE: cpe:/o:microsoft:windows_7 cpe:/o:microsoft:windows_server_2008::sp1
OS details: Microsoft Windows 7 or Windows Server 2008 SP1
Network Distance: 1 hop

OS detection performed. Please report any incorrect results at http://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 17.25 seconds


-OR-


Starting Nmap 6.00 ( http://nmap.org ) at 2014-10-28 15:35 CET
Nmap scan report for pibot.lan (192.168.1.105)
Host is up (0.0011s latency).
Not shown: 998 closed ports
PORT   STATE SERVICE
22/tcp open  ssh
80/tcp open  http
MAC Address: B8:27:EB:BC:FF:1D (Raspberry Pi Foundation)
No exact OS matches for host (If you know what OS is running on it, see http://nmap.org/submit/ ).
TCP/IP fingerprint:
OS:SCAN(V=6.00%E=4%D=10/28%OT=22%CT=1%CU=35989%PV=Y%DS=1%DC=D%G=Y%M=B827EB%
OS:TM=544FA9D5%P=armv7l-unknown-linux-gnueabi)SEQ(SP=104%GCD=1%ISR=10C%TI=Z
OS:%CI=I%II=I%TS=7)OPS(O1=M5B4ST11NW6%O2=M5B4ST11NW6%O3=M5B4NNT11NW6%O4=M5B
OS:4ST11NW6%O5=M5B4ST11NW6%O6=M5B4ST11)WIN(W1=7120%W2=7120%W3=7120%W4=7120%
OS:W5=7120%W6=7120)ECN(R=Y%DF=Y%T=41%W=7210%O=M5B4NNSNW6%CC=Y%Q=)T1(R=Y%DF=
OS:Y%T=41%S=O%A=S+%F=AS%RD=0%Q=)T2(R=N)T3(R=N)T4(R=Y%DF=Y%T=41%W=0%S=A%A=Z%
OS:F=R%O=%RD=0%Q=)T5(R=Y%DF=Y%T=41%W=0%S=Z%A=S+%F=AR%O=%RD=0%Q=)T6(R=Y%DF=Y
OS:%T=41%W=0%S=A%A=Z%F=R%O=%RD=0%Q=)T7(R=Y%DF=Y%T=41%W=0%S=Z%A=S+%F=AR%O=%R
OS:D=0%Q=)U1(R=Y%DF=N%T=41%IPL=164%UN=0%RIPL=G%RID=G%RIPCK=G%RUCK=G%RUD=G)I
OS:E(R=Y%DFI=N%T=41%CD=S)

Network Distance: 1 hop

OS detection performed. Please report any incorrect results at http://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 33.85 seconds

*/



$qry_hosts = mysqli_query($conn, "
	select
		h.id_host,
		h.ip_address,
		h.hostname
	from 
		t_host h
	where
		h.active = 1
		and h.is_online = 1
		and ifnull(h.date_checked, date_add(now(), interval -1 day ) ) < date_add(now(), interval 12 hour )
		
	");
	
while ($host = mysqli_fetch_array($qry_hosts)) {

	$shell_nmap = shell_exec('nmap -O ' . $host['ip_address']);

	echo "<!--\n";
	echo $shell_nmap;
	echo "-->\n";

	$lines = explode("\n", $shell_nmap);
	
	$device_type = '';
	$os = '';
	$os_details = '';
	$network_distance = '';
	$open_ports = '';
	
	
	$portmode = 0;
	
	$c = count($lines);
	for ($i = 0; $i < $c; $i++) {
		$line = $lines[$i];
		
		$linecheck = strtolower( str_replace(str_replace($line, '  ', ' '), '  ', ' ') );
		
		if($line != '' && count(explode(": ", $line)) > 1){
			
			list($code, $value) = explode(": ", $line, 2);
			
			if(strtolower($code) == 'device type'){
				$device_type = $value;
			}
			else if(strtolower($code) == 'running'){
				$os = $value;
			}
			else if(strtolower($code) == 'os details'){
				$os_details = $value;
			}
			else if(strtolower($code) == 'network distance'){
				$network_distance = $value;
			}
			else if(str_replace(strtoupper($code), ' ', '') == 'PORTSTATESERVICE'){
				$portmode = 1;
			}
			
			if($portmode == 1){
				if(is_numeric(substr($code, 0, 1))){
					$open_ports .= '\n' . $value;
				}
				else {
					$portmode = 0;
				}
			}
			
		}
	}
	
	echo "<!--\n";
	echo "device_type = '" . mysqli_real_escape_string($conn, $device_type) . "'\n";
	echo "os = '" . mysqli_real_escape_string($conn, $os) . "'\n";
	echo "os_details = '" . mysqli_real_escape_string($conn, $os_details) . "'\n";
	echo "network_distance = '" . mysqli_real_escape_string($conn, $network_distance) . "'\n";
	echo "open_ports = '" . mysqli_real_escape_string($conn, $open_ports) . "'\n";
	echo "-->\n";
	
	if(str_replace(' ', '', $device_type) == '') $device_type = 'null'; else $device_type = "'" . mysqli_real_escape_string($conn, $device_type) . "'";
	if(str_replace(' ', '', $os) == '') $os = 'null'; else $os = "'" . mysqli_real_escape_string($conn, $os) . "'";
	if(str_replace(' ', '', $os_details) == '') $os_details = 'null'; else $os_details = "'" . mysqli_real_escape_string($conn, $os_details) . "'";
	if(str_replace(' ', '', $network_distance) == '') $network_distance = 'null'; else $network_distance = "'" . mysqli_real_escape_string($conn, $network_distance) . "'";
	if(str_replace(' ', '', $open_ports) == '') $open_ports = 'null'; else $open_ports = "'" . mysqli_real_escape_string($conn, $open_ports) . "'";
	
	echo "<!--\n";
	echo "update t_host
		set
			device_type = ifnull(" . $device_type . ", device_type),
			os = ifnull(" . $os . ", os),
			os_details = ifnull(" . $os_details . ", os_details),
			network_distance = ifnull(" . $network_distance . ", network_distance),
			open_ports = ifnull(" . $open_ports . ", open_ports),
			
			date_checked = now()
			
		where
			id_host = " . $host['id_host'] . "\n";
	echo "-->\n";
	
	mysqli_query($conn, "
		update t_host
		set
			device_type = ifnull(" . $device_type . ", device_type),
			os = ifnull(" . $os . ", os),
			os_details = ifnull(" . $os_details . ", os_details),
			network_distance = ifnull(" . $network_distance . ", network_distance),
			open_ports = ifnull(" . $open_ports . ", open_ports),
			
			date_checked = now()
			
		where
			id_host = " . $host['id_host'] . "
		
		");
	
}


?>