<h1><strong>Join</strong> and be a StreetWatcher</h1>

<div id="form-wrapper" class="span-14">
	<?php if (isset($error_message)): ?>
	<p class=error><?php echo $error_message ?></p>
	<?php endif ?>
	<form action="<?php echo URL::site('/signup') ?>" method="post" enctype="multipart/form-data">
		<div class="span-4"><?php echo $signup->label('username') ?></div>
		<div class="span-9"><?php echo $signup->input('username') ?></div>

		<div class="span-4"><?php echo $signup->label('email') ?></div>
		<div class="span-9"><?php echo $signup->input('email') ?></div>
		
		<div class="span-4"><?php echo $signup->label('password') ?></div>
		<div class="span-9"><?php echo $signup->input('password') ?></div>
		
		<div class="span-4"><?php echo $signup->label('password_confirm') ?></div>
		<div class="span-9"><?php echo $signup->input('password_confirm') ?></div>
		
		<div class="span-4">&nbsp;<?php echo $signup->input('csrf') ?></div>
		<div class="span-9"><?php echo Form::submit('submit', 'Signup') ?></div>
	</form>
</div>