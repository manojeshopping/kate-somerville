<?php

class Alliance_FiveHundredFriends_Model_Mysql4_Redemption extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{   
		$this->_init('alliance_fivehundredfriends/redemption', 'entity_id');
	}
}
