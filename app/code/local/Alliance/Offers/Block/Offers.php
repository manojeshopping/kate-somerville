<?php

class Alliance_Offers_Block_Offers extends Mage_Core_Block_Template
{

	public function getConfigEnabled()
	{
		return $this->_getHelper()->getModuleEnabled();
	}
	
	public function getActionPostUrl()
	{
		$url = $this->getUrl('offers/index/addOffers');
		return $url;
	}
	
	public function getModules() {
		$allModules = $this->_getHelper()->getComplentaryModules();
		
		$modules = array();
		foreach($allModules as $_module) {
			$model = Mage::getModel($_module.'/'.$_module);
			if(method_exists($model, 'checkOffer') && $model->checkOffer()) {
				$modules[] = $_module;
			}
		}
		return $modules;
	}
	
	
	private function _getHelper()
	{
		return Mage::helper('offers');
	}
}

