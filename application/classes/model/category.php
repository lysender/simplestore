<?php defined('SYSPATH') or die('No direct script access.');

class Model_Category extends Sprig
{
	protected $_table = 'category';
	
	protected $_sorting = array('name' => 'ASC');
	
	protected function _init()
	{
		$this->_fields = array(
			'id' 		=> new Sprig_Field_Auto,
			'name' 		=> new Sprig_Field_Char(array(
				'unique' => TRUE,
				'label' => 'Item category',
				'min_length' => 2,
				'max_length' => 30,
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
		$result = DB::select()->from($this->_table)
			->order_by('name', 'ASC')
			->execute();
			
		if ($result)
		{
			$result = $result->as_array();
			
			if ( ! empty($result))
			{
				return $result;
			}
		}
		
		return FALSE;
	}
}
