<?php defined('SYSPATH') or die('No direct access allowed.');

class Dc_Mail_QueueTest extends Kohana_UnitTest_TestCase
{
	/** 
	 * @test
	 */
	public function test_container()
	{
		$queue = new Dc_Mail_Queue;
		
		// Test default container
		$default = $queue->get_container();

		$this->assertType('Model_Mail_Queue', $default);
		
		// Test custom container
		$custom = $this->getMock('Model_Mail_Queue');
		$queue->set_container($custom);
		
		$this->assertNotEquals($default, $custom);
		
		// Same type because of mocking
		$this->assertType('Model_Mail_Queue', $custom);
	}
	
	/** 
	 * @test
	 */
	public function test_mailer()
	{
		$queue = new Dc_Mail_Queue;
		
		// Test default mailer
		$default = $queue->get_mailer();
		
		$this->assertType('Dc_Mail', $default);
		
		// Test custom mailer
		$custom = $this->getMock('Dc_Mail');
		
		$queue->set_mailer($custom);
		
		$this->assertNotEquals($default, $custom);
		
		// Same type because of mocking
		$this->assertType('Dc_Mail', $custom);
	}
	
	public function get_mock_mailer()
	{
		$mailer = $this->getMock('Dc_Mail');
		
		$mailer->expects($this->any())
			->method('send')
			->will($this->returnValue(1));
			
		return $mailer;
	}
	
	public function get_mock_container()
	{
		$container = $this->getMock('Model_Mail_Queue');
		
		$container->expects($this->any())
			->method('put')
			->will($this->returnValue(1));
		
		return $container;
	}
	
	public function get_add_mail_data()
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
		
		$row = array(
			'id' => 1,
			'type' => 1,
			'head_serialized' => $dummy_header, 
			'body_serialized' => $dummy_body, 
			'priority' => 5,
			'sent' => 0
		);
		
		$ret = array();
		
		for ($x = 0; $x < 12; $x++)
		{
			$ret[] = $row;
		}
		
		return $ret;
	}
	
	/** 
	 * @dataProvider get_add_mail_data
	 * @test
	 * 
	 * @param int $type
	 * @param array $header
	 * @param array $body
	 * @param int $priority
	 */
	public function test_add_mail($id, $type, array $header, array $body, $priority, $sent)
	{
		$queue = new Dc_Mail_Queue;
		
		$queue->set_container($this->get_mock_container())
			->set_mailer($this->get_mock_mailer());
			
		$result = $queue->add_mail($type, $header, $body, $priority);
		
		$this->assertTrue( (boolean) $result);
	}
	
	public function get_mock_container_with_get()
	{
		$container = $this->getMock('Model_Mail_Queue');
		
		$test_data = $this->get_add_mail_data();
		
		$container->expects($this->any())
			->method('get')
			->will($this->returnValue($test_data));
		
		$sent = count($test_data);
			
		$container->expects($this->any())
			->method('sent')
			->will($this->returnValue($sent));
			
		return $container;
	}
	
	/** 
	 * @test
	 */
	public function test_run()
	{
		$queue = new Dc_Mail_Queue;
		$sent = $queue->set_mailer($this->get_mock_mailer())
			->set_container($this->get_mock_container_with_get())
			->run();
			
		$test_data = $this->get_add_mail_data();
		$count = count($test_data);
		
		$this->assertEquals($count, $sent);
	}
	
	/** 
	 * @test
	 */
	public function test_run_with_other_running()
	{
		Kohana::cache('Dc_Mail_Queue_Running', 1);
		
		$queue = new Dc_Mail_Queue;
		$sent = $queue->set_mailer($this->get_mock_mailer())
			->set_container($this->get_mock_container_with_get())
			->run();
			
		$this->assertFalse($sent);
		
		Kohana::cache('Dc_Mail_Queue_Running', 0);
	}
	
	/** 
	 * @test
	 */
	public function test_cleanup()
	{
		$queue = new Dc_Mail_Queue;
		
		$container = $this->getMock('Model_Mail_Queue');
		
		$container->expects($this->any())
			->method('delete_sent')
			->will($this->returnValue(12));
		
		$queue->set_container($container);
		
		// Should skip when there is a run processing on the background
		Kohana::cache('Dc_Mail_Queue_Running', 1);
		
		$deleted = $queue->cleanup();
		
		$this->assertFalse($deleted);
		
		// Should clear now
		Kohana::cache('Dc_Mail_Queue_Running', 0);
		
		$deleted = $queue->cleanup();
		
		$this->assertEquals(12, $deleted);
	}
}