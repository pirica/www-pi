
<h1>Log in</h1>

<?php
if (isset($_GET['error'])) {
	echo '<p class="error">Error Logging In!</p>';
}
?> 

<form class="form-horizontal login-form" action="../users/index.php?action=do_login" method="post" name="login_form"> 		
	<?php
		if($app->getId() > 0){
			echo '<input type="hidden" name="url_after_login" value="' . get_url_after_login() . '">';
		}
	?>
	<div class="form-group">
		<label for="email">Email</label>
		<input id="email" name="email" class="form-control" type="text">
	</div>
	
	<div class="form-group">
		<label for="password">Password</label>
		<input id="password" name="password" class="form-control" type="password">
	</div>
	
	<div class="form-group checkbox">
		<label for="rememberme">
			<input id="rememberme" name="rememberme" value="1" class="form-control" type="checkbox">
			Remember me
		</label>
	</div>
	
	<div class="form-group">
		<!--label class="control-label" for="singlebutton">Save</label-->
		<button id="singlebutton" name="singlebutton" class="btn btn-primary" type="submit" value="Login" 
		   onclick="formhash(this.form, this.form.password);">Login</button>
	</div>
	
</form>
