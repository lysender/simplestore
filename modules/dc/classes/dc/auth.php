<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Basic authentication class for the Dc library
 * Components:
 * 		session (Kohana_Session)
 * 		user model Dc_Auth_UserInterface
 * 		token model Dc_Auth_TokenInterface
 * 
 * Based on Kohana_Auth module to be more flexible
 */
class Dc_Auth
{
	/** 
	 * Auth instance - singleton
	 * 
	 * @var Dc_Auth
	 */
	protected static $_instance;

	/** 
	 * @var Session
	 */
	protected $_session;
	
	/** 
	 * Key used for hashing
	 * 
	 * @var string
	 */
	protected $_secret_key;
	
	/** 
	 * Number if seconds the autologin will be active
	 * Default is two weeks
	 * 
	 * @var int
	 */
	protected $_lifetime = 1209600;
	
	/** 
	 * Session key used for the auth module
	 * Recommended to override for different apps
	 * 
	 * @var string
	 */
	protected $_session_key = '_dcausess_';
	
	/** 
	 * Cookie key used for autologin
	 * Recommended to override for different apps
	 * 
	 * @var string
	 */
	protected $_cookie_key = '_dcaacooks_';
	
	/** 
	 * When true, allows autologin
	 * 
	 * @var boolean
	 */
	protected $_allow_autologin = TRUE;
	
	/** 
	 * Currently logged user
	 * 
	 * @var Dc_Auth_UserInterface
	 */
	protected $_user;
	
	/** 
	 * Currently used token for autologin
	 * 
	 * @var Dc_Auth_TokenInterface
	 */
	protected $_token;
	
	/**
	 * Singleton pattern
	 *
	 * @return Dc_Auth
	 */
	public static function instance()
	{
		if ( ! isset(Dc_Auth::$_instance))
		{
			self::$_instance = new self;
		}
		
		return self::$_instance;
	}
	
	/**
	 * Singleton pattern
	 *
	 * @return void
	 */
	protected function __construct()
	{	
		$config = Kohana::config('dc/auth');
		
		$this->set_options($config->as_array());
		
		// Set session object in advanced
		$this->_session = Session::instance();
	}
	
	/** 
	 * Prevents the object from being cloned externally
	 * 
	 */
	protected function __clone()
	{
		
	}
	
	/** 
	 * Sets the options for the auth object
	 * 
	 * @param array $options
	 * @return $this
	 */
	public function set_options(array $options)
	{
		foreach ($options as $key => $value)
		{
			$prop = '_'.$key;
			
			if (property_exists($this, $prop))
			{
				$this->$prop = $value;
			}
		}
		
		return $this;
	}
	
	/** 
	 * Clears the auth object
	 * 
	 * @return $this
	 */
	public function clear()
	{
		$this->_token = NULL;
		$this->_user = NULL;
		
		return $this;
	}
	
	/** 
	 * Returns the currently authenticated user
	 * 
	 * @return Dc_Auth_UserInterface
	 */
	public function get_user(Dc_Auth_UserInterface $user, Dc_Auth_TokenInterface $token = NULL)
	{
		if ($this->_user === NULL)
		{
			$identity = $this->_session->get($this->_session_key);
			
			if ( ! empty($identity))
			{
				$user->load_user($identity);
				
				if ($user->loaded())
				{
					$this->_user = $user;
				}
			}
			elseif ($this->_allow_autologin && $token !== NULL)
			{
				// Check for "remembered" login
				$this->_user = $this->auto_login($user, $token);
			}
		}
		return $this->_user;
	}
	
	/** 
	 * Returns the currently used token
	 * Only works if autologin is used
	 * 
	 * @return Sprig
	 */
	public function get_token()
	{
		return $this->_token;
	}
	
