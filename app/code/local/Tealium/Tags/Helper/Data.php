<?php

class Tealium_Tags_Helper_Data extends Mage_Core_Helper_Abstract {
	
	private $tealium;
	
	public function init($store, $page = array()) {
        require_once(Mage::getBaseDir('lib') . '/Tealium/Tealium.php');
		$account = $this->getAccount($store);
        $profile = $this->getProfile($store);
        $env = $this->getEnv($store);
		
		$data = array("store" => $store, "page" => $page);
		$this->tealium = new Tealium($account,$profile,$env,"Home",$data);
		if (Mage::getStoreConfig('tealium_tags/general/udo_enable', $store)){
			$udoElements = array();
			@include_once(Mage::getStoreConfig('tealium_tags/general/udo', $store));
			foreach ($udoElements as $page => $vars){
				if (is_array($vars)){
					foreach ($vars as $pageKey => $pageValue){
						$this->tealium->updateUdo($pageKey, $pageValue, $page);
					}
				}
			}
		}
		$this->tealium->pageType("Home");
		
    }
	
    public function isEnabled($store) {
        return Mage::getStoreConfig('tealium_tags/general/enable', $store);
    }
	
	public function enableOnePageCheckout($store) {
		return Mage::getStoreConfig('tealium_tags/general/onepage', $store);
	}
	
	public function externalUdoEnabled($store) {
        return Mage::getStoreConfig('tealium_tags/general/udo_enable', $store);
    }

    function getTealiumBaseUrl($store){
        $account = $this->getAccount($store);
        $profile = $this->getProfile($store);
        $env = $this->getEnv($store);
        return "//tags.tiqcdn.com/utag/$account/$profile/$env/utag.js";
    }
	
	function getTealiumObject($store, $page = array()){
		$this->init($store, $page);
        return $this->tealium;
    }

    public function getAccount($store) {
        return Mage::getStoreConfig('tealium_tags/general/account', $store);
    }

    public function getProfile($store) {
        return Mage::getStoreConfig('tealium_tags/general/profile', $store);
    }
	
    public function getEnv($store) {
        return Mage::getStoreConfig('tealium_tags/general/env', $store);
    }
	
	public function getUDOPath($store) {
        return Mage::getStoreConfig('tealium_tags/general/udo', $store);
    }

	public function getDiagnosticTag($store) {
		if (Mage::getStoreConfig('tealium_tags/general/diagnostic_enable', $store)){
			$utag_data= urlencode($this->tealium->render("json"));
			$url = Mage::getStoreConfig('tealium_tags/general/diagnostic_tag', $store) . '?origin=server&user_agent='.$_SERVER['HTTP_USER_AGENT'].'&data='.$utag_data;
			return '<img src="' . $url . '" style="display:none"/>';
		}
		return "";
    }
}
	 