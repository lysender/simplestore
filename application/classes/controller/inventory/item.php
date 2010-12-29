<?php defined('SYSPATH') or die('No direct script access.');

/** 
 * Inventory menu
 * 
 * @author lysender
 *
 */
class Controller_Inventory_Item extends Controller_Site
{
	/** 
	 * Item model
	 * 
	 * @var Model_Item
	 */
	protected $_item;
	
	/** 
	 * @var int
	 */
	protected $_category_id;
	
	/**
	 * Ensures the edit/delete accepts only valid requests
	 *  
	 * (non-PHPdoc)
	 * @see application/classes/controller/Controller_Site::before()
	 */
	public function before()
	{
		parent::before();
		
		if (in_array($this->request->action, array('edit', 'delete')))
		{
			$id = $this->request->param('id');
			
			// Check param
			if ( ! $id)
			{
				$this->session->set('error_message', 'Invalid parameter');
				$this->request->redirect('/inventory/item');
			}
			
			$item = Sprig::factory('item', array('id' => (int) $id))->load();

			if ( ! $item->loaded())
			{
				$this->session->set('error_message', 'Item not found');
				$this->request->redirect('/inventory/item');
			}
			
			$this->_item = $item;
		}
		
		// Initialize selected category if present
		// Reuse route id as category
		if ($this->_category_id = $this->request->param('id'))
		{
			$this->session->set('selected_category', $this->_category_id);
		}
		else
		{
			if (in_array($this->request->action, array('edit', 'delete')))
			{
				$this->_category_id = $this->session->get('selected_category');
			}
			else 
			{
				$this->session->delete('selected_category');
			}
		}
	}
	
	/** 
	 * Item list page
	 * 
	 */
	public function action_index()
	{
		$this->view = View::factory('inventory/item/index');
		$this->template->title = 'Item Masterlist';
		
		$this->template->styles['media/css/pagination.css'] = 'screen, projection';
		$this->template->scripts[] = 'media/js/crud.js';
		$this->template->scripts[] = 'media/js/itemlist.js';
		
		$item = Sprig::factory('item');
		
		// Reuse route id as page
		$page = Arr::get($_GET, 'page');
		
		$this->view->items = $item->get_paged($this->_category_id, $page);
		
		// Load categories
		$categories = Sprig::factory('category')->select_list('id', 'name');
		$categories = Arr::merge(array('' => 'All'), $categories);

		$this->view->categories = $categories;
		$this->view->selected_category = $this->_category_id;
		
		// Pagination
		$paginate = new Dc_Paginate;
		
		if ($this->_category_id)
		{
			$paginate->verbose_first_page = TRUE;
		}
		
		$this->view->paginator = $paginate->render(
			('/inventory/item'),
			('/inventory/item/index/'.($this->_category_id ? $this->_category_id : '').'?page='),
			$item->get_total(),
			Model_Item::ITEMS_PER_PAGE,
			$page
		);
	}
	
	/** 
	 * Add item page
	 * 
	 */
	public function action_add()
	{
		$this->view = View::factory('inventory/item/add');
		$this->template->title = 'Item - Add';
		
		$item = Sprig::factory('item');
		$csrf_check = TRUE;
		
		if ($this->_category_id)
		{
			// Load category as default
			$item->category = $this->_category_id;
		}
		
		if (Request::$method == 'POST')
		{
			$item->values($_POST);
			
			if ($csrf_check = Security::check(Arr::get($_POST, 'csrf')))
			{
				try
				{
					$item->create();
					
					$this->session->set('success_message', 'A new item has been added');
					$this->request->redirect('/inventory/item'.($this->_category_id ? '/index/'.$this->_category_id : ''));
				}
				catch (Validate_Exception $e)
				{
					$this->_page_error($e->array->errors('category'));
				}
				catch (Exception $e)
				{
					$this->_page_error($e->getMessage(), 'category');
				}
			}
			else 
			{
				$this->_page_error('Session timeout, try again');
			}
		}
		else
		{			
			$this->_page_setfocus('category');
		}
		
		$this->view->item = $item;
		$this->view->selected_category = $this->_category_id;
	}
	
	/** 
	 * Edit item page
	 * 
	 */
	public function action_edit()
	{
		$this->view = View::factory('inventory/item/edit');
		$this->template->title = 'Item - Edit';
		
		if ($this->_category_id)
		{
			// Load category as default
			$this->_item->category = $this->_category_id;
		}
		
		$csrf_check = TRUE;
		
		if (Request::$method == 'POST')
		{
			$this->_item->values($_POST);
			
			if ($csrf_check = Security::check(Arr::get($_POST, 'csrf')))
			{
				try
				{
					$this->_item->update();
					
					$this->session->set('success_message', 'Item has been updated');
					$this->request->redirect('/inventory/item'.($this->_category_id ? '/index/'.$this->_category_id : ''));
				}
				catch (Validate_Exception $e)
				{
					$this->_page_error($e->array->errors('category'));
				}
				catch (Exception $e)
				{
					$this->_page_error($e->getMessage(), 'category');
				}
			}
			else 
			{
				$this->_page_error('Session timeout, try again');
			}
		}
		else
		{			
			$this->_page_setfocus('category');
		}
		
		$this->view->item = $this->_item;
		$this->view->selected_category = $this->_category_id;
	}
	
	/** 
	 * Item delete
	 * 
	 */
	public function action_delete()
	{
		// Only allow delete via POST methos
		$delete_params = Arr::extract($_POST, array('referer', 'target'));
		
		if ($delete_params['referer'] === NULL || $delete_params['target'] === NULL)
		{
			$this->view = View::factory('site/cruddelete');
			
			$this->template->title = 'Delete item';
			
			$this->view->delete_subject = 'item';
			$this->view->delete_referer = URL::site('/inventory/item');
			$this->view->delete_target = URL::site('/inventory/item/delete/'.$this->_item->id);
			
			$this->view->delete_record_key = $this->_item->id;
			$this->view->delete_record_detail = $this->_item->name;
		}
		else 
		{
			// Disable template
			$this->auto_render = FALSE;
			$csrf_check = TRUE;
			
			if (Arr::get($_POST, 'yes') && $csrf_check = Security::check(Arr::get($_POST, 'csrf')))
			{
				// Delete
				if ($this->_item->delete())
				{
					// Success
					$this->session->set('success_message', 'Item has been deleted');
				}
				else
				{
					$this->session->set('error_message', 'There was a problem while deleting the item');
				}
			}
			
			if ( ! $csrf_check)
			{
				$this->session->set('error_message', 'Session time out, try again');
			}
			
			$this->request->redirect('/inventory/item');
		}
	}
}