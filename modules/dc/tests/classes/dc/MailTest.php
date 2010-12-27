<?php defined('SYSPATH') or die('No direct access allowed.');
/** 
 * Use NO_MOCKS=1 so that real email sending will be activated
 * 
 * @author Leonel
 *
 */
class Dc_MailTest extends Kohana_UnitTest_TestCase
{
	public function get_mailer()
	{
		$mailer = NULL;
		
		if (getenv('NO_MOCKS'))
		{
			$mailer = new Dc_Mailer;
		}
		else 
		{
			$mailer = $this->getMock('Dc_Mail');
		}
		
		return $mailer;
	}
	
	/** 
	 * @test
	 */
	public function test_send()
	{
		// Set server name
		$_SERVER['SERVER_NAME'] = 'streetwatchph.darkstar.net';
		
		$mailer = $this->get_mailer();
		
		$data = array(
			'from' => array('lysender.mailer@gmail.com' => 'StreetWatchPH Auto Mailer - Lysender AutoMailer'),
			'subject' 	=> 'Testing activation email',
			'recipient' => array('dc.eros@gmail.com', 'leonel@esp.ph'),
			'body_type' => 'text/html'
		);
		
		$test_user = 'testuser';
		$test_link = '/signup/activate/'.Text::random('alnum', 32);
		
		$body = View::factory('signup/activate.email.html')
			->bind('activation_user', $test_user)
			->bind('activation_link', $test_link)
			->render();
		
		$data['body'] = $body;
		
		$body_part = View::factory('signup/activate.email.plain')
			->bind('activation_user', $test_user)
			->bind('activation_link', $test_link)
			->render();
		
		$data['body_part'] = $body_part;
		$data['body_part_type'] = 'text/plain';
			
		$result = $mailer->send($data);
	}	
}
