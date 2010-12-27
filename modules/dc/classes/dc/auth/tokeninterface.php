<?php

interface Dc_Auth_TokenInterface
{
	/** 
	 * Loads the token record to the current object
	 * using the token key
	 * 
	 * @param string $token
	 * @return $this
	 */
	public function load_token($token);
	
	/** 
	 * Returns TRUE if the token record has been
	 * loaded into the token object
	 * 
	 * @return boolean
	 */
	public function loaded();
	
	/** 
	 * Returns true if the token is valid in context
	 * 
	 * @return boolean
	 */
	public function valid_token();
	
	/** 
	 * Generates a new token based on the given user id
	 * 
	 * @param int $user_id
	 * @param int $expires
	 * @return $this
	 */
	public function generate($user_id, $expires);
	
	/** 
	 * Regenerates a new unique token
	 * 
	 * @param int $expires
	 * @return $this
	 */
	public function regenerate($expires);
	
	/** 
	 * Deletes the current token
	 * 
	 * @return $this
	 */
	public function delete();
	
	/** 
	 * Deletes all tokens for a certain user
	 * 
	 * @param string $user_id
	 */
	public function delete_by_user($user_id);
	
	/** 
	 * Deletes all expired token
	 * 
	 * @return $this
	 */
	public function delete_expired();
}