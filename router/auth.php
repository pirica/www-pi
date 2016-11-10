<?php

include 'connection.php';


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


/* Validation fields */

$username = '';
if(isset($_POST['username']) && $_POST['username'] != ''){
	$username = $_POST['username'];
}

$password = '';
if(isset($_POST['password']) && $_POST['password'] != ''){
	$password = $_POST['password'];
}


/* Validation logic */

$valid = 0;

if($url != '' && $username != '' && $password != ''){
	$username = str_replace('%', '', $username);
	$password = str_replace('%', '', $password);
	// check user/passw
	$qry = mysqli_query($conn, "
		select
			*
		from t_host 
		where 
			id_host = " . $id_host . "
			and username like binary '" . mysqli_real_escape_string($conn, $username) . "'
			and password like binary '" . mysqli_real_escape_string($conn, $password) . "'
		");
	
	while($host = mysqli_fetch_array($conn, $qry)){
		$valid = 1;
		mysqli_query($conn, "
			update t_host 
			set
				date_authenticated = now()
			where 
				id_host = " . $id_host . "
			");
	}
	
	
}

if($valid == 1){
	if($url != ''){
		header('Location: ' . $url);
	}
}

?><html>
<head>
<title>Authentication required</title>
</head>

<body>
	
	<?php
	if($valid == 1){
	?>
		
		Login succesfull.
		
	<?php
	}
	else {
	?>
		<form method="post" action="auth.php">
			<input type="hidden" name="id_host" value="<?=$id_host?>">
			<input type="hidden" name="url" value="<?=$url?>">
			
			Username:<br>
			<input type="text" name="username"><br>
			<br>
			
			Password:<br>
			<input type="password" name="password"><br>
			<br>
			
			<input type="submit" name="submit" value="Submit">
		</form>

	<?php
	}
	?>
</body>
</html>