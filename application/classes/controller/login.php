<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Login controller
 * 
 * Handles login and logout
 */
class Controller_Login extends Controller_Site
{
	/** 
	 * Allows no login
	 * 
	 * @var boolean
	 */
	protected $_no_auth = TRUE;
	
	/** 
	 * Login page
	 * 
	 */
	public function action_index()
	{
		$this->template->title = 'Login';
		$this->view = View::factory('login/index')
			->bind('login', $authlogin);	
		
		$authlogin = Sprig::factory('login');
		
		if ( ! empty($_POST))
		{
			try
			{
				$authlogin->values($_POST)->login();
					
				// Successful login, go to main page
				$this->request->redirect('/');
			}
			catch (Validate_Exception $e)
			{
				$this->_page_error($e->array->errors('login'));
			}
			catch (Dc_Auth_Exception $e)
			{
				$this->_page_error($e->getMessage(), 'username');
			}
			catch (Exception $e)
			{
				$this->_page_error('Temporary network failure, try again later', 'username');
				
				Kohana::$log->add(
					Kohana::ERROR,
					$e->getMessage()."\n".$e->getTraceAsString()
				)->write();
			}
		}
		else
		{
			$this->_page_setfocus('username');
		}
	}
	
	public function action_logout()
	{
		$this->auto_render = false;
		
		if (Security::check($this->request->param('id')))
		{
			$this->auth->logout();
		}
		
		// Go back to main
		$this->request->redirect('/');
	}
}
