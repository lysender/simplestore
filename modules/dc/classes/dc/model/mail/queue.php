<?php defined('SYSPATH') or die('No direct script access.');

/** 
 * Mail queue container
 *
 * Manages the queued mail data
 */
class Dc_Model_Mail_Queue extends Dc_Model
{
	protected $_table = 'mail_queue';
	
	/** 
	 * Sets the table for this model
	 * 
	 * @param string $table
	 * @return $this
	 */
	public function set_table($table)
	{
		$this->_table = $table;
		
		return $this;
	}
	
	/** 
	 * Returns the table name
	 * 
	 * @return string
	 */
	public function get_table()
	{
		return $this->_table;
	}
	
	/** 
	 * Inserts an email job to the queue
	 * 
	 * @param int $type
	 * @param array $header
	 * @param array $body
	 * @param int $priority
	 * 
	 * @return int
	 */
	public function put($type, array $header, array $body, $priority)
	{
		$data = array(
			'type' => $type,
			'head_serialized' => serialize($header),
			'body_serialized' => serialize($body),
			'priority' => $priority,
			'sent' => 0
		);
		
		$query = DB::insert($this->_table, array_keys($data));
		$query->values($data);
		
		return $query->execute($this->_db);
	}
	
	/** 
	 * Returns an array of email jobs from the queue
	 * based on the given number of records to get $n
	 * 
	 * @param int $n
	 * @return mixed
	 */
	public function get($n)
	{
		$results = DB::select()->from($this->_table)
			->where('sent', '=', 0)
			->order_by('priority', 'ASC')
			->limit($n)
			->execute($this->_db);
		
		if (empty($results))
		{
			return FALSE;
		}
		
		$results = $results->as_array();
		
		foreach ($results as $row_index => $row)
		{
			$results[$row_index]['head_serialized'] = unserialize($row['head_serialized']);
			$results[$row_index]['body_serialized'] = unserialize($row['body_serialized']);
		}
		
		return $results;
	}

	/** 
	 * Sets the status of queue as sent
	 * according to the passed array of queue $ids
	 *
	 * @param array $ids
	 * @return int
	 */
	public function sent(array $ids)
	{
		if (empty($ids))
		{
			return 0;
		}
		
		$result =  DB::update($this->_table)
			->set(array('sent' => 1))
			->where('id', 'IN', $ids)
			->where('sent', '=', 0)
			->execute($this->_db);
			
		// Returns the number of rows inserted
		if (is_array($result))
		{
			return $result[1];
		}
		
		return FALSE;
	}
	
	/** 
	 * Deletes sent mails
	 * 
	 * @return int
	 */
	public function delete_sent()
	{
		return DB::delete($this->_table)
			->where('sent', '=', 1)
			->execute($this->_db);
	}
}
