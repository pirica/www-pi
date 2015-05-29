<?php

include 'queries/pr_get_apps.php';

// /*
if(isset($_GET['newmenu'])) {

?>

<div class="topmenu">
	<div class="navbar navbar-default navbar-fixed-top" role="navigation">

		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		
		<a class="navbar-brand" href="#">My Application</a>
		
		<div class="dropdown">
			<a class="navbar-right navbar-text cursor" data-toggle="dropdown" data-target=".dropdown-menu">
				<i class="fa fa-user"></i>
				<span class="user"><?= (isset($loggedin) && $loggedin === true ? $_SESSION['username_safe'] : '(Not logged in) ') ?></span>
				<span class="caret"></span>
			</a>
			
			<ul class="dropdown-menu pull-right" role="menu">
				<?php
				if(isset($loggedin) && $loggedin === true){
					?>
					<li class="disabled"><a href="#">My profile</a></li>
					<li class="disabled"><a href="#">My stats</a></li>
					<li class="divider"></li>
					<li><a href="../users/index.php?action=settings"><i class="fa fa-gear"></i> Settings</a></li>
					<li><a href="../users/index.php?action=action"><i class="fa fa-gear"></i> Actions</a></li>
					<li class="divider"></li>
					<li><a href="../users/index.php?action=logout"><i class="fa fa-power-off"></i> Log out</a></li>
				<?php
				}
				else {
					?>
					<li><a href="../users/index.php?action=login"><i class="fa fa-power-on"></i> Log in</a></li>
					<li><a href="../users/index.php?action=register">Register</a></li>
				<?php
				}
				?>
			</ul>
		</div>

		<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
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
		</div>

	</div>
</div>

<?php

// */

} else if(isset($_GET['submenu'])) {


?>

<div class="topmenu">
	<!--div class="dd-button"><i class="fa fa-3x fa-bars"></i></div-->
	<ul class="nav nav-tabs">
		<?php
		if(isset($loggedin) && $loggedin === true){
            $menu = '---';
            $m = count($app->getMenuDataSubs());
			for ($i = 0; $i<$m; $i++) {
			
				$class = '';
				if($app->getMenuDataSubs()[$i]['is_current'] == 1){
					$class .= ' active';
				}
				
				?>
                    <li class="dropdown <?= $class ?>">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?= $app->getMenuDataSubs()[$i]['menu'] ?></a>
                        <ul class="dropdown-menu" role="menu">
				<?php
				
				//if($show_in_topmenu == 1)
				{
                    $n = count($app->getMenuDataSubs()[$i]['items']);
                    for ($j = 0; $j<$n; $j++) {
                                
                        $class = '';
                        if($app->getMenuDataSubs()[$i]['items'][$j]['is_current'] == 1){
                            $class .= ' active';
                        }
                        
                        ?>
                        <li class="<?= $class ?>"><a href="<?= $app->getMenuDataSubs()[$i]['items'][$j]['relative_url'] ?>"><?= $app->getMenuDataSubs()[$i]['items'][$j]['description'] ?></a></li>
                        <?php
                    }
				}
                ?>
                        </ul>
                    </li>
				<?php
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