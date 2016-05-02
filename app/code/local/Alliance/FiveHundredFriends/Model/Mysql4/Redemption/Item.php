<?php

class Alliance_FiveHundredFriends_Model_Mysql4_Redemption_Item extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{   
		$this->_init('alliance_fivehundredfriends/redemption_item', 'entity_id');
	}
}
