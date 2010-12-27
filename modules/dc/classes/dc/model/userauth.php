<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * User model for the auth
 */
class Dc_Model_Userauth extends Sprig implements Dc_Auth_UserInterface
{
	/** 
	 * Table name
	 * 
	 * @var string
	 */
	protected $_table = 'user';
	
	/** 
	 * Salt for hashing
	 * 
	 * @var string
	 */
	protected $_secret_key = 'gAcCNWHM4ysWyhYTHF8kygWfoTu2bq9L';
	
	/** 
	 * Identity field used by auth object
	 * 
	 * @var string
	 */
	protected $_identity_field = 'username';
	
	/**
	 * Initialize model
	 * 
	 * (non-PHPdoc)
	 * @see modules/sprig/classes/sprig/Sprig_Core::_init()
	 */
	protected function _init()
	{
		$this->_fields += array(
			'id' => new Sprig_Field_Auto,
			'username' => new Sprig_Field_Char(array(
				'empty'  => FALSE,
				'unique' => TRUE,
				'min_length' => 4,
				'max_length' => 32,
				'rules'  => array(
					'regex' => array('/^[-\pL\pN_.]++$/uD')
				)
			)),
			'email' 	=> new Sprig_Field_Email(array(
				'unique' => TRUE,
				'min_length' => 4,
				'max_length' => 127
			)),
			'password' => new Sprig_Field_Password(array(
				'hash_with' => NULL,
				'empty' => FALSE,
				'min_length' => 5,
				'max_length' => 64,
				'callbacks' => array(
					'hash_password' => array($this, 'hash_password')
				)
			)),
			'logins'	=> new Sprig_Field_Integer(array(
				'default'	=> 0
			)),
			'last_login'=> new Sprig_Field_Timestamp(array(
				'null'		=> true
			)),
			'last_login_ip'=> new Sprig_Field_Char(array(
				'null'		=> true
			)),
			'banned'	=> new Sprig_Field_Integer(array(
				'default' => 0
			)),
			'active'	=> new Sprig_Field_Integer(array(
				'default' => 0
			)),
			'date_joined' => new Sprig_Field_Integer(array(
				'empty' => FALSE
			))
		);
	}
	
	/**
	 * Hashes the string
	 * 
	 * (non-PHPdoc)
	 * @see modules/dc/classes/dc/auth/Dc_Auth_UserInterface::hash()
	 */
	public function hash($str)
	{
		return hash_hmac('sha256', $str, $this->_secret_key);
	}
	
	/**
	 * Callback for hashing passwords
	 *
	 * @param Validate $validate
	 * @param string $field
	 * @return void
	 */
	public function hash_password(Validate $validate, $field)
	{		
		$validate[$field] = $this->hash($validate[$field]);
	}
	
	/**
	 * Returns the user's identity
	 *  
	 * (non-PHPdoc)
	 * @see modules/dc/classes/dc/auth/Dc_Auth_UserInterface::identity()
	 * 
	 * @return mixed
	 */
	public function identity()
	{
		if ($this->loaded())
		{
			$field = $this->_identity_field;
		
			return $this->$field;
		}
		
		return NULL;
	}
	
	/** 
	 * Logins in the user based on the given credentials
	 * Also loads the user to the current user object
	 * 
	 * (non-PHPdoc)
	 * @see modules/dc/classes/dc/auth/Dc_Auth_UserInterface::login()
	 * 
	 * @return boolean
	 */
	public function login(array $credentials)
	{
		$required = array('username', 'password');
		
		foreach ($required as $field)
		{
			if ( ! isset($credentials))
			{
				throw new Dc_Auth_Exception('Required field '.$field.' is not specified');
			}
		}
		
		$this->load_user($credentials['username']);
		
		if ($this->loaded())
		{
			if ($this->password === $this->hash($credentials['password']))
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	/**
	 * Action after a succesful login
	 *  
	 * (non-PHPdoc)
	 * @see modules/dc/classes/dc/auth/Dc_Auth_UserInterface::after_login()
	 * 
	 * @return $this
	 */
	public function after_login()
	{
		// Update the number of logins
		$this->logins = $this->logins + 1;

		// Set the last login date
		$this->last_login = time();
		
		// Set the last login ip
		$this->last_login_ip = Request::$client_ip;

		// Save the user data
		$this->update();
		
		return $this;
	}
	
	/**
	 * Loads the user based on the specified identity
	 * User must be active
	 * User must not be banned
	 *  
	 * (non-PHPdoc)
	 * @see modules/dc/classes/dc/auth/Dc_Auth_UserInterface::load_user()
	 * 
	 * @param string $identity
	 * @return $this
	 */
	public function load_user($identity)
	{
		return $this->_load_user('username', $identity);
	}
	
	/** 
	 * Loads the user by id
	 * 
	 * @param int $user_id
	 */
	public function load_by_id($user_id)
	{
		return $this->_load_user('id', (int) $user_id);
	}
	
	/** 
	 * Loads the user using the specified key
	 * 
	 * @param string $field
	 * @param mixed $value
	 * @throws Dc_Auth_Exception
	 */
	public function _load_user($field, $value)
	{
		// User must not be loaded
		if ($this->loaded())
		{
			throw new Dc_Auth_Exception('No user must be loaded before loading user');
		}
		
		$this->$field = $value;
		
		$query = DB::select()
			->where('active', '=', 1)
			->where('banned', '=', 0);
		
		$this->load($query);
		
		return $this;
	}
}