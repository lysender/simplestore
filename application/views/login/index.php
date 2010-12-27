<h1><strong>Login</strong></h1>

<div id="form-wrapper" class="span-12">
	<?php if (isset($error_message)): ?>
	<p class=error><?php echo $error_message ?></p>
	<?php endif ?>
	<form action="<?php echo URL::site('/login') ?>" method="post" enctype="multipart/form-data">
		<div class="span-2"><?php echo $login->label('username') ?></div>
		<div class="span-9"><?php echo $login->input('username') ?></div>
		
		<div class="span-2"><?php echo $login->label('password') ?></div>
		<div class="span-9"><?php echo $login->input('password') ?></div>
		
		<div class="span-2">&nbsp;<?php echo $login->input('csrf') ?></div>
		<div class="span-9"><?php echo Form::submit('submit', 'Login') ?></div>
	</form>
</div>