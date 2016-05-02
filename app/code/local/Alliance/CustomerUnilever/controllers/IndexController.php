<?php
/**
 * Class Alliance_CustomerUnilever_CustomerController
 */
class Alliance_CustomerUnilever_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
	    //$this->loadLayout();
        //$this->renderLayout();
		$getValues = $this->getRequest()->getParams();
		if($getValues){
			if(isset($getValues['p'])){	
				$email_encode = $getValues['p'];
				$email_decode = base64_decode($email_encode);
				$pos =  strpos(strtolower($email_decode), $this->_getHelper()->getSearchString());
	
				if($pos !== false){
					$customer = Mage::getModel("customer/customer"); 
					$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
					$customer = $customer->loadByEmail($email_decode);
			
					if($customer){			
						if($this->_getHelper()->getCustomerGroupName($customer) != $this->_getHelper()->getUnileverCustomerGroup() ) {
							$customer->setGroupId($this->_getHelper()->getUnileverCustomerGroupId());
							$customer->save();
							Mage::log('indexAction::Saved Customer Confirmed ' . $email_decode, null, 'customerunilever.log');
							Mage::getSingleton('customer/session')->addSuccess('Customer account has been verified successfully.');							
						}else{
							Mage::getSingleton('customer/session')->addError('Customer account was verified already.');
						}
					}else{
						Mage::getSingleton('customer/session')->addError('Customer account not found.'); 
					}
				}else{
					Mage::getSingleton('customer/session')->addError('Customer account not found.'); 
				}	
			}else{
				Mage::getSingleton('customer/session')->addError('Customer account not found.'); 
			}
		}else{
			Mage::getSingleton('customer/session')->addError('Error'); 
		}
		 session_write_close();	
		 Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl()."/customer/account/login/");
	}
	
	
	protected function _getHelper()
	{	
		return Mage::helper('customerunilever');
	}
	
}	