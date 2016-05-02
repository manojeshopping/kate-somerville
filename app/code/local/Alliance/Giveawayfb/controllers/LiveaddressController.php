<?php

/**
 * Class Alliance_Giveawayfb_AjaxController
 */
class Alliance_Giveawayfb_LiveaddressController extends Mage_Core_Controller_Front_Action
{
    /**
     * An intermediary designed to accept AJAX GET requests, forward the request to the LiveAddress API,
     * and catch/return the response in the form of a JSON-encoded array. This is necessary because API requests can't
     * be made easily from the client
     */
    public function streetAddressAction()
    {
        $helper = Mage::helper('giveawayfb/liveaddress');

        $street = $this->getRequest()->getParam('street');
        $city = $this->getRequest()->getParam('city');
        $state = $this->getRequest()->getParam('state');

        header('Content-Type: application/json');
        echo $helper->streetAddress($street, $city, $state);
    }
}