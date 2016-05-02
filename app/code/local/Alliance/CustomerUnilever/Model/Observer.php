<?php
class Alliance_CustomerUnilever_Model_Observer {

	public function customerRegisterSuccess($observer)
	{
		Mage::log('init customerRegisterSuccess', null, 'customerunilever.log');  
		$customer	= $observer->getEvent()->getCustomer();
		$customer_email = $customer->getEmail();
		$customer_name 	= $customer->getName();
		
		Mage::log('customerRegisterSuccess::EMAIL ' . $customer_email, null, 'customerunilever.log'); 
		Mage::log('customerRegisterSuccess::NAME ' . $customer_name, null, 'customerunilever.log'); 
		
			$pos =  strpos(strtolower($customer_email), $this->_getHelper()->getSearchString());
			
			if($pos !== false){
				$confirmlink = $this->_getConfirmLink($customer_email);
				$sessionData['confirmlink'] = $confirmlink;
				Mage::log('customerRegisterSuccess::CONFIRMLINK ' . $confirmlink, null, 'customerunilever.log'); 
				$EmailTemplate = $this->_getHelper()->getConfirmationEmailTemplate();
				$EmailTemplate->send($customer_email, $customer_name, $sessionData);
			}
	}
	
	protected function _getHelper()
	{	
		return Mage::helper('customerunilever');
	}
	
	protected function _getConfirmLink($email)
	{
		$email_encode = base64_encode($email);
		$module_name = $this->_getHelper()->getModuleName();
		$_url = Mage::getBaseUrl() . $module_name . "/?p=" . $email_encode ;
		
		return $_url;
	}
}