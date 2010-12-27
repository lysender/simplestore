<?php

class Dc_Model_UserToken extends Sprig implements Dc_Auth_TokenInterface
{
	protected $_table = 'user_token';
	
	/** 
	 * @var int
	 */
	protected $_now;
	
	/**
	 * Initialize the sprig model
	 *  
	 * (non-PHPdoc)
	 * @see modules/sprig/classes/sprig/Sprig_Core::_init()
	 */
	protected function _init()
	{
		$this->_fields += array(
			'id'			=> new Sprig_Field_Auto,
			'user_id'		=> new Sprig_Field_Integer,
			'user_agent'	=> new Sprig_Field_Char,
			'user_ip'		=> new Sprig_Field_Char,
			'token'			=> new Sprig_Field_Char,
			'created'		=> new Sprig_Field_Timestamp,
			'expires'		=> new Sprig_Field_Timestamp,
			'activation'	=> new Sprig_Field_Integer(array(
				'default' => 0
			))
		);
		
		// Set the now, we use this a lot
		$this->_now = time();
	}
	
	/** 
	 * Loads the token record to this token object
	 * Should not load expired tokens
	 * 
	 * @param string $token
	 */
	public function load_token($token)
	{
		$this->token = $token;
		
		$query = DB::select()->where('expires', '>=', $this->_now);
		
		$this->load($query);
		
		return $this;
	}
	
	/**
	 * Returns true if the token is valid
	 * Token must not be expired
	 * Token must match the current user agent
	 *  
	 * (non-PHPdoc)
	 * @see modules/dc/classes/dc/auth/Dc_Auth_TokenInterface::valid_token()
	 * 
	 * @return boolean
	 */
	public function valid_token()
	{
		if ($this->loaded() && $this->expires >= $this->_now)
		{
			if ($this->user_agent === sha1(Request::$user_agent))
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	/**
	 * Generates a new token for the specified user
	 *  
	 * (non-PHPdoc)
	 * @see modules/dc/classes/dc/auth/Dc_Auth_TokenInterface::generate()
	 * 
	 * @param int $user_id
	 * @param int $expires
	 * @return $this
	 */
	public function generate($user_id, $expires)
	{
		$this->user_id = $user_id;
		$this->expires = $expires;
		
		$this->_before_save();
		
		return $this->create();
	}
	
	/** 
	 * Regenerate from a loaded token to create a new unique token
	 * 
	 * (non-PHPdoc)
	 * @see modules/dc/classes/dc/auth/Dc_Auth_TokenInterface::regenerate()
	 * 
	 * @param int $expires
	 * @return $this
	 */
	public function regenerate($expires)
	{
		$this->expires = $expires;
		
		$this->_before_save();
		
		return $this->update();
	}
	
	/** 
	 * Fills user data before the record is created or updated
	 * 
	 * @return void
	 */
	protected function _before_save()
	{
		$this->user_agent = sha1(Request::$user_agent);
		$this->user_ip = Request::$client_ip;
		$this->token = Text::random('alnum', 32);
		$this->created = $this->_now;
	}
	
	/**
	 * Deletes all tokens for a certain user
	 *  
	 * (non-PHPdoc)
	 * @see modules/dc/classes/dc/auth/Dc_Auth_TokenInterface::delete_by_user()
	 * 
	 * @param string $user_id
	 */
	public function delete_by_user($user_id)
	{
		DB::delete($this->_table)
			->where('user_id', '=', $user_id)
			->execute($this->_db);
		
		return $this;
	}
	
	/**
	 * Deletes all expired tokens.
	 * Performed via cron
	 *
	 * @return Sprig
	 */
	public function delete_expired()
	{
		// Delete all expired tokens
		// Delete tokens only for autologin
		DB::delete($this->_table)
			->where('expires', '<', $this->_now)
			->where('activation', '=', 0)
			->execute($this->_db);

		return $this;
	}
}