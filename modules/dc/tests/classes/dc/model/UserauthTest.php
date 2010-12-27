<?php defined('SYSPATH') or die('No direct access allowed.');

class Dc_Model_UserauthTest extends Kohana_UnitTest_TestCase
{
	/** 
	 * Returns the user object
	 * 
	 * @return Dc_Auth_UserInterface
	 */
	public function get_user_object()
	{
		return Sprig::factory('userauth');
	}
	
	/** 
	 * @test
	 */
	public function test_object()
	{
		$user = $this->get_user_object();
		
		$this->assertTrue($user instanceof Dc_Auth_UserInterface);
	}
	
	/** 
	 * @test
	 */
	public function test_hash()
	{
		$user = $this->get_user_object();
		
		$str = '$0m3W31rdPassw0rd1';
		
		$hash1 = $user->hash($str);
		$hash2 = $user->hash($str);
		
		$this->assertEquals($hash1, $hash2);
		
		$this->assertNotEquals($str, $hash1);
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
		// Value8 = load_success (true/false)
		// Value9 = login_success (true/false)
		return array(
			// Able to login
			array('dctestuser01', 'dctestuser01@lysender.com', 'secretpass1', 0, 1, 'dctestuser01', 'secretpass1', TRUE, TRUE),
			// Inactive, can't login
			array('dctestuser02', 'dctestuser02@lysender.com', 'secretpass2', 0, 0, 'dctestuser02', 'secretpass2', FALSE, FALSE),
			// Banned, can't login
			array('dctestuser03', 'dctestuser03@lysender.com', 'secretpass3', 1, 0, 'dctestuser03', 'secretpass3', FALSE, FALSE),
			// Both banned and inactive can't login
			array('dctestuser04', 'dctestuser04@lysender.com', 'secretpass4', 1, 0, 'dctestuser04', 'secretpass4', FALSE, FALSE),
			// Incorrect password
			array('dctestuser05', 'dctestuser05@lysender.com', 'secretpass5', 0, 1, 'dctestuser05', 'wrongpass', TRUE, FALSE),
			// Incorrect username
			array('dctestuser06', 'dctestuser06@lysender.com', 'secretpass6', 0, 1, 'wronguser', 'secretpass6', FALSE, FALSE)
		);
	}
	
	/** 
	 * @test
	 * @dataProvider user_provider
	 * 
	 * @param string $username
	 * @param string $email
	 * @param string $password
	 * @param string $banned
	 * @param string $active
	 * @param string $input_user
	 * @param string $input_password
	 * @param string $expected_load
	 * @param string $expected_login
	 */
	public function test_create($username, $email, $password, $banned, $active, $input_user, $input_password, $expected_load, $expected_login)
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
	
	/** 
	 * @test
	 * @dataProvider user_provider
	 * 
	 * @param string $username
	 * @param string $email
	 * @param string $password
	 * @param string $banned
	 * @param string $active
	 * @param string $input_user
	 * @param string $input_password
	 * @param string $expected_load
	 * @param string $expected_login
	 */
	public function test_load_user($username, $email, $password, $banned, $active, $input_user, $input_password, $expected_load, $expected_login)
	{
		$user = $this->get_user_object();
		
		$user->load_user($input_user);
		
		$this->assertEquals($expected_load, $user->loaded());
		
		$identity = $user->identity();
		
		$this->assertEquals($expected_load, ! ($identity === NULL));
	}
	
	public function load_by_id_provider()
	{
		$dataset = $this->user_provider();
		$users = array();
		
		foreach ($dataset as $row)
		{
			$users[] = $row[0];
		}
		
		$user = $this->get_user_object();
		
		// Load all test user data
		$query = DB::select()->where('username', 'IN', $users);
		$dataset = $user->load($query, FALSE);
		
		$provider_data = array();
		
		foreach ($dataset as $row)
		{
			$provider_data[] = array(
				'id' => $row->id,
				'expected' => ( ! $row->banned && $row->active)
			);
		}
		
		// Value1 = id
		// Value2 = expected (true/false)
		return $provider_data;
	}
	
	/** 
	 * @test
	 * @dataProvider load_by_id_provider
	 * 
	 * @param int $id
	 * @param boolean $expected
	 */
	public function test_load_by_id($id, $expected)
	{
		$user = $this->get_user_object();
		
		$user->load_by_id($id);
		
		$this->assertEquals($exptected, $user->loaded());
	}
	
	/** 
	 * @test
	 * @dataProvider user_provider
	 * 
	 * @param string $username
	 * @param string $email
	 * @param string $password
	 * @param string $banned
	 * @param string $active
	 * @param string $input_user
	 * @param string $input_password
	 * @param string $expected_load
	 * @param string $expected_login
	 */
	public function test_login($username, $email, $password, $banned, $active, $input_user, $input_password, $expected_load, $expected_login)
	{
		$user = $this->get_user_object();
		
		$result = $user->login(array(
			'username' => $input_user,
			'password' => $input_password
		));
		
		$this->assertEquals($expected_login, $result);
		
		// Cleanup
		$actual_user = $this->get_user_object();
		$actual_user->username = $username;
		$actual_user->delete();
	}
}