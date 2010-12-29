<?php defined('SYSPATH') or die('No direct script access.');

/** 
 * Stock pages
 * 
 * @author lysender
 *
 */
class Controller_Inventory_Price extends Controller_Site
{
	/** 
	 * Stock list page
	 * 
	 */
	public function action_index()
	{
		$this->template->title = 'Pricing';
		$this->template->scripts[] = 'media/js/item-price.js';
		
		$this->view = View::factory('inventory/price/index');
		
		$this->_page_setfocus('search_key');
	}
	
	/** 
	 * Ajax post search
	 * 
	 */
	public function action_search()
	{
		$this->auto_render = FALSE;
		
		$result = array(
			'success' => FALSE,
			'content' => NULL
		);
		
		$keyword = Arr::get($_POST, 'keyword');
		
		// Ensure that only POST request is accepted
		if (Request::$method == 'POST' && $keyword)
		{
			$searched = Sprig::factory('item')->search_with_price($keyword);
			
			if ( ! empty($searched))
			{
				$result['success'] = 1;
				$result['content'] = $this->_searched_to_html($searched);
			}
		}
		
		$this->request->headers['Content-type'] = 'application/json';
		$this->request->response = json_encode($result);
	}
	
	/** 
	 * Ajax item lookup
	 * 
	 */
	public function action_itemlookup()
	{
		$this->auto_render = FALSE;
		
		$result = array(
			'success' => FALSE,
			'price' => null,
			'effective_date' => null,
			'new_price' => 0,
			'new_effective_date' => date('Y-m-d')
		);
		
		$item_id = Arr::get($_POST, 'item_id');
		
		$item = Sprig::factory('item', array('id' => $item_id))->load();
		
		if ($item->loaded())
		{
			$result['success'] = 1;
			$result['item_id'] = $item->id;
			$result['name'] = $item->name;
			$result['description'] = $item->description;
			
			// Load price when exists
			$price = Sprig::factory('price')->load_price($item_id);
			
			if ($price->loaded())
			{
				$result['price'] = $price->price;
				$result['effective_date'] = $price->verbose('effective_date');
				
				$result['new_price'] = $result['price'];
				$result['new_effective_date'] = date('Y-m-d');
			}
		}
		
		$this->request->headers['Content-type'] = 'application/json';
		$this->request->response = json_encode($result);
	}
	
	/** 
	 * Sets the price for an item
	 * 
	 */
	public function action_setprice()
	{
		$this->auto_render = FALSE;
		
		$result = array(
			'success' => FALSE
		);
		
		$data = Arr::extract($_POST, array(
			'item',
			'price',
			'effective_date'
		));
		
		if ($data['item'])
		{
			$old_price = Sprig::factory('price')->load_price($data['item']);
			
			// If the target price record already exists
			// and the effective date is the same as the one set
			if ($old_price->loaded() && $data['effective_date'] == $old_price->verbose('effective_date'))
			{
				// Then simply update the price
				$old_price->price = $data['price'];
				
				try
				{
					$old_price->update();
					
					$result['success'] = 1;
				}
				catch (Validate_Exception $e)
				{
					$result['message'] = implode("\n", $e->array->errors('price'));
				}
				catch (Exception $e)
				{
					$result['message'] = $e->getMessage(); //'An error occured while setting price';
				}
			}
			else 
			{
				// All other transaction will just create new price entry
				$new_price = Sprig::factory('price')->values($data);
				
				try
				{
					$new_price->create();
					
					$result['success'] = 1;
				}
				catch (Validate_Exception $e)
				{
					$result['message'] = implode("\n", $e->array->errors('price'));
				}
				catch (Exception $e)
				{
					$result['message'] = 'NEW: '.$e->getMessage()."\n".var_export($data, TRUE); //'An error occured while setting price';
				}
			}
		}
		
		$this->request->headers['Content-type'] = 'application/json';
		$this->request->response = json_encode($result);
	}
	
	/** 
	 * Convertes searched items into html tr's
	 * 
	 * @param array $searched
	 * @return string
	 */
	protected function _searched_to_html(array $searched)
	{
		$s = '';
		
		foreach ($searched as $key => $row)
		{
			$class = ($key % 2 == 0) ? 'even' : 'odd';
			$s .= '<tr class="'.$class.'" id="pricerow-'.$row['id'].'">'
					.'<td><a class="search-select-price" href="#" id="sel-'.$row['id'].'" title="Select item">Select price</a></td>'
					.'<td>'.HTML::chars($row['name']).'</td>'
					.'<td>'.HTML::chars($row['description']).'&nbsp;</td>'
					.'<td class="price-cell">'.$row['price'].'&nbsp;</td>'
				 .'</tr>';
		}
		
		return $s;
	}
}