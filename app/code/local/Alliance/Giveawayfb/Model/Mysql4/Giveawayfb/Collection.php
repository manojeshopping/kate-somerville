<?php
 
class Alliance_Giveawayfb_Model_Mysql4_Giveawayfb_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct()
	{
		//parent::__construct();
		$this->_init('giveawayfb/giveawayfb');
	}
}