<?php

class Alliance_Offers_Helper_Data extends Mage_Core_Helper_Abstract
{
	private $_moduleConfig;
	private $_complementaryModules;
	
	
	public function checkModule()
	{
		$config = $this->_getModuleConfig();
		return $config['enabled'];
	}
	
	public function getComplentaryModules()
	{
		if(empty($this->_complementaryModules)) {
			$extraModules = Mage::getStoreConfig('alliance_offers');
			
			$this->_complementaryModules = array();
			foreach($extraModules as $_module => $data) {
				$_module = str_replace('_configuration', '', $_module);
				if($_module != "offers") {
					$this->_complementaryModules[$data['order'].'-'.$_module] = $_module;
				}
			}
		}
		// Sort array.
		ksort($this->_complementaryModules);
		
		return $this->_complementaryModules;
	}
	
	public function checkComplentaryModules()
	{
		$checked = false;
		$modules = $this->getComplentaryModules();
		foreach($modules as $_module) {
			$model = Mage::getModel($_module.'/'.$_module);
			if(method_exists($model, 'checkOffer') && $model->checkOffer()) {
				$checked = true;
			}
		}
		
		return $checked;
	}
	
	
	public function getModuleEnabled()
	{
		$config = $this->_getModuleConfig();
		return $config['enabled'];
	}
	
	private function _getModuleConfig()
	{
		if(empty($this->_moduleConfig)) {
			$this->_moduleConfig = array(
				'enabled' => Mage::getStoreConfig('alliance_offers/offers_configuration/enabled'),
			);
		}
		
		return $this->_moduleConfig;
	}
}

