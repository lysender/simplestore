<div id="foot_nav" class="span-14 prefix-5 suffix-5 last">
	<ul>
		<li><?php echo HTML::anchor('/manual', 'Manual') ?></li>
		<li><?php echo HTML::anchor('/about', 'About') ?></li>
		<li><?php echo HTML::anchor('/faq', 'FAQ') ?></li>
		<li><?php echo HTML::anchor('/contribute', 'Contribute') ?></li>
		<li><?php echo HTML::anchor('/credits', 'Credits') ?></li>
		<li><?php echo HTML::anchor('/contact', 'Contact') ?></li>
	</ul>
	<p><?php echo HTML::anchor('/', 'SimpleStore - Sales and Inventory System') ?></p>
</div>

<?php if (Kohana::$environment == Kohana::DEVELOPMENT && Kohana::$profiling): ?>
<!-- Profiler stats -->
<div id="kohana-profiler" class="span-24">
	<?php echo View::factory('profiler/stats') ?>
</div>
<?php endif ?>
