<?php



if ($loggedin) {
    $logged = 'in';
} else {
    $logged = 'out';
}
/*
<p>If you don't have a login, please <a href="index.php?action=register">register</a></p>

*/
?>

<p>If you have a login, please <a href="index.php?action=login">log in</a></p>
<p>If you are logged in, you can <a href="index.php?action=logout">log out</a>.</p>
<p>You are currently logged <?php echo $logged ?>.</p>