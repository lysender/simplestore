<div class="container">
	<div id="head-top">
		<h1>
			<?php echo HTML::anchor('/', 'SimpleStore') ?>
			<span class="caps thin">Sales and Inventory System</span>
		</h1>
		
		<p id="user-bar">
			<?php if (isset($current_user)): ?>
				<?php echo HTML::anchor('/user/'.$current_user, $current_user) ?> |
				<?php echo HTML::anchor('/login/logout/'.Security::token(), 'logout') ?>
			<?php else: ?>
				&nbsp;
			<?php endif ?>
		</p>
	</div>
</div>

<div id="head-nav">
	<div class="container">
		<ul>
		<?php if (isset($current_user)): ?>
			<li<?php echo $headnav_class['dashboard'] ?>><a href="<?php echo URL::site('/') ?>">Current stats <strong>Dashboard</strong></a></li>
			<li<?php echo $headnav_class['inventory'] ?>><a href="<?php echo URL::site('/inventory') ?>">Item Management <strong>Inventory</strong></a></li>
			<li<?php echo $headnav_class['sales'] ?>><a href="<?php echo URL::site('/sales') ?>">Item Movement <strong>Sales</strong></a></li>
			<li<?php echo $headnav_class['report'] ?>><a href="<?php echo URL::site('/report') ?>">Store Performance <strong>Reports</strong></a></li>
			<li<?php echo $headnav_class['security'] ?>><a href="<?php echo URL::site('/security') ?>">System and Users <strong>Security</strong></a></li>
		<?php else: ?>
			<li class="selected"><a href="<?php echo URL::site('/login') ?>">Authenticate<strong>Login</strong></a></li>
		<?php endif ?>
		</ul>
	</div>
</div>
