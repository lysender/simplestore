<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_LoginTest extends Kohana_UnitTest_TestCase
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
	
	/** 
	 * Other tests depends on this test
	 * 
	 * This test creates a test user in the system
	 * 
	 * @test
	 */
	public function test_create_user()
	{		
		$user = Sprig::factory('userauth');
		$user->email = 'dctestuser@lysender.com';
		$user->username = 'dctestuser';
		$user->password = 'password';
		$user->date_joined = time();
		
		$this->assertTrue( (boolean) $user->create());
		
		// Update them so that they are active
		$user->active = 1;
		$user->update();
		
		$testuser = Sprig::factory('userauth', array(
			'username' => 'dctestuser'
		));
		
		$testuser->load();
		
		return $testuser;
	}
	
	/** 
	 * @test 
	 * @depends test_create_user
	 * @param Sprig $testuser
	 */
	public function test_empty_all(Sprig $testuser)
	{
		$login = Sprig::factory('login');
		$login->values(array(
			'username' => '',
			'password' => '',
			'csrf' => ''
		));
		
		try
		{
			$login->login();
		}
		catch (Exception $e)
		{
			$this->assertType('Validate_Exception', $e);
			
			$errors = $e->array->errors();
			$error_count = count($errors);
			
			$this->assertEquals(3, $error_count);
		}
	}
	
	/** 
	 * @test
	 * @depends test_create_user
	 * @param $testuser
	 */
	public function test_invalid_token(Sprig $testuser)
	{
		$login = Sprig::factory('login');
		$login->values(array(
			'username' => $testuser->username,
			'password' => 'password',
			'csrf' => Text::random('alnum', 23)
		));
		
		try
		{
			$login->login();
		}
		catch (Exception $e)
		{
			$this->assertType('Validate_Exception', $e);
			
			$errors = $e->array->errors();
			$error_count = count($errors);
			
			$this->assertEquals(1, $error_count);
			
			$this->assertTrue(isset($errors['csrf']));
		}
	}
	
	/** 
	 * @test
	 * @depends test_create_user
	 * @expectedException Dc_Auth_Exception
	 * @param $testuser
	 */
	public function test_invalid_password(Sprig $testuser)
	{
		$login = Sprig::factory('login');
		$login->values(array(
			'username' => $testuser->username,
			'password' => 'passwordxxx',
			'csrf' => Security::token()
		));
		
		$login->login();
	}
	
	/** 
	 * @test
	 * @depends test_create_user
	 * @expectedException Dc_Auth_Exception
	 * @param $testuser
	 */
	public function test_inactive(Sprig $testuser)
	{
		$testuser->active = 0;
		$testuser->banned = 0;
		$testuser->update();
		
		$login = Sprig::factory('login');
		$login->values(array(
			'username' => $testuser->username,
			'password' => 'password',
			'csrf' => Security::token()
		));
		
		$login->login();
	}
	
	/** 
	 * @test
	 * @depends test_create_user
	 * @expectedException Dc_Auth_Exception
	 * @param $testuser
	 */
	public function test_banned(Sprig $testuser)
	{
		$testuser->active = 1;
		$testuser->banned = 1;
		$testuser->update();
		
		$login = Sprig::factory('login');
		$login->values(array(
			'username' => $testuser->username,
			'password' => 'password',
			'csrf' => Security::token()
		));
		
		$login->login();
	}
	
	/** 
	 * @test
	 * @depends test_create_user
	 * @param $testuser
	 */
	public function test_valid(Sprig $testuser)
	{
		$testuser->active = 1;
		$testuser->banned = 0;
		$testuser->update();
		
		$login = Sprig::factory('login');
		$login->values(array(
			'username' => $testuser->username,
			'password' => 'password',
			'csrf' => Security::token()
		));
		
		$this->assertTrue($login->login());
		
		$auth = $this->get_auth();
		//$this->assertTrue($auth->get_user(Sprig::factory('user'))->loaded());
		
		$config = $this->get_auth_config();
		
		// Auth session must have been set
		$this->assertEquals($auth->get_user()->username, Session::instance()->get($config['session_key']));
		
		// User must have 1 login
		$user = Sprig::factory('user', array('id' => $testuser->id))->load();
		$this->assertEquals(1, $user->logins);
	}
	
	/** 
	 * @test
	 * @depends test_create_user
	 * @param $testuser
	 */
	public function test_valid_remember(Sprig $testuser)
	{
		$login = Sprig::factory('login');
		$login->values(array(
			'username' => $testuser->username,
			'password' => 'password',
			'remember' => 1,
			'csrf' => Security::token()
		));
		
		$this->assertTrue($login->login());
		
		$auth = Dc_Auth::instance();
		$this->assertTrue($auth->get_user(Sprig::factory('user'), Sprig::factory('usertoken'))->loaded());
		$this->assertTrue($auth->get_token()->loaded());
	}
	
	/** 
	 * @test
	 * @depends test_create_user
	 * 
	 * @param Sprig $testuser
	 */
	public function test_cleanup_test_user(Sprig $testuser)
	{
		// Delete user token
		$token = Sprig::factory('usertoken', array(
			'user_id' => $testuser->id
		));
		
		$token->delete();
		
		// Delete user
		$this->assertTrue( (boolean) $testuser->delete());
	}
}