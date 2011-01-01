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
	 * Current page number
	 * 
	 * @var int
	 */
	protected $_page;
	
	/**
	 * Ensures the edit/delete accepts only valid requests
	 *  
	 * (non-PHPdoc)
	 * @see application/classes/controller/Controller_Site::before()
	 */
	public function before()
	{
		parent::before();
		
		// Route: /id/param2/param3 = /category_id/page/item_id
		$this->_category_id = (int) $this->request->param('id');
		
		$this->_page = (int) $this->request->param('param2', 1);
		
		// Default to page one
		if ( ! $this->_page)
		{
			$this->_page = 1;
		}
		
		// For edit/delete, ensure that valid item id is set
		if (in_array($this->request->action, array('edit', 'delete')))
		{
			$id = (int) $this->request->param('param3');
			
			// Check param
			if ( ! $id)
			{
				$this->session->set('error_message', 'Invalid parameter');
				$this->request->redirect('/inventory/item/index/'.$this->_category_id.'/'.$this->_page);
			}
			
			// Load item if it exist
			$item = Sprig::factory('item', array('id' => $id))->load();

			if ( ! $item->loaded())
			{
				// Item not found, go back
				$this->session->set('error_message', 'Item not found');
				$this->request->redirect('/inventory/item/index/'.$this->_category_id.'/'.$this->_page);
			}
			
			// Item found, keep it
			$this->_item = $item;
		}
		
		// Set to view the category_id and current page
		if ($this->auto_render)
		{
			View::set_global('selected_category', $this->_category_id);
			View::set_global('current_page', $this->_page);
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
		$this->template->scripts[] = 'media/js/jquery.tablesorter.min.js';

		$this->template->head_readyscripts .= '
			$(".reg-list").tablesorter({
				widgets: ["zebra"],
				headers: {
					0: {sorter: false},
					4: {sorter: false}
				}
			});
		';
		
		$item = Sprig::factory('item');
		
		$this->view->items = $item->get_paged($this->_category_id, $this->_page);
		
		// Load categories
		$categories = Sprig::factory('category')->select_list('id', 'name');

		// To view
		$this->view->categories = Arr::merge(array('' => 'All'), $categories);
		
		// Pagination
		$paginate = new Dc_Paginate;
		
		if ($this->_category_id)
		{
			$paginate->verbose_first_page = TRUE;
		}
		
		$this->view->paginator = $paginate->render(
			('/inventory/item/index/'.$this->_category_id),
			('/inventory/item/index/'.$this->_category_id.'/'),
			$item->get_total(),
			Model_Item::ITEMS_PER_PAGE,
			$this->_page
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
		
		// Load category as default
		if ($this->_category_id)
		{
			$item->category = $this->_category_id;
		}
		
		// Only accept post request
		if (Request::$method == 'POST')
		{
			$item->values($_POST);
			
			if ($csrf_check = Security::check(Arr::get($_POST, 'csrf')))
			{
				try
				{
					$item->create();
					
					$this->session->set('success_message', 'A new item has been added');
					$this->request->redirect('/inventory/item/index/'.$item->category->id.'/'.$this->_page);
				}
				catch (Validate_Exception $e)
				{
					$this->_page_error($e->array->errors('category'));
				}
				catch (Exception $e)
				{
					$this->_page_error('Temporary network failure, try again later', 'category');
					Kohana::$log->add(Kohana::DEBUG, $e->getMessage());
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
	}
	
	/** 
	 * Edit item page
	 * 
	 */
	public function action_edit()
	{
		$this->view = View::factory('inventory/item/edit');
		$this->template->title = 'Item - Edit';
		
		$csrf_check = TRUE;
		
		// Only accept post requests
		if (Request::$method == 'POST')
		{
			$this->_item->values($_POST);
			
			if ($csrf_check = Security::check(Arr::get($_POST, 'csrf')))
			{
				try
				{
					$this->_item->update();
					
					$this->session->set('success_message', 'Item has been updated');
					$this->request->redirect('/inventory/item/index/'.$this->_item->category->id.'/'.$this->_page);
				}
				catch (Validate_Exception $e)
				{
					$this->_page_error($e->array->errors('category'));
				}
				catch (Exception $e)
				{
					$this->_page_error('Temporary network failure, try again later', 'category');
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
			$this->view->delete_referer = URL::site('/inventory/item/index/'.$this->_category_id.'/'.$this->_page);
			$this->view->delete_target = URL::site('/inventory/item/delete/'.$this->_category_id.'/'.$this->_page.'/'.$this->_item->id);
			
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
			
			$this->request->redirect('/inventory/item/index/'.$this->_category_id.'/'.$this->_page);
		}
	}
}
