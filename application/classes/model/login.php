<?php
/** 
 * Login model
 * 
 * @author Leonel
 */
class Model_Login extends Sprig
{
	/** 
	 * Initialize
	 * 
	 * @see modules/sprig/classes/sprig/Sprig_Core::_init()
	 */
	protected function _init()
	{
		$this->_fields += array(
			'username' => new Sprig_Field_Char(array(
				'attributes' => array(
					'id' => 'username'
				),		
				'rules' => array(
					'not_empty' => NULL
				)
			)),
			'password' => new Sprig_Field_Password(array(
				'hash_with' =>NULL,
				'attributes' => array(
					'id' => 'password'
				),
				'rules' => array(
					'not_empty' => NULL
				)
			)),
			'remember' => new Sprig_Field_Boolean(array(
				'attributes' => array(
					'id' => 'remember'
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
	 * Login
	 * 
	 * @param array $data
	 * @throws Exception
	 * @return boolean
	 */
	public function login()
	{
		// If check fails, it will automatically throw an exception
		if ($this->check($this->as_array()))
		{
			$auth = Dc_Auth::instance();
			$valid = $auth->login(
				Sprig::factory('user'),
				array('username' => $this->username, 'password' => $this->password), 
				$this->remember ? Sprig::factory('usertoken') : NULL
			);
			
			if ($valid)
			{
				return TRUE;
			}
		}
		
		throw new Dc_Auth_Exception('Incorrect username or password');
	}
}