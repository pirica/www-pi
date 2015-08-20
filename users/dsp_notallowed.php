
<h1>Not allowed</h1>

<?php
if (isset($_GET['error'])) {
	echo '<p class="error">Error Logging In!</p>';
}
?> 

<p>
	You are not allowed to access this page.
	Please login as a different user, or request access from the administrator.
</p>
