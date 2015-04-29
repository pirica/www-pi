<?php



if ($loggedin) {
    $logged = 'in';
} else {
    $logged = 'out';
}
/*
<p>If you don't have a login, please <a href="dsp_register.php">register</a></p>

*/
?>

<p>If you have a login, please <a href="dsp_login.php">log in</a></p>
<p>If you are logged in, you can <a href="act_logout.php">log out</a>.</p>
<p>You are currently logged <?php echo $logged ?>.</p>