	/** 
	 * Logs in the user
	 * If token is present, autologin is enabled
	 * 
	 * @param Dc_Auth_UserInterface $user
	 * @param array $credentials
	 * @param Dc_Auth_TokenInterface $token
	 * 
	 * @return boolean
	 */
	public function login(Dc_Auth_UserInterface $user, array $credentials, Dc_Auth_TokenInterface $token = NULL)
	{
		if ($user->login($credentials))
		{
			if ($this->_allow_autologin && $token)
			{
				$this->_remember_login($user, $token);
			}
			
			// Finish the login process
			$this->_complete_login($user);
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	/** 
	 * Logs in a user without specifying a password
	 * 
	 * @param Dc_Auth_UserInterface $user
	 * @param mixed $identity
	 * @param Dc_Auth_TokenInterface $token
	 * @return boolean
	 */
	public function force_login(Dc_Auth_UserInterface $user, $identity, Dc_Auth_TokenInterface $token = NULL)
	{
		$user->load_user($identity);
		
		if ($user->loaded())
		{
			$this->_user = $user;
			
			if ($this->_allow_autologin && $token)
			{
				$this->_remember_login($user, $token);
			}
			
			// Run the standard completion
			$this->_complete_login($user);
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	/** 
	 * Logs out the user by removing the related session variable
	 * 
	 * @param Dc_Auth_TokenInterface $token
	 * @param boolean $destroy
	 * @param boolean $logout_all
	 */
	public function logout(Dc_Auth_TokenInterface $token = NULL, $destroy = FALSE, $logout_all = FALSE)
	{
		// Delete cookie and tokens
		$strtoken = Cookie::get($this->_cookie_key);
		
		// Although autologin may be disabled, tokens are 
		// still cleared when specified
		if ($strtoken && $token)
		{
			// Delete the autologin cookie to prevent re-login
			Cookie::delete($this->_cookie_key);

			$token->load_token($strtoken);

			if ($logout_all)
			{
				$token->delete_by_user($user_id);
			}
			elseif ($token->loaded())
			{
				$token->delete();
			}
		}
		
		// Regenerate new security token
		Security::token(TRUE);
		
		if ($destroy === TRUE)
		{
			// Destroy the session completely
			$this->_session->destroy();
		}
		else
		{
			// Remove the user from the session
			$this->_session->delete($this->_session_key);

			// Regenerate session_id
			$this->_session->regenerate();
		}

		// Remove the user from auth object
		$this->_user = NULL;
		
		// Remove the token used if present
		$this->_token = NULL;
		
		// Double check
		return ! $this->logged_in();
	}
	
	public function logged_in()
	{
		if ($this->_user !== NULL)
		{
			if ($this->_user->loaded())
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	/**
	 * Logs a user in, based on the authautologin cookie
	 *
	 * @return  mixed
	 */
	public function auto_login(Dc_Auth_UserInterface $user, Dc_Auth_TokenInterface $token)
	{
		$strtoken = Cookie::get($this->_cookie_key);
		
		if ($strtoken && $token && $this->_allow_autologin)
		{
			// Load the token and user
			$token->load_token($strtoken);
			
			if ($token->loaded())
			{
				$user->load_by_id($token->user_id);
			}

			if ($user->loaded() && $token->valid_token())
			{
				// Save the token to create a new unique token
				$token->regenerate($this->_lifetime);

				// Set the new token
				Cookie::set($this->_cookie_key, $token->token, $token->expires - time());

				// Set the token used
				$this->_token = $token;
				
				// Complete the login with the found data
				$this->_complete_login($user);

				// Automatic login was successful
				return $user;
			}
			else
			{
				// Token is invalid
				$token->delete();
			}
		}

		return NULL;
	}
	
	/** 
	 * Completes the login process by regenerating session id
	 * and setting the session key
	 * 
	 * This also sets the number of logins and the time of
	 * last login
	 * 
	 * @param Sprig $user
	 */
	protected function _complete_login(Sprig $user)
	{
		$user->after_login();
		
		// Regenerate session_id
		$this->_session->regenerate();

		// Store identity in session
		$this->_session->set($this->_session_key, $user->identity());

		// Regenerate new token
		Security::token(TRUE);
		
		$this->_user = $user;
		
		return $this;
	}
	
	/** 
	 * Remembers the user's login so that when the user visits
	 * the site again, he/she is automatically logged in
	 * 
	 * @param Sprig $user
	 */
	protected function _remember_login(Dc_Auth_UserInterface $user, Dc_Auth_TokenInterface $token)
	{
		// Create a new autologin token
		$token->generate($user->id, $this->_lifetime);

		// Set the token used
		$this->_token = $token;
		
		// Set the autologin cookie
		Cookie::set($this->_cookie_key, $token->token, $token->expires);
		
		return $this;
	}
}