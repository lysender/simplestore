<?php

return array(
	'username' => array(
		'not_empty' => 'Username is not entered',
		'regex' => 'Username must be composed of letters and numbers',
		'min_length' => 'Username must be at least :param1 characters',
		'max_length' => 'Username must be at most :param1 characters',
		'unique' => 'Username is already taken'
	),
	'email' => array(
		'not_empty' => 'Email is not entered',
		'min_length' => 'Email must be at least :param1 characters',
		'max_length' => 'Email must be at most :param1 characters',
		'email' => 'Email is invalid',
		'unique' => 'Email i already exists'
	),
	'password' => array(
		'not_empty' => 'Password is not entered',
		'min_length' => 'Password must be at least :param1 characters',
		'max_length' => 'Password must be at most :param1 characters',
	),
	'password_confirm' => array(
		'matches' => 'Passwords did not match'
	),
	'csrf' => array(
		'not_empty' => 'Security token is missing',
		'matches' => 'Session timeout, try again'
	)
);