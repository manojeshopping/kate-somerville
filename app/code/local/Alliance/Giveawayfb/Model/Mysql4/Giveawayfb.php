<?php
 
class Alliance_Giveawayfb_Model_Mysql4_Giveawayfb extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{   
		$this->_init('giveawayfb/giveawayfb', 'giveawayfb_id');
	}
}