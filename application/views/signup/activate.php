<?php if (isset($error_message)): ?>
<p class="error"><?php echo $error_message ?></p>

<p>There are errors on you account activation. Verify the following:</p>
<ul>
	<li>Make sure you have clicked the activation link on the activation email</li>
	<li>Make sure that you activate your account within 24 hours</li>
	<li>If you cannot activate your account within 24 hours, you can signup again the next day</li>
</ul>
<?php elseif (isset($success_message)): ?>
<p class="success"><?php echo $success_message ?></p>

<p><strong>Congratulations <?php echo $activated_user ?>!</strong> Your account is now active. To get started, you may now:</p>
<ul>
	<li>Browse for traffic updates</li>
	<li>Submit traffic status</li>
	<li>Bookmark /create traffic routes</li>
</ul>
<?php endif ?>