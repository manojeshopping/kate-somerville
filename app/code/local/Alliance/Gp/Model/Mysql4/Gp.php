<?php

class Alliance_Gp_Model_Mysql4_Gp extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{   
		$this->_init('gp/gp', 'order_id');
	}
}