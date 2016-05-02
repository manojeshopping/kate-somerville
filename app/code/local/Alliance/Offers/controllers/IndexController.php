<?php
class Alliance_Offers_IndexController extends Mage_Core_Controller_Front_Action
{

	public function indexAction()
	{
		// Check Module availability.
		if(! $this->_getHelper()->checkComplentaryModules()) {
			$redirect = Mage::getUrl('checkout/cart');
			$this->getResponse()->setRedirect($redirect);
			return;
		}
		
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function addOffersAction()
	{
		$added = true;
		$modules = $this->_getHelper()->getComplentaryModules();
		foreach($modules as $_module) {
			$model = Mage::getModel($_module.'/'.$_module);
			$request = $this->getRequest()->getPost($_module);
			if(method_exists($model, 'addOffer') && ! $model->addOffer($request)) {
				$added = false;
			}
		}
		
		// If all products were added successfully, then, redirect to checkout, otherwise, redirect to cart.
		if($added) {
			$redirect = Mage::helper('checkout/url')->getCheckoutUrl();
		} else {
			$redirect = Mage::getUrl('checkout/cart');
		}
		
		$this->getResponse()->setRedirect($redirect);
	}
	
	
	private function _getHelper()
	{
		return Mage::helper('offers');
	}
}

