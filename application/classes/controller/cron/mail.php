<?php defined('SYSPATH') or die('No direct script access.');

/** 
 * Cron frond-end for mailing utilities
 * 
 * @author lysender
 *
 */
class Controller_Cron_Mail extends Controller
{
	/** 
	 * No access for index
	 * 
	 * @throws Kohana_Request_Exception
	 */
	public function action_index()
	{
		throw new Kohana_Request_Exception('Page not found');
	}
	
	/** 
	 * Runs the sending of email
	 * 
	 * @throws Kohana_Request_Exception
	 */
	public function action_run()
	{
		$token = '5uctzl4g1VdaZmR4opZ3SvWCXeTvwqW5';
		
		if ($this->request->param('id') != $token)
		{
			throw new Kohana_Request_Exception('Page not found');
		}
		
		$queue = new Dc_Mail_Queue;
		
		$queue->run();
	}
	
	/** 
	 * Runs the deleting of sent mails
	 * 
	 * @throws Kohana_Request_Exception
	 */
	public function action_cleanup()
	{
		$token = 'MSYxrb5DkK2A951QLnIHru0nqINkmscY';
		
		if ($this->request->param('id') != $token)
		{
			throw new Kohana_Request_Exception('Page not found');
		}
		
		$queue = new Dc_Mail_Queue;
		
		$queue->cleanup();
		
		echo Text::random('alnum', 32);
		$str = hash_hmac('sha256', 'password', $token);
		
		var_dump($str);
	}
}