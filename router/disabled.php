<?php

//include 'connection.php';


/* Main info */

$id_host = -1;
if(isset($_POST['id_host']) && $_POST['id_host'] != '' && is_numeric($_POST['id_host'])){
	$id_host = $_POST['id_host'];
}
else if(isset($_GET['id_host']) && $_GET['id_host'] != '' && is_numeric($_GET['id_host'])){
	$id_host = $_GET['id_host'];
}


$url = '';
if(isset($_POST['url']) && $_POST['url'] != ''){
	$url = $_POST['url'];
}
else if(isset($_GET['url']) && $_GET['url'] != ''){
	$url = $_GET['url'];
}


if!isset($msg)){
	$msg = '';
}
if(isset($_POST['msg']) && $_POST['msg'] != ''){
	$msg = $_POST['msg'];
}
else if(isset($_GET['msg']) && $_GET['msg'] != ''){
	$msg = $_GET['msg'];
}

?><html>
<head>
<title>Access forbidden</title>
</head>

<body>
	
	<h1>Access forbidden</h1>
	
	<?php
	if($url != ''){
	?>
		<p>
			You are not authorised to access <a href="<?=$url?>"><?=$url?></a>.
		</p>
	<?php
	}
	else {
	?>
		<p>
			You are not authorised.
		</p>
	<?php
	}
	?>
	
	<?php
	if($msg != ''){
		echo '<p>' . $msg . '</p>';
	}
	?>
	
</body>
</html>