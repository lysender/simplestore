<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'default' => array
	(
		'type'       => 'pdo',
		'connection' => array(
			/**
			 * The following options are available for MySQL:
			 *
			 * string   hostname
			 * integer  port
			 * string   socket
			 * string   username
			 * string   password
			 * boolean  persistent
			 * string   database
			 */
			'dsn'		 => 'mysql:host=localhost;dbname=simplestore',
			'username'   => 'simplestore',
			'password'   => 'simplestore',
			'persistent' => FALSE,
		),
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => FALSE,
	),
	'development' => array
	(
		'type'       => 'pdo',
		'connection' => array(
			/**
			 * The following options are available for MySQL:
			 *
			 * string   hostname
			 * integer  port
			 * string   socket
			 * string   username
			 * string   password
			 * boolean  persistent
			 * string   database
			 */
			'dsn'		 => 'mysql:host=localhost;dbname=simplestore',
			'username'   => 'simplestore',
			'password'   => 'simplestore',
			'persistent' => FALSE,
		),
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => TRUE,
	),
	'testing' => array
	(
		'type'       => 'pdo',
		'connection' => array(
			/**
			 * The following options are available for MySQL:
			 *
			 * string   hostname
			 * integer  port
			 * string   socket
			 * string   username
			 * string   password
			 * boolean  persistent
			 * string   database
			 */
			'dsn'		 => 'mysql:host=localhost;dbname=simplestore',
			'username'   => 'simplestore',
			'password'   => 'simplestore',
			'persistent' => FALSE,
		),
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => TRUE,
	),
	'staging' => array
	(
		'type'       => 'pdo',
		'connection' => array(
			/**
			 * The following options are available for MySQL:
			 *
			 * string   hostname
			 * integer  port
			 * string   socket
			 * string   username
			 * string   password
			 * boolean  persistent
			 * string   database
			 */
			'dsn'		 => 'mysql:host=localhost;dbname=simplestore',
			'username'   => 'simplestore',
			'password'   => 'simplestore',
			'persistent' => FALSE,
		),
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => FALSE,
	)
);
