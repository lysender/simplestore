<?php

/** 
 * Overrides the Sprig core so that db instance name will use
 * the Kohana environment. To avoid ambiguity with the default
 * behavior of databases and kohana in general, 'default' will refer
 * to production and others will simply use the environment name
 * 
 * @author lysender
 *
 */
abstract class Sprig extends Sprig_Core
{	
	public function __construct()
	{
		// Set the database instance name from kohana environment
		// For production, db instance name is 'default', otherwise,
		// instance names will use the environment name
		if (Kohana::$environment != Kohana::PRODUCTION)
		{
			$this->_db = Kohana::$environment;
		}
		
		parent::__construct();
	}
}