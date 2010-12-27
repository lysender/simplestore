<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_User extends Dc_Model_Userauth
{
	/** 
	 * @var array
	 */
	protected $_config;
	
	/** 
	 * Returns the configuration for the user model
	 * 
	 * @return array
	 */
	public function get_config()
	{
		if (empty($this->_config))
		{
			$this->_config = Kohana::config('user');
		}
		
		return $this->_config;
	}
	
	/** 
	 * Sets the configuration for the user model
	 * 
	 * @param array $config
	 * @return $this
	 */
	public function set_config(array $config)
	{
		$this->_config = $config;
		
		return $this;
	}
	
	public function create()
	{
		$this->date_joined = time();
		
		return parent::create();
	}
	
	/** 
	 * Signup - returns the activation when signup successfull
	 * 
	 * @return Sprig
	 */
	public function signup()
	{
		$this->create();
		
		// Create a token for activation
		$token = Sprig::factory('usertoken');
		
		$config = $this->get_config();
		
		$token->activation = 1;
		$token->generate($this->id, time() + $config['activation_expiry']);
		
		// Send activation mail
		$this->send_activation($token);
		
		return $token;
	}
	
	/** 
	 * Sents activation email to the new user's email address
	 * 
	 * @param Sprig $token
	 * @return boolean
	 */
	public function send_activation(Sprig $token)
	{
		if ( ! $this->loaded())
		{
			throw new Exception('Sending activation requires that a new has been created');
		}
		
		$mail_queue = new Dc_Mail_Queue;
		
		$mail_config = Kohana::config('dc/mail');
		
		$header = array(
			'from' => $mail_config->from,
			'recipient' => $this->email,
			'subject' => 'StreetWatchPH - Activate your acount'
		);
		
		$new_user = $this->username;
		$user_link = '/signup/activate/'.$token->token;
		
		$body_message = View::factory('signup/activate.email.html')
			->bind('activation_user', $new_user)
			->bind('activation_link', $user_link)
			->render();
		
		$body_part = View::factory('signup/activate.email.plain')
			->bind('activation_user', $new_user)
			->bind('activation_link', $user_link)
			->render();
			
		$body = array(
			'body_type' => 'text/html',
			'body' => $body_message,
			'body_part_type' => 'text/plain',
			'body_part' => $body_part
		);
		
		return $mail_queue->add_mail(Dc_Mail_Queue::TYPE_SINGLE, $header, $body);
	}
	
	/** 
	 * Activate user's account
	 * 
	 * @param string $param_token
	 * @throws Dc_Auth_Exception
	 * @throws Exception
	 */
	public function activate($param_token)
	{
		// When no token is given
		if (empty($param_token))
		{
			throw new Dc_Auth_Exception('Activation key is not set');
		}
		
		// To avoid conflict, the user must be logged in when activating
		// somebody's account
		if ($this->loaded())
		{
			throw new Exception('When activating, user must not be loaded');
		}
		
		$token = Sprig::factory('usertoken');
		
		$token->token = $param_token;
		$token->load();
		
		// Token not found
		if ( ! $token->loaded())
		{
			throw new Dc_Auth_Exception('Activation key is invalid');	
		}
		
		$this->id = $token->user_id;
		$this->load();
		
		// User is not found
		if ( ! $this->loaded())
		{
			throw new Dc_Auth_Exception('User to activate is not found');
		}
		
		// Activation expires
		if (time() > $token->expires)
		{
			throw new Dc_Auth_Exception('Activation for this user has expired');
		}

		$this->active = 1;
		$this->update();
		
		// Delete the token
		$token->delete();
		
		$config = $this->get_config();
		
		if ($config['activation_autologin'])
		{
			$auth = Dc_Auth::instance();
			$auth->force_login($this, $config['activation_autologin_remember']);
		}
		
		return $this;
	}
}