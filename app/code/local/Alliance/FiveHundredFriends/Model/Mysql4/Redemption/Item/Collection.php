<?php

/**
* Redeem Item collection
*/
class Alliance_FiveHundredFriends_Model_Mysql4_Redemption_Item_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	/**
	* Initialize resource model
	*
	*/
	protected function _construct()
	{
		$this->_init('alliance_fivehundredfriends/redemption_item');
	}
}

