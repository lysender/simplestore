<?php defined('SYSPATH') or die('No direct script access.');

/** 
 * Item model
 * 
 * @author lysender
 *
 */
class Model_Item extends Sprig
{
	/** 
	 * Table name
	 * 
	 * @var string
	 */
	protected $_table = 'item';
	
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
				'empty' => TRUE,
				'column' => 'category_id',
				'model' => 'category',
				'attributes' => array(
					'id' => 'category'
				)
			)),
			'code_name' => new Sprig_Field_Char(array(
				'unique' => TRUE,
				'label' => 'Code name',
				'min_length' => 3,
				'max_length' => 16,
				'rules' => array(
					'alpha_numeric' => NULL
				),
				'attributes' => array(
					'id' => 'code_name'
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
