<?php
class Alliance_Offers_Model_Observer {
	
	public function checkOffersPage($observer)
	{
		Mage::log("checkOffersPage: ".$observer->getEvent()->getName().".", null, 'alliance_offers.log');
		
		// Initiate helper.
		$helper = $this->_getHelper();
		
		// Check if module if enable.
		if(! $helper->checkModule()) {
			Mage::log("checkOffersPage: Module disbabled.", null, 'alliance_offers.log');
			return true;
		}
		
		// Check for complementary modules.
		if(! $helper->checkComplentaryModules()) {
			Mage::log("checkOffersPage: Complementary Modules disbabled.", null, 'alliance_offers.log');
			return true;
		}
		
		
		// Redirect.
		$redirect = Mage::getUrl('offers');
		Mage::log("checkGiftOfChoice: redirect: ".$redirect.".", null, 'alliance_offers.log');
		Mage::app()->getResponse()->setRedirect($redirect);
		return true;
	}
	
	public function checkCart($observer)
	{
		// Mage::log("checkCart: ".$observer->getEvent()->getName().".", null, 'alliance_offers.log');
		
		// Initiate helper.
		$helper = $this->_getHelper();
		
		// Check if module if enable.
		if(! $helper->checkModule()) {
			Mage::log("checkOffersPage: Module disbabled.", null, 'alliance_offers.log');
			return true;
		}
		
		// Check all add-on modules.
		$modules = $helper->getComplentaryModules();
		foreach($modules as $_module) {
			// Mage::log($_module.'/'.$_module, null, 'alliance_offers.log');
			$model = Mage::getModel($_module.'/'.$_module);
			// Mage::log("model: ".get_class($model).".", null, 'alliance_offers.log');
			if(method_exists($model, 'checkOfferInCart')) {
				// Mage::log("checkOfferInCart.", null, 'alliance_offers.log');
				$model->checkOfferInCart();
			}
		}
		
		// Mage::log("checkCart: finished.", null, 'alliance_offers.log');
		return true;
	}
	

	private function _getHelper()
	{
		return Mage::helper('offers');
	}
}
