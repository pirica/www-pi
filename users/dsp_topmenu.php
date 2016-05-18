<?php


// /*
if(isset($_GET['newmenu'])) {
	
	include 'queries/pr_get_apps_actions.php';

?>

<div class="topmenu1">
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
			<ul class="nav navbar-nav nav-user pull-right">
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

	</nav>
</div>


<div class="topmenu2">
	<nav class="navbar navbar-default navbar-inverse" role="navigation">
		
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
					
					/*if(isset($loggedin) && $loggedin === true){
						
					}
					else if($menu['login_required'] == 1) {
						$class .= ' disabled';
						$relative_url = "#";
					}*/
					
					if($menu['show_in_topmenu'] == 1){
						
						if($menu['menu_actions'] == 0)
						{
							?>
								<li class="<?= $class ?>"><a href="<?= $relative_url ?>"><?= $description ?></a></li>
							<?php
						}
						else
						{
							$class .= ' subs';
							?>
								<li class="<?= $class ?>"><a href="#" id="btnmenu<?= $menu['id_app'] ?>" data-toggle="collapse" data-target="#submenu<?= $menu['id_app'] ?>" aria-expanded="<?= ($menu['is_current'] == 1 ? 'true' : 'false') ?>" class="<?= ($menu['is_current'] == 1 ? '' : 'collapsed') ?>"><?= $description ?></a>
									<ul class="nav collapse <?= ($menu['is_current'] == 1 ? 'in' : '') ?>" id="submenu<?= $menu['id_app'] ?>" role="menu" aria-labelledby="btnmenu<?= $menu['id_app'] ?>">
										<?php
											$class = '';
											
											mysql_data_seek($qry_actions, 0);
											
											while($menu_action = mysql_fetch_array($qry_actions))
											{
												if($menu_action['id_app'] == $menu['id_app'] && $menu_action['show_in_menu'] == 1)
												{
													if($menu['is_current'] == 1 && $menu_action['code'] == $action->getCode()){
														$class .= ' active';
													}
													echo '<li class="' . $class . '"><a href="' . $relative_url . '?action=' . $menu_action['code'] . '">' . $menu_action['page_title'] . '</a></li>';
												}
											}
										?>
									</ul>
								</li>
							<?php
						}
					}
				}
				?>
			</ul>
		</div><!-- /.navbar-collapse -->
	</nav>
</div>


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