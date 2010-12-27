<?php defined('SYSPATH') or die('No direct script access.');

require_once APPPATH.'/vendor/swift/swift_required.php';

/**
 * Dc_Mailer - SwiftMailer wrapper
 * 
 * SwiftMailer must exist inside APPATH/vendor/swift/
 * 
 */
class Dc_Mail
{
	/** 
	 * STMP server to connect
	 * 
	 * @var string
	 */
	protected $_smtp_server;
	
	/** 
	 * Port number to connect
	 * 
	 * @var int
	 */
	protected $_port;
	
	/** 
	 * Whether or not encryption is used
	 * 
	 * @var boolean
	 */
	protected $_enc;
	
	/** 
	 * Encryption type
	 * 
	 * @var string
	 */
	protected $_enc_type;
	
	/** 
	 * Username to use when authenticating
	 * 
	 * @var string
	 */
	protected $_username;
	
	/** 
	 * Password to use when authenticating
	 * 
	 * @var string
	 */
	protected $_password;
	
	/** 
	 * Default from email address when no from is specified
	 * 
	 * @var mixed
	 */
	protected $_from;
	
	/** 
	 * SwiftMailer instance
	 * 
	 * @var Swift_Mailer
	 */
	protected $_mailer;
	
	/** 
	 * Initializes the mailer object with configurations
	 * 
	 * @param array $options
	 */
	public function __construct(array $options = array())
	{
		$config = Kohana::config('dc/mail');
		
		// Merge with the passed options
		$config = Arr::merge($config->as_array(), $options);
		
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
	 * Sends email using the current smtp account
	 * for auto mailer
	 * 
	 * $data possible keys
	 * 		subject
	 * 		recipient
	 * 		body
	 * 		body_type
	 * 		from
	 * 		cc
	 * 		body_part
	 * 		body_part_type
	 * 
	 * @param array $data
	 * @return integer
	 */
	public function send(array $data)
	{
		$mailer = $this->_mailer();
		
		// Create the message
		$message = Swift_Message::newInstance()
		  ->setSubject($data['subject'])
		  ->setTo($data['recipient'])
		  ->setBody($data['body'], $data['body_type']);
		  
		// If no from is defined, use the `from` from config
		$from = NULL;
		
		if (isset($data['from']))
		{
			$from = $data['from'];
		}
		else
		{
			$from = $this->_from;
		}
		
		// Set from
		$message->setFrom($from);
		  
		// If cc is defined, add cc
		if (isset($data['cc']) && !empty($data['cc']))
		{
			$message->setCc($data['cc']);
		}
		
		// If body part is defined, add it
		if (isset($data['body_part']))
		{
			$message->addPart($data['body_part'], $data['body_part_type']);
		}
		
		// Send the message
		try
		{
			$result = $mailer->send($message);
		}
		catch (Exception $e)
		{
			// Log the error
			Kohana::$log->add(Kohana::ERROR, Kohana::exception_text($e));
			
			// If we are in development mode, re-throw the exception 
			if (Kohana::$environment == Kohana::DEVELOPMENT || Kohana::$environment == Kohana::TESTING)
			{
				throw $e;
			}
			
			return FALSE;
		}
		
		return $result;
	}
	
	/** 
	 * Returns an instance of Swift_Mailer
	 * 
	 * @return Swift_Mailer
	 */
	protected function _mailer()
	{
		if ($this->_mailer === NULL)
		{
			// Always use UTF-8
			Swift_Preferences::getInstance()->setCharset('utf-8');
			
			// Create transport
			$transport = Swift_SmtpTransport::newInstance()
				->setHost($this->_smtp_server)
				->setEncryption($this->_enc_type)
				->setPort($this->_port)
				->setUsername($this->_username)
				->setPassword($this->_password);
			
			// Create mailer
			$this->_mailer = Swift_Mailer::newInstance($transport);
		}
		
		return $this->_mailer;
	}
}
