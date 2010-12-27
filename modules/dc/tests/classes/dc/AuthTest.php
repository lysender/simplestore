<?php defined('SYSPATH') or die('No direct access allowed.');

class Dc_AuthTest extends Kohana_UnitTest_TestCase
{
	/**
	 * Gets a mock of the session class
	 *
	 * @return Session
	 */
	public function get_mock_session(array $config = array())
	{
		return $this->getMockForAbstractClass('Session', array($config));
	}
	
	public function get_auth($session = NULL)
	{
		$auth = Dc_Auth::instance();
		
		$config = $this->get_auth_config()->as_array();
		
		if ($session)
		{
			$config['session'] = & $session;
		}
		else 
		{
			$config['session'] = $this->get_mock_session();
		}
		
		$auth->clear()->set_options($config);
		
		return $auth;
	}
	
	public function get_auth_config()
	{
		$config = Kohana::config('dc/auth');
		
		return $config;
	}	
	
	public function get_user_object()
	{
		return Sprig::factory('userauth');
	}
	
	public function get_token_object()
	{
		return Sprig::factory('usertoken');
	}
	
	/** 
	 * @test
	 */
	public function test_object()
	{
		$auth = $this->get_auth();
		
		$this->assertEquals($auth, Dc_Auth::instance());
	}
	
	/** 
	 * @test
	 */
	public function test_set_options()
	{
		$auth = $this->get_auth();
		
		$result = $auth->set_options(array(
			'session' => Session::instance(),
			'secret_key' => 'asdas7a6sd87a6s8dasa',
			'lifetime' => 3600,
			'session_key' => 'dc_auth_user',
			'cookie_key' => 'dc_auth_auto',
			'allow_autologin' => TRUE
		));
		
		$this->assertSame($result, $auth);
	}
	
	public function user_provider()
	{
		// Value1 = username
		// Value2 = email
		// Value3 = password
		// Value4 = banned
		// Value5 = active
		// Value6 = input_username
		// Value7 = input_password
		// Value8 = get_user_result (true/false)
		// Value9 = login_result (true/false)
		return array(
			// Valid user
			array('dctestuser1', 'dctestuser1@lysender.com', 'secretpass1', 0, 1, 'dctestuser1', 'secretpass1', TRUE, TRUE),
			// Banned user
			array('dctestuser2', 'dctestuser2@lysender.com', 'secretpass2', 1, 1, 'dctestuser2', 'secretpass2', FALSE, FALSE),
			// Inactive user
			array('dctestuser3', 'dctestuser3@lysender.com', 'secretpass3', 0, 0, 'dctestuser3', 'secretpass3', FALSE, FALSE),
			// Both banned and inactive
			array('dctestuser4', 'dctestuser4@lysender.com', 'secretpass4', 1, 0, 'dctestuser4', 'secretpass4', FALSE, FALSE),
			// Invalid username, can get user but can't login
			array('dctestuser5', 'dctestuser5@lysender.com', 'secretpass5', 0, 1, '_wrong_user_', 'secretpass5', TRUE, FALSE),
			// Invalid password, can get user but can't login
			array('dctestuser6', 'dctestuser6@lysender.com', 'secretpass6', 0, 1, 'dctestuser6', '_wrong_password', TRUE, FALSE),
			// Invalid username and password, can get user but can't login
			array('dctestuser7', 'dctestuser7@lysender.com', 'secretpass7', 0, 1, '_wrong_user_', '_wrong_password_', TRUE, FALSE),
		);
	}
	
	/** 
	 * Auth tests dependes on this
	 * 
	 * @test
	 * @dataProvider user_provider
	 * 
	 * @param string $username
	 * @param string $email
	 * @param string $password
	 * @param int $banned
	 * @param int $active
	 * @param string $input_username
	 * @param string $input_password
	 * @param boolean $get_user_expect
	 * @param boolean $login_expect
	 */
	public function test_create_user($username, $email, $password, $banned, $active, $input_username, $input_password, $get_user_expect, $login_expect)
	{
		$user = $this->get_user_object();
		
		$user->username = $username;
		$user->email = $email;
		$user->password = $password;
		$user->banned = $banned;
		$user->active = $active;
		$user->date_joined = time();
		
		$user->create();
		
		$this->assertTrue($user->loaded());
	}
	
	public function clear_auth_env($session)
	{
		$config = $this->get_auth_config();
		
		$session->delete($config->session_key);
		
		Cookie::delete($config->cookie_key);
	}
	
	public function init_session($session, $identity)
	{
		$config = $this->get_auth_config();
		
		$session->set($config->session_key, $identity);
	}
	
	public function init_token($token)
	{
		$config = $this->get_auth_config();
		
		$_COOKIE[$config['cookie_key']] = Cookie::salt($config['cookie_key'], $token).'~'.$token;
	}
	
	/**
	 * @test
	 * @dataProvider user_provider
	 * 
	 * @param string $username
	 * @param string $email
	 * @param string $password
	 * @param int $banned
	 * @param int $active
	 * @param string $input_username
	 * @param string $input_password
	 * @param boolean $get_user_expect
	 * @param boolean $login_expect
	 */
	public function test_get_user_with_session_only($username, $email, $password, $banned, $active, $input_username, $input_password, $get_user_expect, $login_expect)
	{
		$session = $this->get_mock_session();
		$this->clear_auth_env($session);
		$this->init_session($session, $username);
		
		$auth = $this->get_auth($session);
		
		$user = $auth->get_user($this->get_user_object(), $this->get_token_object());
		
		$this->assertEquals($get_user_expect, ($user instanceof Dc_Auth_UserInterface));
	}
	
