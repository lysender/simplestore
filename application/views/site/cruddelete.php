<div id="form-wrapper" class="span-14 last">
	<p class="menu-nav"><a href="<?php echo $delete_referer ?>">Back to list</a></p>
	
	<h2 class="thin">Delete <?php echo $delete_subject ?></h2>
	
	<div id="form_wrapper" class="span-14 last">
		<?php if (isset($error_message) && $error_message): ?>
		<p class=error><?php echo $error_message ?></p>
		<?php endif ?>
		
		<p class="notice">Are you sure you want to delete this record?</p>
		
		<form action="<?php echo $delete_target ?>" method="post" enctype="multipart/form-data">
			<div class="span-3"><label>Record key</label></div>
			<div class="span-10 last"><span><?php echo HTML::chars($delete_record_key) ?></span></div>
	
			<div class="span-3"><label>Detail</label></div>
			<div class="span-10 last"><span><?php echo HTML::chars($delete_record_detail) ?></span></div>
			
			<div class="span-3">&nbsp;</div>
			<div class="span-10 last">
				<input type="submit" id="yes" name="yes" value="Yes" /> &nbsp;
				<input type="submit" id="no" name="no" value="No" />
				<?php echo Form::hidden('csrf', Security::token(TRUE)),
					Form::hidden('referer', $delete_referer),
					Form::hidden('target', $delete_target) ?>
			</div>
		</form>
	</div>
</div>