<?php

interface Dc_Auth_UserInterface
{
	/** 
	 * Hashes a string
	 * 
	 * @param string $str
	 */
	public function hash($str);
	
	/** 
	 * Logs in the user based on the given credentials
	 * Also loads the user to the current user object
	 * 
	 * @param array $credentials
	 * @return boolean
	 */
	public function login(array $credentials);
	
	/** 
	 * Action after a successful login
	 * 
	 * @return $this
	 */
	public function after_login();
	
	/** 
	 * Loads the user record to the current object
	 * using the identity key
	 * 
	 * @param string $identity
	 * @return $this
	 */
	public function load_user($identity);
	
	/** 
	 * Loads the user record to the current object
	 * using the id key
	 * 
	 * @param int $id
	 * @return $this
	 */
	public function load_by_id($id);
	
	/** 
	 * Returns the identity of the user
	 * 
	 * @return string
	 */
	public function identity();
	
	/** 
	 * Returns TRUE if the user record has been
	 * loaded into the user object
	 * 
	 * @return boolean
	 */
	public function loaded();
}