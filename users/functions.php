<?php

/*
 * Copyright (C) 2013 peredur.net
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

function sec_session_start() {
    $session_name = 'sec_session_id';   // Set a custom session name 
    $secure = SECURE;

    // This stops JavaScript being able to access the session id.
    $httponly = true;

    // Forces sessions to only use cookies.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: index.php?action=error&err=Could not initiate a safe session (ini_set)");
        exit();
    }

    // Gets current cookies params.
    $cookieParams = session_get_cookie_params();
	// set cookie timeout (default = 0 secs, this session)
	//$cookieParams["lifetime"] = 60 * 10;
	$cookieParams["lifetime"] = 60 * 60 * 24 * 30;
    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);

    // Sets the session name to the one set above.
    session_name($session_name);

    session_start();            // Start the PHP session 
    session_regenerate_id(true);    // regenerated the session, delete the old one. 
	
	$_SESSION['desktop'] = 1;
	if(isset($_SERVER['HTTP_USER_AGENT'])){
		$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
		if(stripos($ua, 'android') !== false) { // && stripos($ua,'mobile') !== false) {
			$_SESSION['desktop'] = 0;
		}
	}
	
	$_SESSION['local'] = 1;
	if(isset($_SERVER['REMOTE_ADDR'])){
		if(stripos($_SERVER['REMOTE_ADDR'], '192.168.1.') === false) { // && stripos($ua,'mobile') !== false) {
			$_SESSION['local'] = 0;
		}
	}
	
}

function login($email, $password_plain, $mysqli, $rememberme = false) {
    // Using prepared statements means that SQL injection is not possible. 
    if ($stmt = $mysqli->prepare("SELECT id_user, username, password, salt 
				  FROM t_user 
                  WHERE email = ? 
					and active = 1
				  LIMIT 1")) {
        $stmt->bind_param('s', $email);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();

        // get variables from result.
        $stmt->bind_result($user_id, $username, $db_password, $salt);
        $stmt->fetch();

        // hash the password with the unique salt.
        $password = hash('sha512', $password_plain . $salt);
        if ($stmt->num_rows == 1) {
            // If the user exists we check if the account is locked
            // from too many login attempts 
            if (checkbrute($user_id, $mysqli) == true) {
                // Account is locked 
                // Send an email to user saying their account is locked 
                return false;
            }
			else {
                // Check if the password in the database matches 
                // the password the user submitted.
                if ($db_password == $password) {
                    // Password is correct!
                    // Get the user-agent string of the user.
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];

                    // XSS protection as we might print this value
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    $_SESSION['user_id'] = $user_id;

                    // XSS protection as we might print this value
                    //$username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $username);

                    $_SESSION['username'] = $username;
                    $_SESSION['username_safe'] = htmlentities(preg_replace("/[^a-zA-Z0-9_\-]+/", "", $username));
                    $_SESSION['login_string'] = hash('sha512', $password . $user_browser);
					$_SESSION['logintime'] = time();
					$_SESSION['logintimecheck'] = time();
					$_SESSION['logins'] = 1;
					$_SESSION['loginchecks'] = 1;
					
					$cookieParams = session_get_cookie_params();
					
					if($rememberme){
						// set cookie
						setcookie('sessiontimeout', (3600 * 24 * 30), time() + (3600 * 24 * 30), $cookieParams["path"], $cookieParams["domain"], SECURE, /*$httponly =*/ true);
					}
					else {
						// clear cookie
						setcookie('sessiontimeout', '', 0, $cookieParams["path"], $cookieParams["domain"], SECURE, /*$httponly =*/ true);
					}
					
					if(isset($_SESSION['url_after_login'])){
						header("Location: " . $_SESSION['url_after_login']);
						exit();
					}
                    // Login successful. 
                    return true;
                }
				else {
                    // Password is not correct 
                    // We record this attempt in the database 
                    if (!$mysqli->query("INSERT INTO t_log_login(id_user, email, password, ip_address) 
                                    VALUES ('$user_id', '$email', '$password_plain', '".$_SERVER['REMOTE_ADDR']."')")) {
                        header("Location: index.php?action=error&err=Database error: login_attempts");
                        exit();
                    }

                    return false;
                }
            }
        }
		else {
            // No user exists. 
            return false;
        }
    }
	else {
        // Could not create a prepared statement
        header("Location: index.php?action=error&err=Database error: cannot prepare statement");
        exit();
    }
}

