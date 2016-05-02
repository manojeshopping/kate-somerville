<?php

class Alliance_FiveHundredFriends_Block_Redemption_System_Relations extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		if(! $this->getTemplate()) {
			$this->setTemplate('alliance/fivehundredfriends/relations.phtml');
		}
		return $this;
	}
	
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		$this->addData(array(
			'rewards' => $this->_getRewards($element)
		));
		
		return $this->_toHtml();
	}
	
	protected function _getName($originalName, $elementType, $id)
	{
		$finalName = $originalName.'['.$elementType.']['.$id.']';
		return $finalName;
	}
	protected function _getValue($value, $elementType, $id)
	{
		return $value[$elementType][$id];
	}
	
	protected function _getRewards(Varien_Data_Form_Element_Abstract $element)
	{
		$apiHelper = $this->_getFivehundredApi();
		$rewardsResponse = $apiHelper->getRewards();
		
		if(! $rewardsResponse['success']) return false;
		$rewards = $rewardsResponse['data'];
		$originalName = $element->getName();
		$elementValues = $element->getValue();
		
		foreach($rewards as $_rewardKey => $_rewardData) {
			$rewards[$_rewardKey]['element_type'] = array(
				'name' => $this->_getName($originalName, 'type', $_rewardData['id']),
				'value' => $this->_getValue($elementValues, 'type', $_rewardData['id']),
			);
			$rewards[$_rewardKey]['element_value'] = array(
				'name' => $this->_getName($originalName, 'value', $_rewardData['id']),
				'value' => $this->_getValue($elementValues, 'value', $_rewardData['id']),
			);
		}
		
		return $rewards;
	}
	protected function _getFivehundredApi()
	{
		return Mage::helper('alliance_fivehundredfriends/api');
	}
}
