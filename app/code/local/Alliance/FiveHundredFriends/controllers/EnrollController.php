<?php

/**
 * Class Alliance_FiveHundredFriends_SandboxController
 *
 * Enrollment controller, handles all things enrollment for Magento/FiveHundredFriends API integration
 */
class Alliance_FiveHundredFriends_EnrollController extends Mage_Core_Controller_Front_Action
{
    /**
     * Mage::getUrl('katerewards/enroll/thanks')
     *
     * Thank you page for all customers that enroll, or are already enrolled
     */
    public function thanksAction()
    {
        $this->_redirect('katerewards');
    }

    /**
     * Mage::getUrl('katerewards/enroll/now')
     *
     * If the current customer is logged in, this route will enroll them and redirect them to the thanks page on success
     *
     * If the current customer is not logged in, or the enrollment API request gets dropped or otherwise fails, the
     * customer will be redirected to the main enrollment page
     */
    public function nowAction()
    {
		if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer           = Mage::getSingleton('customer/session')->getCustomer();
            $customer_email     = $customer->getEmail();
            $customer_firstname = $customer->getFirstname();
            $customer_lastname  = $customer->getLastname();

            $api                = Mage::helper('alliance_fivehundredfriends/api');
            $request_parameters = array(
                'email'      => $customer_email,
                'first_name' => $customer_firstname,
                'last_name'  => $customer_lastname,
            );
            $response           = $api->enroll($request_parameters);

            if ($response['success']) {
				// If refere is cart, redirect to cart.
                if(strpos(Mage::helper('core/http')->getHttpReferer(), 'checkout/cart') !== false) {
					Mage::getSingleton('core/session')->setLoadRewardTab(true);
					$this->_redirect('checkout/cart');
				} else {
					$this->_redirect('katerewards/customer/rewards');
				}
                return;
            }
        }

        $this->_redirectReferer();

        return;
    }
}
