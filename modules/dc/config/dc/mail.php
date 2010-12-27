<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'smtp_server'	=> 'smtp.gmail.com',
	'port'			=> 465,
	'enc'			=> true,
	'enc_type'		=> 'tls',
	'username'		=> 'username@gmail.com',
	'password'		=> 'password',
	'from'			=> array('lysender.mailer@gmail.com' => 'User name')
);