<?php
/**
 * Class Alliance_Ordergroove_CustomerController
 */
class Alliance_Subscriptions_CustomerController extends Mage_Core_Controller_Front_Action
{



 protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }


    /**
     * Mage::getUrl('/subscriptions/customer')
     *
     * Main Ordergroove_Customer page
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
	
	/*
	* My account
	*/
	public function subscriptionsAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }


	/*
	* My account
	*/
	public function og_authAction()
    {
	
	$this->isSecure();
	$url = 	Mage::getUrl('/subscriptions/customer/og_auth/', array(
		'_secure' => true,
	));

	echo $url;	

        //$this->loadLayout();
        //$this->renderLayout();
    }
	
	
}