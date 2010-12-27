<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Signup extends Controller_Site
{	
	public function action_index()
	{
		if ($this->auth->logged_in())
		{
			$this->auth->logout();
			
			$this->request->redirect('/signup');
		}
		
		$this->template->title = 'Signup';
		$this->view = View::factory('signup/index')
			->bind('signup', $signup);
		
		$signup = Sprig::factory('signup');
		
			
		if ( ! empty($_POST))
		{
			try
			{
				$signup->values($_POST)->signup();
					
				// Successful login, to to main page
				$success_token = Text::random('alnum', 32);
				$this->session->set('signup_success_token', $success_token);
				$this->request->redirect('/signup/success/'.$success_token);
			}
			catch (Validate_Exception $e)
			{
				$this->view->error_message = implode('<br />', $e->array->errors('signup'));
			}
			catch (Exception $e)
			{
				$this->view->error_message = 'Temporary network failure, try again later';
				
				Kohana::$log->add(
					Kohana::ERROR,
					$e->getMessage()."\n".$e->getTraceAsString()
				)->write();
			}
		}
	}
	
	public function action_success()
	{
		$this->template->title = 'Signup successfull!';
		$this->view = View::factory('signup/success');
		
		$success_token = $this->session->get('signup_success_token');
		$param_token = $this->request->param('id');
		
		$this->session->delete('signup_success_token');
		
		if ($param_token === NULL || $success_token != $param_token)
		{
			$this->request->redirect('/');
		}
	}
	
	public function action_activate()
	{
		$this->template->title = 'Account activation';
		$this->view = View::factory('signup/activate');
		
		$user = Sprig::factory('user');
		
		try
		{
			$user->activate($this->request->param('id'));
			
			$this->template->set_global('current_user', $user->username);
			
			$this->view->activated_user = $user->username;
			$this->view->success_message = 'Your account is now active!';
		}
		catch (Dc_Auth_Exception $e)
		{
			$this->view->error_message = $e->getMessage();
		}
		catch (Exception $e)
		{
			$this->view->error_message = 'Temporary network failure, try again later';
			
			Kohana::$log->add(
				Kohana::ERROR,
				$e->getMessage()."\n".$e->getTraceAsString()
			)->write();
		}
	}
}
