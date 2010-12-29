<?php defined('SYSPATH') or die('No direct script access.');

/** 
 * Price model
 * 
 * @author lysender
 *
 */
class Model_Price extends Sprig
{
	protected $_table = 'price';
	
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
				'column' => 'item_id',
				'model' => 'item'
			)),
			'price' => new Sprig_Field_Float(array(
				'label' => 'Price',
				'places' => 2
			)),
			'effective_date' => new Sprig_Field_Timestamp(array(
				'null' => FALSE,
				'empty' => FALSE,
				'label' => 'Effective date',
				'format' => 'Y-m-d'
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
	 * Creates a new price
	 *  
	 * (non-PHPdoc)
	 * @see modules/sprig/classes/sprig/Sprig_Core::create()
	 */
	public function create()
	{
		// Set the effective date properly
		$this->effective_date = strtotime($this->verbose('effective_date').' 00:00:00');
		
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
	 * Updates price
	 *  
	 * (non-PHPdoc)
	 * @see modules/sprig/classes/sprig/Sprig_Core::update()
	 */
	public function update()
	{
		// Set the effective date properly
		$this->effective_date = strtotime($this->verbose('effective_date').' 00:00:00');
		
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
	 * Loads a price by item id
	 * 
	 * @param int $item_id
	 * @param int $relative_date
	 * @return Model_Price
	 * 
	 */
	public function load_price($item_id, $relative_date = NULL)
	{
		if ($relative_date === NULL)
		{
			$relative_date = time();
		}
		
		$query = DB::select()->from($this->_table)
			->where('item_id', '=', (int) $item_id)
			->where('effective_date', '<=', $relative_date)
			->order_by('effective_date', 'DESC')
			->limit(1);
			
		return $this->load($query);
	}
}