	/**
	 * @test
	 * @dataProvider user_provider
	 * 
	 * @param string $username
	 * @param string $email
	 * @param string $password
	 * @param int $banned
	 * @param int $active
	 * @param string $input_username
	 * @param string $input_password
	 * @param boolean $get_user_expect
	 * @param boolean $login_expect
	 */
	public function test_login_no_remember($username, $email, $password, $banned, $active, $input_username, $input_password, $get_user_expect, $login_expect)
	{
		$session = $this->get_mock_session();
		$this->clear_auth_env($session);
		
		$auth = $this->get_auth($session);
		
		$result = $auth->login(
			$this->get_user_object(),
			array('username' => $input_username, 'password' => $input_password)
		);
		
		$this->assertEquals($login_expect, $result);
		
		// Test get user after login
		$user = $auth->get_user($this->get_user_object());
		
		$this->assertEquals($login_expect, ($user instanceof Dc_Auth_UserInterface));
		
		if ($login_expect)
		{
			$this->assertEquals($user->username, $username);
		}
	}
	
	/**
	 * @test
	 * @dataProvider user_provider
	 * 
	 * @param string $username
	 * @param string $email
	 * @param string $password
	 * @param int $banned
	 * @param int $active
	 * @param string $input_username
	 * @param string $input_password
	 * @param boolean $get_user_expect
	 * @param boolean $login_expect
	 */
	public function test_login_remember($username, $email, $password, $banned, $active, $input_username, $input_password, $get_user_expect, $login_expect)
	{
		$config = $this->get_auth_config();
		$session = $this->get_mock_session();
		$this->clear_auth_env($session);
		
		$auth = $this->get_auth($session);
		
		$result = $auth->login(
			$this->get_user_object(),
			array('username' => $input_username, 'password' => $input_password),
			$this->get_token_object()
		);
		
		$this->assertEquals($login_expect, $result);
		
		// Test get user after login
		$user = $auth->get_user($this->get_user_object());
		$this->assertEquals($login_expect, ($user instanceof Dc_Auth_UserInterface));
		
		// There must be a token
		$token = $auth->get_token();
		$this->assertEquals($login_expect, ($token instanceof Dc_Auth_TokenInterface));
		
		if ($login_expect)
		{
			$this->assertEquals($user->username, $username);
			// Manually set cookie
			$this->init_token($token->token);
		}
		
		// Check cookie
		$strtoken = Cookie::get($config->cookie_key);

		$this->assertEquals($login_expect, ! ($strtoken === NULL));
	}
	
	/**
	 * @test
	 * @dataProvider user_provider
	 * 
	 * @param string $username
	 * @param string $email
	 * @param string $password
	 * @param int $banned
	 * @param int $active
	 * @param string $input_username
	 * @param string $input_password
	 * @param boolean $get_user_expect
	 * @param boolean $login_expect
	 */
	public function test_force_login($username, $email, $password, $banned, $active, $input_username, $input_password, $get_user_expect, $login_expect)
	{
		$config = $this->get_auth_config();
		$session = $this->get_mock_session();
		$this->clear_auth_env($session);
		
		$auth = $this->get_auth($session);
		
		$result = $auth->force_login($this->get_user_object(), $username);
		
		$this->assertEquals($get_user_expect, $result);
		
		$this->assertEquals(NULL, $auth->get_token());
	}
	
	/**
	 * @test
	 * @dataProvider user_provider
	 * 
	 * @param string $username
	 * @param string $email
	 * @param string $password
	 * @param int $banned
	 * @param int $active
	 * @param string $input_username
	 * @param string $input_password
	 * @param boolean $get_user_expect
	 * @param boolean $login_expect
	 */
	public function test_force_login_remember($username, $email, $password, $banned, $active, $input_username, $input_password, $get_user_expect, $login_expect)
	{
		$config = $this->get_auth_config();
		$session = $this->get_mock_session();
		$this->clear_auth_env($session);
		
		$auth = $this->get_auth($session);
		
		$result = $auth->force_login($this->get_user_object(), $username, $this->get_token_object());
		
		$this->assertEquals($get_user_expect, $result);
		
		$token_object = $auth->get_token();
		$strtoken = NULL;
		$cookie_token = NULL;
		
		if ($get_user_expect)
		{
			$this->init_token($token_object->token);
			$cookie_token = Cookie::get($config->cookie_key);
			$strtoken = $token_object->token;
		}
		
		$this->assertEquals($get_user_expect, ! ($token_object === NULL));
		$this->assertEquals($get_user_expect, ! ($strtoken === NULL));
		$this->assertEquals($get_user_expect, ! ($cookie_token === NULL));
	}
	
	/**
	 * Cleanup users
	 * 
	 * @test
	 * @dataProvider user_provider
	 * 
	 * @param string $username
	 * @param string $email
	 * @param string $password
	 * @param int $banned
	 * @param int $active
	 * @param string $input_username
	 * @param string $input_password
	 * @param boolean $get_user_expect
	 * @param boolean $login_expect
	 */
	public function test_cleanup($username, $email, $password, $banned, $active, $input_username, $input_password, $get_user_expect, $login_expect)
	{
		$user = $this->get_user_object();
		
		$user->username = $username;
		$user->delete();
		
		$this->assertFalse($user->loaded());
	}
}