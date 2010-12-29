<?php defined('SYSPATH') or die('No direct script access.');

/** 
 * Item model
 * 
 * @author lysender
 *
 */
class Model_Stock extends Sprig
{
	const ITEMS_PER_PAGE = 50;
	
	/** 
	 * Table name
	 * 
	 * @var string
	 */
	protected $_table = 'stock';
	
	/** 
	 * Count for pagination
	 * 
	 * @var int
	 */
	protected $_total_count;
	
	/** 
	 * Initialize
	 * 
	 * (non-PHPdoc)
	 * @see modules/sprig/classes/sprig/Sprig_Core::_init()
	 */
	protected function _init()
	{
		$this->_fields = array(
			'id' 		=> new Sprig_Field_Auto,
			'item'	=> new Sprig_Field_BelongsTo(array(
				'null'	=> FALSE,
				'empty' => FALSE,
				'column' => 'item_id',
				'model' => 'item',
				'attributes' => array(
					'id' => 'item'
				)
			)),
			'quantity' => new Sprig_Field_Integer(array(
				'label' => 'Code name',
				'default' => 0
			)),
			'date_created' => new Sprig_Field_Timestamp,
			'created_by' => new Sprig_Field_Integer(),
			'date_modified' => new Sprig_Field_Timestamp(array(
				'empty' => TRUE,
				'null' => TRUE
			)),
			'modified_by' => new Sprig_Field_Integer(array(
				'empty' => TRUE,
				'null' => TRUE
			))
		);
	}
	
	/**
	 * Creates a new category
	 *  
	 * (non-PHPdoc)
	 * @see modules/sprig/classes/sprig/Sprig_Core::create()
	 */
	public function create()
	{
		$this->date_created = time();
		
		$user = Dc_Auth::instance()->get_user(
			Sprig::factory('user'),
			Sprig::factory('usertoken')
		);
		
		if ($user)
		{
			$this->created_by = $user->id;
		}
		
		return parent::create();
	}
	
	/**
	 * Updates category
	 *  
	 * (non-PHPdoc)
	 * @see modules/sprig/classes/sprig/Sprig_Core::update()
	 */
	public function update()
	{
		$this->date_modified = time();
		
		$user = Dc_Auth::instance()->get_user(
			Sprig::factory('user'),
			Sprig::factory('usertoken')
		);
		
		if ($user)
		{
			$this->modified_by = $user->id;
		}
		
		return parent::update();
	}
	
	/** 
	 * Returns all categories
	 * 
	 */
	public function get_all()
	{
		$result = DB::select(
				'i.id',
				'i.category_id', 
				array('c.name', 'category_name'),
				'i.code_name',
				'i.name',
				'i.description'
			)
			->from(array($this->_table, 'i'))
			->join(array('category', 'c'))
			->on('i.category_id', '=', 'c.id')
			->order_by('i.name', 'ASC')
			->execute(
			)->as_array();
			
		if ( ! empty($result))
		{
			return $result;
		}
		
		return FALSE;
	}
	
	/**
	 * Returns paginated item records
	 *
	 * @param int $category_id
	 * @param int $page
	 * @param string $sort
	 * @return array
	 */
	public function get_paged($category_id = NULL, $page = 1, $sort = 'ASC')
	{		
		$page = (int) $page;
		
		// Pre calculate totals
		$total_rec = $this->get_total($category_id);
		$total_pages = $this->get_total_pages($total_rec);
		
		// Determine the correct page
		if ($page < 1)
		{
			$page = 1;
		}
		
		// Make sure the page does not exceed total pages
		if ($page > $total_pages)
		{
			$page = $total_pages;
		}
		
		$offset = ($page - 1) * self::ITEMS_PER_PAGE;
		
		// Set limit and offset and execute
		$query = DB::select(
				's.id',
				's.item_id',
				'i.category_id', 
				array('c.name', 'category_name'),
				'i.code_name',
				'i.name',
				'i.description'
			)
			->from(array($this->_table, 's'))
			->join(array('item', 'i'))
			->on('s.item_id', '=', 'i.id')
			->join(array('category', 'c'))
			->on('i.category_id', '=', 'c.id');
			
		if ($category_id)
		{
			$query->where('i.category_id', '=', $category_id);
		}
			
		$result = $query->order_by('c.name', 'ASC')
			->order_by('i.name', 'ASC')
			->limit(self::ITEMS_PER_PAGE)
			->offset($offset)
			->execute();
		
		if ( ! empty($result))
		{
			return $result->as_array();
		}
		
		return FALSE;
	}
	
	/**
	 * Returns the total number of shows
	 *
	 * @param string $cat
	 * @return int
	 */
	public function get_total($category_id = NULL)
	{
		if ($this->_total_count === NULL)
		{
			$query = DB::select('COUNT("*") AS show_total_count')
				->from($this->_table);
				
			if ($category_id)
			{
				$query->where('category_id', '=', $category_id);
			}
			
			$this->_total_count = $query->execute()
				->get('show_total_count');
		}
		
		return $this->_total_count;
	}
	
	/**
	 * Returns the total number of pages
	 * for a given total record count
	 *
	 * @param int $total_rec
	 * @return int
	 */
	function get_total_pages($total_rec)
	{
		$ret = ceil($total_rec / self::ITEMS_PER_PAGE);
		
		if ($ret > 0)
		{
			return (int) $ret;
		}
		
		return 1;
	}
}
