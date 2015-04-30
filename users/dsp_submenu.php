<!--
<ul class="nav nav-tabs">
	<li class="<?= ($action->getCode() == 'main' ? 'active' : '') ?>"><a href="?action=main">Overview</a></li>
	<li class="<?= ($action->getCode() == 'status' ? 'active' : '') ?>"><a href="?action=status">Status</a></li>
	
	<li class="dropdown <?= (substr($action->getCode(), 0, 5) == 'usage' ? 'active' : '') ?>">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#">
			Usage
			<span class="caret"></span>
		</a>
		<ul class="dropdown-menu" role="menu">
			<li class="<?= ($action->getCode() == 'usage_now' ? 'active' : '') ?>"><a href="?action=usage_now">Now</a></li>
			<li class="<?= ($action->getCode() == 'usage_today' ? 'active' : '') ?>"><a href="?action=usage_today">Today</a></li>
			<li class="<?= ($action->getCode() == 'usage_day' ? 'active' : '') ?>"><a href="?action=usage_day">Per day</a></li>
			<li class="<?= ($action->getCode() == 'usage_month' ? 'active' : '') ?>"><a href="?action=usage_month">Per month</a></li>
		</ul>
	</li>
	
	<li class="<?= ($action->getCode() == 'config' ? 'active' : '') ?> pull-right"><a href="?action=config">Configure</a></li>
	
</ul>
-->