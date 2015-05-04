
<?php if (login_check($mysqli) == true) : ?>
<p>Welcome <?php echo $_SESSION['username_safe']; ?>!</p>
	<p>
		This is an example protected page.  To access this page, users
		must be logged in.  At some stage, we'll also check the role of
		the user, so pages will be able to determine the type of user
		authorised to access the page.
	</p>
	<p>Return to <a href="index.php?action=login">login page</a></p>
<?php else : ?>
	<p>
		<span class="error">You are not authorized to access this page.</span> Please <a href="index.php?action=login">login</a>.
	</p>
<?php endif; ?>
