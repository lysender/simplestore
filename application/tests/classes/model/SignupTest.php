<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_SignupTest extends Kohana_UnitTest_TestCase
{
	/** 
	 * @test
	 */
	public function test_empty_all()
	{
		$signup = Sprig::factory('signup');
		$signup->values(array(
			'username' => '',
			'email' => '',
			'password' => '',
			'password_confirm' => '',
			'csrf' => '',
		));
		
		try 
		{
			$signup->signup();
		}
		catch (Exception $e)
		{
			$this->assertType('Validate_Exception', $e);
			
			$errors = $e->array->errors('signup');
			$error_count = count($errors);
			
			$this->assertEquals(4, $error_count);
		}
		
		// Sprig rules / filters / callbacks are well tested already
	}
	
	/** 
	 * @test
	 */
	public function test_signup()
	{
		// Set server name
		$_SERVER['SERVER_NAME'] = 'streetwatchph.darkstar.net';
		
		$signup = Sprig::factory('signup');
		$signup->values(array(
			'username' => 'dctestuser',
			'email' => 'dctestuser@lysender.com',
			'password' => 'password',
			'password_confirm' => 'password',
			'csrf' => Security::token(),
		));
		
		$token = $signup->signup();
		
		$this->assertType('Sprig', $token);
		$this->assertTrue($token->loaded());
		
		$user = Sprig::factory('user', array('username' => 'dctestuser'))->load();
		
		// User must not be active yet
		$this->assertTrue($user->loaded());
		$this->assertEquals(0, $user->active);
		$this->assertEquals(0, $user->banned);
		
		// There must be 1 token for activation
		$token_count = Sprig::factory('usertoken', array('user_id' => $user->id))->count();
		
		$this->assertEquals(1, $token_count);
		
		return array($user, $token);
	}
	
	/** 
	 * @test
	 * @depends test_signup
	 * @param Sprig $user
	 */
	public function test_cleanup(array $models)
	{
		$token = Sprig::factory('usertoken', array('user_id' => $models[0]->id))->delete();
		
		$models[0]->delete();
	}
}