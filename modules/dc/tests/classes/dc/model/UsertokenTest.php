<?php defined('SYSPATH') or die('No direct access allowed.');

class Dc_Model_UsertokenTest extends Kohana_UnitTest_TestCase
{
	public function get_token_object()
	{
		return Sprig::factory('usertoken');
	}
	
	public function get_test_user_ids()
	{
		$time1 = time() + (Date::WEEK * 2);
		$time2 = time() - 1;
		$time3 = time() + 10;
		$time4 = time() - Date::WEEK;
		
		// Value1 = user id
		// Value2 = expires
		// Value3 = expired (true/false)
		return array(
			array(9990, $time1, false),
			array(9991, $time1, false),
			array(9992, $time2, true),
			array(9993, $time3, false),
			array(9994, $time4, true),
			
			// The same users
			array(9995, $time1, false),
			array(9995, $time1, false),
			
			array(9996, $time1, false),
			array(9996, $time1, false),
			array(9996, $time1, false)
		);
	}
	
	/** 
	 * @test
	 * @dataProvider get_test_user_ids
	 */
	public function test_tokens($user_id, $expires, $expired)
	{
		$token = $this->get_token_object();
		
		$token->generate($user_id, $expires);
		
		// Test creation
		$this->assertTrue($token->loaded());
		
		$loaded_token = $this->get_token_object();
		$loaded_token->load_token($token->token);
		
		// Test loading for expired and not expired
		$this->assertEquals( ! $expired, $loaded_token->loaded());
		
		// Test token validity
		$this->assertEquals( ! $expired, $loaded_token->valid_token());
		
		if ( ! $expired)
		{
			// Expires must be equal
			$this->assertEquals($token->expires, $loaded_token->expires);
			// User must be the same
			$this->assertEquals($token->user_id, $loaded_token->user_id);
			
			// Test updating tokens
			$loaded_token->regenerate($loaded_token->expires + Date::WEEK);
			
			// Token string must now not equal
			$this->assertNotEquals($token->token, $loaded_token->token);
			// But user id is still equal
			$this->assertEquals($token->user_id, $loaded_token->user_id);
			// Token must still be valid
			$this->assertTrue($loaded_token->valid_token());
			
			// Load a new instance to make sure
			$token2 = $this->get_token_object();
			$token2->load_token($loaded_token->token);
			
			$this->assertTrue($token2->loaded());
			
			// Test deletion of tokens
			$token2->delete();
			
			$deleted_token = $this->get_token_object();
			$deleted_token->load_token($loaded_token->token);
			
			$this->assertFalse($deleted_token->loaded());
			
			// Token must be invalid
			$this->assertFalse($deleted_token->valid_token());
		}
	}
	
	/** 
	 * @test
	 */
	public function test_delete_expired_token()
	{		
		$this->get_token_object()->delete_expired();
		
		$token_list = $this->get_test_user_ids();
		$user_list = array();
		
		foreach ($token_list as $list)
		{
			list($user_id, $expires, $expired) = $list;
			
			if ($expired)
			{
				$user_list[] = $user_id;
			}
		}
		
		$query = DB::select()->where('user_id', 'IN', $user_list);
		
		$users = $this->get_token_object()->load($query, FALSE)->as_array();
		
		$this->assertTrue(empty($users));
	}
	
	/** 
	 * @test
	 */
	public function test_delete_by_user()
	{
		$time = time() + Date::WEEK * 2;
		
		$user_id = 9997;
		
		$token_list = array();
		
		for ($x=0; $x<3; $x++)
		{			
			$token = $this->get_token_object();
			
			$token->generate($user_id, $time + ($x * 2));
			
			$token_list[] = $token->token;
			
			$this->assertTrue($token->loaded());
		}
		
		// There must be 3 records for that user_id
		$this->token_count_test_set($user_id, 3);
		
		// Delete token
		$this->get_token_object()->delete_by_user($user_id);
		
		// There must be no more tokens for that user
		$this->token_count_test_set($user_id, 0);
	}
	
	public function token_count_test_set($user_id, $expected)
	{
		$query = DB::select()->where('user_id', '=', $user_id);
		$token_from_db = $this->get_token_object()->load($query, FALSE)->as_array();
		
		$count = count($token_from_db);

		$this->assertEquals($expected, $count);
	}
}