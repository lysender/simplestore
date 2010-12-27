<?php defined('SYSPATH') or die('No direct access allowed.');

class Dc_Model_Mail_QueueTest extends Kohana_UnitTest_TestCase
{
	const TYPE = 100;
	const TOP_PRIORITY = 0;
	const PRIORITY_FOR_SENT = 3;
	const PER_BATCH = 3;
	
	/** 
	 * @return Dc_Model_Mail_Queue
	 */
	public function get_object()
	{
		return new Model_Mail_Queue;
	}
	
	/** 
	 * @test
	 */
	public function test_object()
	{
		$queue = $this->get_object();
		
		$this->assertType('Model_Mail_Queue', $queue);
	}
	
	/** 
	 * @test
	 */
	public function test_table()
	{
		$queue = $this->get_object();
		
		// Queue model must have table name by default
		$this->assertTrue( (boolean) $queue->get_table());
		
		// Change table
		$expected = 't_mail_queue';
		
		$queue->set_table($expected);
		
		$this->assertEquals($expected, $queue->get_table());
	}
	
	public function put_provider()
	{
		$dummy_header = array(
			'from' => array(),
			'recipient' => array(),
			'subject' => 'Test subject'
		);
		
		$dummy_body = array(
			'body_type' => 'text/html',
			'body' => '<h1>Test body</h1>',
			'body_part_type' => 'text/plain',
			'body_part' => 'Test body'
		);
		
		$top_header = array(
			'subject' => 'TOP PRIORITY'
		);
		
		$top_body = array(
			'body' => 'TOP PRIORITY'
		);
		
		return array(
			array(self::TYPE, $dummy_header, $dummy_body, 5),
			array(self::TYPE, $dummy_header, $dummy_body, 5),
			array(self::TYPE, $dummy_header, $dummy_body, 5),
			array(self::TYPE, $dummy_header, $dummy_body, self::PRIORITY_FOR_SENT),
			array(self::TYPE, $dummy_header, $dummy_body, self::PRIORITY_FOR_SENT),
			array(self::TYPE, $dummy_header, $dummy_body, self::PRIORITY_FOR_SENT),
			array(self::TYPE, $dummy_header, $dummy_body, 5),
			array(self::TYPE, $top_header, $top_body, self::TOP_PRIORITY),
			array(self::TYPE, $dummy_header, $dummy_body, 5),
			array(self::TYPE, $dummy_header, $dummy_body, 5),
			array(self::TYPE, $dummy_header, $dummy_body, 5),
			array(self::TYPE, $dummy_header, $dummy_body, 5),
			array(self::TYPE, $top_header, $top_body, self::TOP_PRIORITY),
			array(self::TYPE, $dummy_header, $dummy_body, 5)
		);
	}
	
	/** 
	 * @dataProvider put_provider
	 * @test
	 * 
	 * @param int $type
	 * @param array $header
	 * @param array $body
	 * @param int $priority
	 */
	public function test_put($type, $header, $body, $priority)
	{
		$queue = $this->get_object();
		
		$result = $queue->put($type, $header, $body, $priority);
		
		$this->assertTrue( (boolean) $result);
	}
	
	/** 
	 * @test
	 */
	public function test_get_normal()
	{
		$queue = $this->get_object();
		
		$data = $queue->get(self::PER_BATCH);
		$data_count = count($data);
		
		// There must full content
		$this->assertEquals($data_count, self::PER_BATCH);
		
		// Priority mail should be included
		$priority_count = 0;
		$priority_expected = 2;
		
		foreach ($data as $row)
		{
			if ($row['priority'] == self::TOP_PRIORITY)
			{
				$this->assertEquals('TOP PRIORITY', $row['head_serialized']['subject']);
				$this->assertEquals('TOP PRIORITY', $row['body_serialized']['body']);
				
				$priority_count++;
			}
		}
		
		$this->assertEquals($priority_count, $priority_expected);
	}
	
	/** 
	 * @test
	 */
	public function test_get_more()
	{
		$queue = $this->get_object();
		
		$data = $queue->get(self::PER_BATCH + 5);
		$data_count = count($data);
		
		// There must full content
		$this->assertEquals($data_count, self::PER_BATCH + 5);
	}
	
	public function get_for_sent()
	{
		$result = DB::select('id')
			->from($this->get_object()->get_table())
			->where('type', '=', self::TYPE)
			->where('priority', '=', self::PRIORITY_FOR_SENT)
			->execute(Kohana::TESTING);
			
		$result = $result->as_array();
		
		if (empty($result))
		{
			throw new Exception('No sent records');
		}
		
		return $result;
	}
	
	/** 
	 * @test
	 * 
	 * @param array $ids
	 */
	public function test_sent()
	{
		$ids = $this->get_for_sent();
		
		$queue = $this->get_object();
		$result = $queue->sent($ids);
		
		$id_count = count($ids);
		
		// Retrive it back and assert
		$sent = DB::select('id')
			->from($queue->get_table())
			->where('type', '=', self::TYPE)
			->where('sent', '=', 1)
			->execute(Kohana::TESTING);
			
		$sent = $sent->as_array();
		
		$sent_count = count($sent);
		
		$this->assertEquals($id_count, $sent_count);
		$this->assertEquals($ids, $sent);
		
		return $sent;
	}
	
	/** 
	 * @depends test_sent
	 * @test
	 * 
	 * @param array $ids
	 */
	public function test_after_sent(array $ids)
	{
		$queue = $this->get_object();
		$sent = $queue->get(self::PER_BATCH + 5);
		
		foreach ($sent as $mail)
		{
			$this->assertFalse(in_array($mail['id'], $ids));
		}
		
		return $ids;
	}
	
	/** 
	 * @depends test_after_sent
	 * @test
	 * 
	 * @param array $ids
	 */
	public function test_delete_sent(array $ids)
	{
		$queue = $this->get_object();
		
		$id_count = count($ids);
		$delete_count = $queue->delete_sent();
		
		$this->assertEquals($id_count, $delete_count);
		
		$verify = DB::select('id')
			->from($queue->get_table())
			->where('type', '=', self::TYPE)
			->where('sent', '=', 1)
			->execute(Kohana::TESTING);
			
		$verify = $verify->as_array();
		
		$this->assertTrue(empty($verify));
	}
	
	public function test_cleanup()
	{
		$query = DB::delete($this->get_object()->get_table())
			->where('type', '=', self::TYPE)
			->execute(Kohana::TESTING);
			
		$this->assertTrue( (boolean) $query);
	}
}
