<?php

class Alliance_FiveHundredFriends_Model_Redemption_System_Backend_Relations extends Mage_Core_Model_Config_Data
{
	protected function _afterLoad()
	{
		if(! is_array($this->getValue())) {
			$value = $this->getValue();
			$this->setValue(empty($value) ? false : unserialize($value));
		}
	}
	
	// Save data serialized.
	protected function _beforeSave()
	{
		$this->setValue(serialize($this->getValue()));
	}
}