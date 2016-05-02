<?php

class Alliance_Bronto_IndexController extends Mage_Core_Controller_Front_Action
{
    
    public function unsubscribeAction()
    {
		$this->loadLayout();    
		$this->renderLayout();
    }
	
	public function thankyouAction()
    {
		$email = urldecode($this->getRequest()->getParam('email'));
		
		if(!$email) {
			$this->_redirect('bronto/index/unsubscribe');
		}
		$this->loadLayout();    
		$this->renderLayout();
    }
}