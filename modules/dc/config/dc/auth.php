<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'lifetime'		=> 1209600,			// Autologin cookie stays for 2 weeks
	'session_key'	=> '_dcausess_',	// Session key to use when storing auth user key
	'cookie_key'	=> '_dcaacooks_',	// Cookie key to use when storing autologin token
	'allow_autologin' => TRUE
);