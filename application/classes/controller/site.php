<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Site extends Controller_Template
{
	/**
	 * @var string
	 */
	public $template = 'site/template';
	
	/**
	 * @var string
	 */
	public $header = 'site/header';
	
	/**
	 * @var Kohana_View
	 */
	public $view;
	
	/**
	 * @var string
	 */
	public $footer = 'site/footer';
	
	/**
	 * @var Auth
	 */
	public $auth;
	
	/** 
	 * @var Session
	 */
	public $session;
	
	/** 
	 * Whether or not the user is required to be authenticated or not
	 * 
	 * @var boolean
	 */
	protected $_no_auth = FALSE;
	
	/** 
	 * Head navigation selected menu
	 * 
	 * @var string
	 */
	protected $_headnav_class = ' class="selected"';
	
	/** 
	 * before()
	 *
	 * Called before action is called
	 */
	public function before()
	{
		// make sure template is initialized first
		parent::before();
		
		// Initialize current page URL
		// we are not using query string so uri is only used
		$this->current_page = $this->request->uri();

		if ($this->auto_render)
		{
			$this->_init_template();
		}
		
		// Initialize session and authentication
		$this->_init_auth();
		
		// Initialize flash messages
		$this->_init_messages();
	}

	/** 
	 * Initializes template
	 * 
	 */
	protected function _init_template()
	{
		$this->template->styles = array(
			'media/css/screen.css'	=> 'screen, projection',
			'media/css/print.css'	=> 'print',
			'media/css/style.css'	=> 'screen, projection',
			'media/css/crud.css'	=> 'screen, projection'
		);

		$this->template->scripts = array(
			'media/js/jquery-1.4.4.min.js'
		);
		
		// Initialize head_scripts and head_readyscripts
		$this->template->head_scripts = '';
		$this->template->head_readyscripts = '';
		
		// Set head nav selected
		View::set_global('headnav_class', $this->_current_headnav());
	}
	
	/** 
	 * Initializes session and authentication
	 * 
	 */
	protected function _init_auth()
	{
		// Initialize session
		$this->session = Session::instance();
		
		// Initialize auth if present
		$this->auth = Dc_Auth::instance();
		
		$user = $this->auth->get_user(
			Sprig::factory('user'), 
			Sprig::factory('usertoken')
		);
		
		if ($user && $this->auto_render)
		{
			View::set_global('current_user', $user->username);
		}
		
		// Redirect to login for unauthenticated users
		if ($this->_no_auth === FALSE && ! $user)
		{
			$this->request->redirect('/login');
		}
	}
	
	/** 
	 * Initializes success / error messages passed via session
	 * via flash message pattern
	 * 
	 */
	protected function _init_messages()
	{
		// Display error message to template when there was a passed message
		if ($error_message = $this->session->get_once('error_message'))
		{
			if ($this->auto_render)
			{
				View::bind_global('error_message', $error_message);
			}
		}

		// Display success message to template when there was a passed message
		if ($success_message = $this->session->get_once('success_message'))
		{
			if ($this->auto_render)
			{
				View::bind_global('success_message', $success_message);
			}
		}
	}
	
	/**
	 * after()
	 * 
	 * @see system/classes/kohana/controller/Kohana_Controller_Template#after()
	 */
	public function after()
	{
		if ($this->auto_render)
		{			
			// Template disyplay logic
			$this->template->header = View::factory($this->header);
			$this->template->content = $this->view;
			
			$this->template->footer = View::factory($this->footer);			
		}

		return parent::after();
	}
	
	/** 
	 * Sets focus to a form element
	 * 
	 * @param string $id
	 * @return void
	 */
	protected function _page_setfocus($id)
	{
		if ($this->auto_render)
		{
			$this->template->head_scripts .= '$("#'.$id.'").focus();'."\n";
		}
	}
	
	/** 
	 * Sets the error message to view
	 * 
	 * @param mixed $error
	 * @param mixed $focus
	 * @return void
	 */
	protected function _page_error($error, $focus = TRUE)
	{
		if ($this->auto_render)
		{
			if (is_array($error))
			{
				$error_keys = array_keys($error);
				$first_error = current($error_keys);
	
				$this->view->error_message = implode('<br />', $error);
			
				if ($focus === TRUE)
				{
					$this->_page_setfocus($first_error);
				}
			}
			else
			{
				$this->view->error_message = $error;
			}
			
			if (is_string($focus))
			{
				$this->_page_setfocus($focus);
			}
		}
	}
	
	/** 
	 * Focus to the first error from all given errors
	 * 
	 * @param array $errors
	 */
	protected function _first_error_focus(array $errors)
	{
		$error_keys = array_keys($errors);
		
		if ( ! empty($error_keys))
		{
			$first_error = current($error_keys);
			
			$this->_page_setfocus($first_error);
		}
	}
	
	/** 
	 * Returns the current stats for head nav
	 * 
	 * @return array
	 */
	protected function _current_headnav()
	{
		$stats = array(
			'dashboard' => '',
			'inventory' => '',
			'sales' => '',
			'report' => '',
			'security' => ''
		);
		
		$dir = $this->request->directory;
		
		if ($dir && isset($stats[$dir]))
		{
			$stats[$dir] = $this->_headnav_class;
		}
		else
		{
			$stats['dashboard'] = $this->_headnav_class;
		}
		
		return $stats;
	}
}
