<?php
set_time_limit(0);

require dirname(__FILE__).'/../_core/appinit.php';

//require 'connection.php';
//require 'functions.php';
$conn = $conn_users;


if(!$task->getIsRunning())
{
	$task->setIsRunning(true);
	
	
	// Set "salt" field for new users
	$qry_users_new = mysqli_query($conn, "
		select
			id_user,
			username
		from t_user
		where
			ifnull(salt,'') = ''
		");
		
	while($user = mysqli_fetch_array($qry_users_new)){
		mysqli_query($conn, "
			update t_user
			set
				salt = '" . hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE)) . "'
			where
				id_user = " . $user['id_user'] . "
				and ifnull(salt,'') = ''
			");
		echo 'Salt for user "' . $user['username'] . '" set';
	}
	
	
	// Set "password" field for new users
	$qry_users_new = mysqli_query($conn, "
		select
			id_user,
			username,
			salt,
			new_password
		from t_user
		where
			ifnull(new_password,'') <> ''
			and ifnull(salt,'') <> ''
		");
		
	while($user = mysqli_fetch_array($qry_users_new)){
		$password = $user['new_password'];
		mysqli_query($conn, "
			update t_user
			set
				password = '" . hash('sha512', hash('sha512', $password) . $user['salt']) . "',
				new_password = ''
			where
				id_user = " . $user['id_user'] . "
				and ifnull(new_password,'') <> ''
			");
		echo 'Password for user "' . $user['username'] . '" set to "' . $password . '"';
	}
	
	
	
	$task->setIsRunning(false);
}

?>