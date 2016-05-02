<?php
 
class Alliance_Gp_Model_Mysql4_Gp_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct()
	{
		//parent::__construct();
		$this->_init('gp/gp');
	}
}