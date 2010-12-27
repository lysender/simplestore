<?php

return array(
	'username' => array(
		'not_empty' => 'Username is not entered'
	),
	'password' => array(
		'not_empty' => 'Password is not entered'
	),
	'csrf' => array(
		'not_empty' => 'Security token is missing',
		'matches' => 'Session timeout, try again'
	)
);