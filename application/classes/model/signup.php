<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * User model for signup
 */
class Model_Signup extends Sprig
{	
	protected function _init()
	{
		$this->_fields += array(
			'username' => new Sprig_Field_Char(array(
				'empty'  => FALSE,
				'min_length' => 4,
				'max_length' => 32,
				'rules'  => array(
					'regex' => array('/^[-\pL\pN_.]++$/uD')
				)
			)),
			'email' 	=> new Sprig_Field_Email(array(
				'min_length' => 4,
				'max_length' => 127
			)),
			'password' => new Sprig_Field_Password(array(
				'hash_with' => NULL,
				'empty' => FALSE,
				'min_length' => 5,
				'max_length' => 16
			)),
			'password_confirm' => new Sprig_Field_Password(array(
				'hash_with' => NULL,
				'empty' => TRUE,
				'in_db' => FALSE,
				'rules' => array(
					'matches' => array('password'),
				),
			)),
			'csrf' => new Sprig_Field_Char(array(
				'default' => Security::token(),
				'in_db' => FALSE,
				'attributes' => array(
					'type' => 'hidden'
				),
				'rules' => array(
					'not_empty' => NULL,
					'Security::check' => NULL
				)
			))
		);
	}
	
	/** 
	 * Sign up and account
	 * 
	 * @throws Validate_Exception
	 * @throws Exception
	 * @return Sprig
	 */
	public function signup()
	{
		$data = $this->check($this->as_array());
		
		$user = Sprig::factory('user');
		$user->values($data);
		return $user->signup();
	}
}