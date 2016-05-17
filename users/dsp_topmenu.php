<?php


// /*
if(isset($_GET['newmenu'])) {
	
	include 'queries/pr_get_apps_actions.php';

?>

<div class="topmenu2">
	<nav class="navbar navbar-default navbar-inverse" role="navigation">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#">Wikke.net</a>
		</div>
		
		<?php
		if(isset($loggedin) && $loggedin === true)
		{
			?>
			<ul class="nav navbar-nav nav-user">
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
						<li><a href="../users/index.php?action=profileapps">Profiles - Apps</a></li>
						<li><a href="../users/index.php?action=profileappactions">Profiles - Actions</a></li>
						<li class="divider"></li>
						<li><a href="../users/index.php?action=logout">Log out</a></li>
					</ul>
				</li>
			</ul>
			<?php
		}
		else
		{
			?>
			<ul class="nav navbar-nav nav-user">
				<li class="dropdown pull-right">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">
						<i class="fa fa-user"></i>
						<span class="user">(Not logged in) </span>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu pull-right" role="menu">
						<li><a href="../users/index.php?action=login">Log in</a></li>
						<!--<li><a href="../users/index.php?action=register">Register</a></li>-->
					</ul>
				</li>
			</ul>
			<?php
		}
		?>

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse navbar-ex1-collapse">
			<ul class="nav navbar-nav nav-apps">
				<?php
				while ($menu = mysql_fetch_array($qry_apps)) {
					$relative_url = $menu['relative_url'];
					$description = $menu['description'];
					
					$class = '';
					if($menu['is_current'] == 1){
						$class .= ' active';
					}
					
					if(isset($loggedin) && $loggedin === true){
						
					}
					else if($menu['login_required'] == 1) {
						$class .= ' disabled';
						$relative_url = "#";
					}
					
					if($menu['show_in_topmenu'] == 1){
					?>
						<li class="<?= $class ?>"><a href="<?= $relative_url ?>"><?= $description ?></a>
						<li class="<?= $class ?>"><a href="<?= $relative_url ?>" id="btnmenu<?= $menu['id_app'] ?>" data-toggle="collapse" data-target="#submenu<?= $menu['id_app'] ?>" aria-expanded="false"><?= $description ?></a>
							<ul class="nav collapse" id="submenu<?= $menu['id_app'] ?>" role="menu" aria-labelledby="btnmenu<?= $menu['id_app'] ?>">
								<li><a href="#">Link 2.1</a></li>
								<li><a href="#">Link 2.2</a></li>
								<li><a href="#">Link 2.3</a></li>
							</ul>
						</li>
					<?php
					}
				}
				?>
			</ul>
		</div><!-- /.navbar-collapse -->
	</nav>
</div>

<!--
<div class="topmenu">
	<nav role="navigation" class="navbar navbar-default navbar-static-top">
		<div class="container-fluid">
			<div class="navbar-header">
				<button data-target="#bs-example-navbar-collapse-1" data-toggle="collapse" class="navbar-toggle collapsed" type="button" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a href="index.html" class="navbar-brand">metisMenu</a>
			</div>
			
			<div id="bs-example-navbar-collapse-1" class="navbar-collapse collapse" aria-expanded="false" style="height: 1px;">
				<ul class="nav navbar-nav">
					<li class="active"><a href="index.html">Vertical Menu</a></li>
					<li><a href="metisFolder.html">Folder View</a></li>
					<li><a href="hover.html">Hover Option For Desktop</a></li>
					
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="#">Action</a></li>
							<li><a href="#">Another action</a></li>
							<li><a href="#">Something else here</a></li>
							<li role="separator" class="divider"></li>
							<li><a href="#">Separated link</a></li>
							<li role="separator" class="divider"></li>
							<li><a href="#">One more separated link</a></li>
						</ul>
					</li>
					
					<li><a href="zurb.html">Foundation | Zurb</a></li>
					
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="#">Action</a></li>
							<li><a href="#">Another action</a></li>
							<li><a href="#">Something else here</a></li>
							<li role="separator" class="divider"></li>
							<li><a href="#">Separated link</a></li>
							<li role="separator" class="divider"></li>
							<li><a href="#">One more separated link</a></li>
						</ul>
					</li>
					<li><a href="animate.html">Animate</a></li>
					<li><a href="event.html">Event</a></li>
					
				</ul>
				
				<ul class="nav navbar-nav pull-right">
					<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">
						<i class="fa fa-user"></i>
						<span class="user">(Not logged in) </span>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu pull-right" role="menu">
						<li><a href="../users/index.php?action=login">Log in</a></li>
					</ul>
				</li>
				</ul>
			</div>
		</div>
	</nav>
</div>
-->

<?php
// */


// /*
} else {
	
	include 'queries/pr_get_apps.php';

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
					<li><a href="../users/index.php?action=profileapps">Profiles - Apps</a></li>
					<li><a href="../users/index.php?action=profileappactions">Profiles - Actions</a></li>
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
					<!--<li><a href="../users/index.php?action=register">Register</a></li>-->
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