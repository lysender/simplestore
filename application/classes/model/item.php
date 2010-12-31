<?php defined('SYSPATH') or die('No direct script access.');

/** 
 * Item model
 * 
 * @author lysender
 *
 */
class Model_Item extends Sprig
{
	const ITEMS_PER_PAGE = 50;
	
	/** 
	 * Table name
	 * 
	 * @var string
	 */
	protected $_table = 'item';
	
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
			'category'	=> new Sprig_Field_BelongsTo(array(
				'null'	=> FALSE,
				'empty' => FALSE,
				'column' => 'category_id',
				'model' => 'category',
				'attributes' => array(
					'id' => 'category'
				)
			)),
			'code_name' => new Sprig_Field_Char(array(
				'unique' => TRUE,
				'null' => TRUE,
				'label' => 'Code name',
				'min_length' => 3,
				'max_length' => 16,
				'rules' => array(
					'alpha_dash' => NULL
				),
				'attributes' => array(
					'id' => 'code_name'
				),
				'callbacks' => array(
					'generate_codename' => array($this, 'generate_codename')
				)
			)),
			'name' => new Sprig_Field_Char(array(
				'unique' => TRUE,
				'label' => 'Name',
				'min_length' => 3,
				'max_length' => 64,
				'attributes' => array(
					'id' => 'name'
				)
			)),
			'description' => new Sprig_Field_Text(array(
				'empty' => TRUE,
				'label' => 'Description',
				'min_length' => 5,
				'max_length' => 256,
				'attributes' => array(
					'id' => 'description'
				)
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
	 * Callback filter for generating codename
	 * 
	 * @param Validate $array
	 * @param string $field
	 */
	public function generate_codename(Validate $array, $field)
	{
		// Check if code_name has content
		$code_name = $array[$field];
		
		if ( ! $code_name)
		{
			// Generate from item name
			$name = isset($array['name']) ? $array['name'] : NULL;

			if ($name)
			{
				$code_name = strtoupper(preg_replace('/[^-a-zA-Z0-9]/', '', $name));
			}
		}
		
		$array[$field] = Text::limit_chars($code_name, 16);
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
				'i.id',
				'i.category_id', 
				array('c.name', 'category_name'),
				'i.code_name',
				'i.name',
				'i.description'
			)
			->from(array($this->_table, 'i'))
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
	public function get_total_pages($total_rec)
	{
		$ret = ceil($total_rec / self::ITEMS_PER_PAGE);
		
		if ($ret > 0)
		{
			return (int) $ret;
		}
		
		return 1;
	}
	
	/** 
	 * Searches an item including price
	 * 
	 * @param string $keyword
	 * @param int $limit
	 */
	public function search_with_price($keyword, $limit = 10, $relative_date = NULL)
	{
		if ( ! $relative_date)
		{
			$relative_date = time();
		}
		
		// Inner query to latest price per item
		$inner_query = DB::select('id')->from('price')
			->where('item_id', '=', DB::expr('i.id'))
			->where('effective_date', '<=', (int) $relative_date)
			->order_by('effective_date', 'DESC')
			->limit(1);
			
		// Outer query
		$query = DB::select(
				'i.id',
				'i.name',
				'i.description',
				'i.category_id',
				array('p.id', 'price_id'),
				'p.price',
				'p.effective_date'
			)
			->from(array($this->_table, 'i'))
			->join(array('price', 'p'), 'LEFT')
			->on('p.id', '=', $inner_query)
			->where('i.name', 'LIKE', '%'.$keyword.'%')
			->or_where('i.description', 'LIKE', '%'.$keyword.'%')
			->order_by('i.name', 'ASC')
			->limit($limit);
			
		return $query->execute()->as_array();
	}
	
	/** 
	 * Returns paginated items with price
	 * 
	 * @param int $category_id
	 * @param int $page
	 * @param int $relative_date
	 * @return array
	 */
	public function get_paged_price($category_id = NULL, $page = 1, $relative_date = NULL)
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
		
		if ( ! $relative_date)
		{
			$relative_date = time();
		}
		
		if ( ! $relative_date)
		{
			$relative_date = time();
		}
		
		// Inner query to latest price per item
		$inner_query = DB::select('id')->from('price')
			->where('item_id', '=', DB::expr('i.id'))
			->where('effective_date', '<=', (int) $relative_date)
			->order_by('effective_date', 'DESC')
			->limit(1);
			
		// Outer query
		$query = DB::select(
				'i.id',
				'i.name',
				'i.description',
				'i.category_id',
				array('c.name', 'category_name'),
				array('p.id', 'price_id'),
				'p.price',
				'p.effective_date'
			)
			->from(array($this->_table, 'i'))
			->join(array('category', 'c'))
			->on('i.category_id', '=', 'c.id')
			->join(array('price', 'p'), 'LEFT')
			->on('p.id', '=', $inner_query);
			
		// Add category filter if specified
		if ($category_id)
		{
			$query->where('i.category_id', '=', (int) $category_id);
		}
		
		// Finish query
		$query->order_by('c.name', 'ASC')
			->order_by('i.name', 'ASC')
			->limit(self::ITEMS_PER_PAGE)
			->offset($offset);
			
		return $query->execute()->as_array();
	}
}
