<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'hash_method'	=> 'sha1',				// Hash method to use for password hashing
	'salt_pattern'	=> '1, 3, 5, 9, 14, 15, 20, 21, 28, 30',	// Salt pattern in which the salt is inserted to hashed password
	'lifetime'		=> 1209600,				// Autologin cookie stays for 2 weeks
	'session_key'	=> 'dcauthuserstwph',	// Session key to use when storing auth user key
	'cookie_key'	=> 'dcautologinstwph'	// Cookie key to use when storing autologin token
);