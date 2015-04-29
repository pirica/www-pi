
<?php
if (isset($_GET['error'])) {
	echo '<p class="error">Error Logging In!</p>';
}
?> 

<h1>Log in</h1>

<form class="form-horizontal login-form" action="../users/act_login.php" method="post" name="login_form"> 		
	
	<div class="form-group">
		<label for="email">Email</label>
		<input id="email" name="email" class="form-control" type="text">
	</div>
	
	<div class="form-group">
		<label for="password">Password</label>
		<input id="password" name="password" class="form-control" type="password">
	</div>
	
	<div class="form-group">
		<!--label class="control-label" for="singlebutton">Save</label-->
		<button id="singlebutton" name="singlebutton" class="btn btn-primary" type="submit" value="Login" 
		   onclick="formhash(this.form, this.form.password);">Login</button>
	</div>
	
</form>
