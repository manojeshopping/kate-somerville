<?php
class Alliance_Giveawayfb_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		// If user is logged, redirect.
		if(Mage::helper('customer')->isLoggedIn()) {
			$url = Mage::helper('giveawayfb')->getSorryURL();
			$this->_redirectUrl($url);
            return;
		}
		
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__("Discover Hollywood's Best Kept Secret"));
		$this->renderLayout();
	}
	
	
	public function affiliateAction()
	{
		// If user is logged, redirect.
		if(Mage::helper('customer')->isLoggedIn()) {
			$url = Mage::helper('giveawayfb')->getSorryURL();
			$this->_redirectUrl($url);
            return;
		}
		
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__("Discover Hollywood's Best Kept Secret"));
		$this->renderLayout();
	}
}

