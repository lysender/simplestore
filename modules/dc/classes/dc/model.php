<?php

/** 
 * Basic modeling for the DC lib
 * 
 * @author lysender
 *
 */
abstract class Dc_Model
{
	/** 
	 * Database instance name
	 * 
	 * @var string
	 */
	protected $_db = 'default';

	/**
	 * Loads the database.
	 *
	 *     $model = new Foo_Model($db);
	 *
	 * @param   mixed  Database instance object or string
	 * @return  void
	 */
	public function __construct($db = NULL)
	{
		if ($db !== NULL)
		{
			// Set the database instance name
			$this->_db = $db;
		}
		else 
		{
			// Set the database instance name from kohana environment
			// For production, db instance name is 'default', otherwise,
			// instance names will use the environment name
			if (Kohana::$environment != Kohana::PRODUCTION)
			{
				$this->_db = Kohana::$environment;
			}
		}
	}
}