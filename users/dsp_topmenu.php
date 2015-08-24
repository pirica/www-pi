<?php

include 'queries/pr_get_apps.php';

// /*
if(isset($_GET['newmenu'])) {

?>

<div class="topmenu">
	<nav class="navbar">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#">Bpi home server</a>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<!--
					<li class="active"><a href="#">Home</a></li>
					<li><a href="#">About</a></li>
					<li><a href="#">Contact</a></li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="#">Action</a></li>
							<li><a href="#">Another action</a></li>
							<li><a href="#">Something else here</a></li>
							<li role="separator" class="divider"></li>
							<li class="dropdown-header">Nav header</li>
							<li><a href="#">Separated link</a></li>
							<li><a href="#">One more separated link</a></li>
						</ul>
					</li>
					-->
					
					<?php
					if(isset($loggedin) && $loggedin === true){
						while ($qry_apps->fetch()) {
							
							$class = '';
							if($is_current == 1){
								$class .= ' active';
							}
							
							if($show_in_topmenu == 1){
							?>
								<li class="<?= $class ?>"><a href="<?= $relative_url ?>"><?= $description ?></a></li>
							<?php
							}
						}
					}
					else {
						while ($qry_apps->fetch()) {
							
							$class = '';
							if($is_current == 1){
								$class .= ' active';
							}
							
							if($login_required == 1) {
								$class .= ' disabled';
								$relative_url = "#";
							}
							
							if($show_in_topmenu == 1){
							?>
								<li class="<?= $class ?>"><a href="<?= $relative_url ?>"><?= $description ?></a></li>
							<?php
							}
						}
					}
					?>
					
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<!--
					<li class="active"><a href="./">Default <span class="sr-only">(current)</span></a></li>
					<li><a href="../navbar-static-top/">Static top</a></li>
					<li><a href="../navbar-fixed-top/">Fixed top</a></li>
					-->
					
					<?php
					if(isset($loggedin) && $loggedin === true){
					?>
						<li class="dropdown pull-right">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">
								<i class="fa fa-user"></i>
								<span class="user"><?= $_SESSION['username_safe'] ?></span>
								<span class="caret"></span>
							</a>
							<ul class="dropdown-menu pull-right" role="menu">
								<li class="disabled"><a href="#">My profile</a></li>
								<li class="disabled"><a href="#">My stats</a></li>
								<li class="divider"></li>
								<li><a href="../users/index.php?action=settings">Settings</a></li>
								<li><a href="../users/index.php?action=actions">Actions</a></li>
								<li class="divider"></li>
								<li><a href="../users/index.php?action=logout">Log out</a></li>
							</ul>
						</li>
					<?php
					}
					else {
					?>
						<li class="dropdown pull-right">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">
								<i class="fa fa-user"></i>
								<span class="user">(Not logged in) </span>
								<span class="caret"></span>
							</a>
							<ul class="dropdown-menu pull-right" role="menu">
								<li><a href="../users/index.php?action=login">Log in</a></li>
								<li><a href="../users/index.php?action=register">Register</a></li>
							</ul>
						</li>
					<?php
					}
					?>
				</ul>
			</div><!--/.nav-collapse -->
		</div><!--/.container-fluid -->
	</nav>
</div>

<?php
// */


// /*
} else {
?>

<div class="topmenu">
	<!--div class="dd-button"><i class="fa fa-3x fa-bars"></i></div-->
	<ul class="nav nav-tabs">
		<?php
		if(isset($loggedin) && $loggedin === true){
			while ($qry_apps->fetch()) {
				
				$class = '';
				if($is_current == 1){
					$class .= ' active';
				}
				
				if($show_in_topmenu == 1){
				?>
					<li class="<?= $class ?>"><a href="<?= $relative_url ?>"><?= $description ?></a></li>
				<?php
				}
			}
			?>
			<li class="dropdown pull-right">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#">
					<i class="fa fa-user"></i>
					<span class="user"><?= $_SESSION['username_safe'] ?></span>
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu pull-right" role="menu">
					<li class="disabled"><a href="#">My profile</a></li>
					<li class="disabled"><a href="#">My stats</a></li>
					<li class="divider"></li>
					<li><a href="../users/index.php?action=settings">Settings</a></li>
					<li><a href="../users/index.php?action=actions">Actions</a></li>
					<li class="divider"></li>
					<li><a href="../users/index.php?action=logout">Log out</a></li>
				</ul>
			</li>
		<?php
		}
		else {
			while ($qry_apps->fetch()) {
				
				$class = '';
				if($is_current == 1){
					$class .= ' active';
				}
				
				if($login_required == 1) {
					$class .= ' disabled';
					$relative_url = "#";
				}
				
				if($show_in_topmenu == 1){
				?>
					<li class="<?= $class ?>"><a href="<?= $relative_url ?>"><?= $description ?></a></li>
				<?php
				}
			}
			?>
			
			<li class="dropdown pull-right">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#">
					<i class="fa fa-user"></i>
					<span class="user">(Not logged in) </span>
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu pull-right" role="menu">
					<li><a href="../users/index.php?action=login">Log in</a></li>
					<li><a href="../users/index.php?action=register">Register</a></li>
				</ul>
			</li>
			
		<?php
		}
		?>
	</ul>
</div>

<?php
// */
}
?>