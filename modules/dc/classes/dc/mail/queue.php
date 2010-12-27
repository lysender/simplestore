<?php defined('SYSPATH') or die('No direct script access.');
/** 
 * Dc_Mail_Queue
 * 
 * Manages sending of email. Features include priority flags
 * and batch sending. Uses database for queue container 
 * 
 * Roughly based on PEAR's Mail_Queue but not actually looking
 * at the codes.
 * 
 */
class Dc_Mail_Queue
{
	const TYPE_SINGLE = 0;
	const TYPE_MASS = 1;
	
	/** 
	 * Number of mail to send per run
	 * 
	 * @var int
	 */
	protected $_mail_per_batch = 5;
	
	/** 
	 * Number in seconds the amount of time the process
	 * is allowed to run
	 * 
	 * @var int
	 */
	protected $_max_proc_time = 300;
	
	/** 
	 * Default priority for ordinary mail
	 * 
	 * @var int
	 */
	protected $_default_priority = 3;
	
	/** 
	 * Model for mail queue container
	 * 
	 * @var Model_Mail_Queue
	 */
	protected $_container;
	
	/** 
	 * @var Dc_Mail
	 */
	protected $_mailer;
	
	/** 
	 * Cache instance
	 * 
	 * @var Cache
	 */
	protected $_cache;
	
	/** 
	 * Cache key to use for storing and retreiving
	 * cached status. Status lasts for 60 secnds only
	 * 
	 * @var string
	 */
	protected $_running_key = 'Dc_Mail_Queue_Running';
	
	/** 
	 * Initialized the class
	 * 
	 * @param array $options
	 * @param Model_Mail_Queue $container
	 * @return void
	 */
	public function __construct(array $options = array(), $container = NULL)
	{
		// Load config from file
		$config = Kohana::config('dc/mail/queue');
		
		// Merge config with passed options
		$config = Arr::merge($config->as_array(), $options);
		
		if ($container)
		{
			$this->_container = $container;
		}
		
		foreach ($config as $key => $value)
		{
			$prop = '_'.$key;
			
			if (property_exists($this, $prop))
			{
				$this->$prop = $value;
			}
		}
	}
	
	/** 
	 * Returns the container object
	 * 
	 * @return Sprig
	 */
	public function get_container()
	{
		if ($this->_container === NULL)
		{
			$this->_container = new Model_Mail_Queue;
		}
		
		return $this->_container;
	}
	
	/** 
	 * Sets the container object
	 * 
	 * @param Model_Mail_Queue $container
	 * @return $this
	 */
	public function set_container($container)
	{
		$this->_container = $container;
		
		return $this;
	}
	
	/** 
	 * Returns the mailer object
	 * 
	 * @return Dc_Mail
	 */
	public function get_mailer()
	{
		if ($this->_mailer === NULL)
		{
			$this->_mailer = new Dc_Mail;
		}
		
		return $this->_mailer;
	}
	
	/** 
	 * Sets the mailer to use
	 * 
	 * @param Dc_Mail $mailer
	 * @return $this
	 */
	public function set_mailer($mailer)
	{
		$this->_mailer = $mailer;
		
		return $this;
	}
	
	/** 
	 * Adds an email job to the queue for scheduling
	 * 
	 */
	public function add_mail($type, $header, $body, $priority = NULL)
	{
		if ($priority === NULL)
		{
			$priority = $this->_default_priority;
		}
		
		return $this->get_container()->put($type, $header, $body, $priority);
	}
	
	/** 
	 * Sends top priority mails from the queue and 
	 * mark them as sent when successful
	 * 
	 */
	public function run()
	{
		// Check if there is another run process and exit when there is
		if (Kohana::cache($this->_running_key))
		{
			Kohana::$log->add(Kohana::DEBUG, 'Mail Queue run is still processing.');
			return FALSE;
		}
		
		// Run is processing
		Kohana::cache($this->_running_key, 1);
		
		// Get the queue container
		$container = $this->get_container();
		
		// Get the next batch of mail to send
		$pending = $container->get($this->_mail_per_batch);
		
		// If no mail to process, we're out here
		if (empty($pending))
		{
			// Finished processing
			Kohana::cache($this->_running_key, 0);
		
			return FALSE;
		}
		
		$mailer = $this->get_mailer();
		$sent = array();
		
		foreach ($pending as $key => $data)
		{
			$result = $mailer->send(array(
				'from' 		=> $data['head_serialized']['from'],
				'subject' 	=> $data['head_serialized']['subject'],
				'recipient' => $data['head_serialized']['recipient'],
				'body' 		=> $data['body_serialized']['body'],
				'body_type' => $data['body_serialized']['body_type'],
				'body_part' => $data['body_serialized']['body_part'],
				'body_part_type' => $data['body_serialized']['body_part_type']
			));
			
			if ($result)
			{
				$sent[] = $data['id'];
			}
		}
		
		// Mark them as sent when successful
		if ( ! empty($sent))
		{
			$update_result = $container->sent($sent);
			
			// Finished processing
			Kohana::cache($this->_running_key, 0);
			
			return $update_result;
		}
		
		// Finished processing
		Kohana::cache($this->_running_key, 0);
		
		return FALSE;
	}
	
	/** 
	 * Removes sent mails
	 * 
	 * @return int
	 */
	public function cleanup()
	{
		// Only cleanup when there is no run processing
		if ( ! Kohana::cache($this->_running_key))
		{
			return $this->get_container()->delete_sent();
		}
		
		return FALSE;
	}
}
