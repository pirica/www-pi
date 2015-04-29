
<ul class="nav nav-tabs">
	<li class="<?= ($action == 'main' ? 'active' : '') ?>"><a href="?action=main">Overview</a></li>
	
	<li class="<?= ($action == 'messages' ? 'active' : '') ?>"><a href="?action=messages">Messages</a></li>
	<li class="<?= ($action == 'alerts_email' ? 'active' : '') ?>"><a href="?action=alerts_email">Email alerts</a></li>
	<li class="<?= ($action == 'alerts_tt' ? 'active' : '') ?>"><a href="?action=alerts_tt">Track&amp;Trace alerts</a></li>
	<li class="<?= ($action == 'php_errors' ? 'active' : '') ?>"><a href="?action=php_errors">PHP errors</a></li>
	
	<li class="dropdown pull-right <?= (substr($action, 0, 13) == 'manage_alerts' ? 'active' : '') ?>">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#">
			Manage alerts
			<span class="caret"></span>
		</a>
		<ul class="dropdown-menu" role="menu">
			<li class="<?= ($action == 'manage_alerts_email' ? 'active' : '') ?>"><a href="?action=manage_alerts_email">Email alerts</a></li>
			<li class="<?= ($action == 'manage_alerts_tt' ? 'active' : '') ?>"><a href="?action=manage_alerts_tt">Track&amp;Trace alerts</a></li>
		</ul>
	</li>
	
</ul>
