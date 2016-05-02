<?php
/**
 * Log creation resource class. Logs entries to database
 * for processing.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Model_Mysql4_Log extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		$this->_init('ordergroove/log', 'entity_id');
	}
}