function checkbrute($user_id, $mysqli) {
    // Get timestamp of current time 
    $now = time();

    // All login attempts are counted from the past 2 hours. 
    $valid_attempts = /*$now -*/ (2 * 60 * 60);

    if ($stmt = $mysqli->prepare("SELECT date as time 
                                  FROM t_log_login 
                                  WHERE id_user = ? AND date > now() - interval $valid_attempts second")) {
        $stmt->bind_param('i', $user_id);

        // Execute the prepared query. 
        $stmt->execute();
        $stmt->store_result();

        // If there have been more than 5 failed logins 
        if ($stmt->num_rows > 5) {
            return true;
        }
		else {
            return false;
        }
    }
	else {
        // Could not create a prepared statement
        header("Location: index.php?action=error&err=Database error: cannot prepare statement");
        exit();
    }
}

function login_check($mysqli) {

    // Check if all session variables are set 
    if (isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'], $_SESSION['logintimecheck'])) {
        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        //$username = $_SESSION['username'];
		$logintimecheck = $_SESSION['logintimecheck'];

        // Get the user-agent string of the user.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
		
		// Logged In less than 10 minutes ago!!!! 
		$sessiontimeout = 10 * 60;
		
		if(isset($_COOKIE['sessiontimeout'])){
			$sessiontimeout = $_COOKIE['sessiontimeout'];
		}
		
		if($logintimecheck > time() - $sessiontimeout){
			$_SESSION['logintimecheck'] = time();
			$_SESSION['loginchecks']++;
            return true;
		}
        else if ($stmt = $mysqli->prepare("SELECT password FROM t_user WHERE id_user = ? LIMIT 1")) {
            // Bind "$user_id" to parameter. 
            $stmt->bind_param('i', $user_id);
            $stmt->execute();   // Execute the prepared query.
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                // If the user exists get variables from result.
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = hash('sha512', $password . $user_browser);

                if ($login_check == $login_string) {
					$_SESSION['logintimecheck'] = time();
					$_SESSION['logins']++;
                    // Logged In!!!! 
					
					$cookieParams = session_get_cookie_params();
					setcookie('sessiontimeout', $sessiontimeout, time() + (3600 * 24 * 30), $cookieParams["path"], $cookieParams["domain"], SECURE, /*$httponly =*/ true);
						
                    return true;
                }
				else {
                    // Not logged in 
                    return false;
                }
            }
			else {
                // Not logged in 
                return false;
            }
        }
		else {
            // Could not prepare statement
            header("Location: index.php?action=error&err=Database error: cannot prepare statement");
            exit();
        }
    }
	else {
        // Not logged in 
        return false;
    }
}

function esc_url($url) {

    if ('' == $url) {
        return $url;
    }

    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
    
    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;
    
    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }
    
    $url = str_replace(';//', '://', $url);

    $url = htmlentities($url);
    
    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);

    if ($url[0] !== '/') {
        // We're only interested in relative links from $_SERVER['PHP_SELF']
        return '';
    }
	else {
        return $url;
    }
}

function get_url_after_login(){
	// from: http://stackoverflow.com/questions/6768793/get-the-full-url-in-php 
	$ssl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? true:false;
	$sp = strtolower($_SERVER['SERVER_PROTOCOL']);
	$protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
	$port = $_SERVER['SERVER_PORT'];
	$port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
	$host = /*($use_forwarded_host && isset($_SERVER['HTTP_X_FORWARDED_HOST'])) ? $_SERVER['HTTP_X_FORWARDED_HOST'] :*/ (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null);
	$host = isset($host) ? $host : $_SERVER['SERVER_NAME'] . $port;
	
	return $protocol . '://' . $host . $_SERVER['REQUEST_URI'];
}

?>