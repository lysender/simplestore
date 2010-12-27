<form id="crud-pre-delete" action="/" method="post" enctype="multipart/form-data">
	<div>
		<input type="hidden" name="referer" id="referer" />
		<input type="hidden" name="target" id="target" />
		<input type="hidden" name="yes" id="yes" value="Yes" />
		<input type="hidden" name="csrf" id="csrf" value="<?php echo Security::token(TRUE) ?>" />
	</div>
</form>