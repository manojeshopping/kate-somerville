<?php
/**
 * Pixafy Ordergroove module rewrite of the checkout type onepage model.
 * The rewrite is used to check for international billing and shipping
 * addresses, and to not allow a user to proceed if true, and the following
 * conditions are met: og_autoship cookie is set, they are set to default in
 * the System configuration page of the Magento admin.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Model_Rewrite_Checkout_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{	
	/**
	 * Call parent class constructor
	 */
	public function __construct(){
		parent::__construct();
	}

	/**
	 * Save billing address information to quote
	 * This method is called by One Page Checkout JS (AJAX) while saving the billing information.
	 * 
	 * Pixafy overwrite: Call the parent save billing function,
	 * and throw an exception if the country is not a US
	 * address if the og_autoship cookie is set and specified
	 * to be disabled.
	 *
	 * @param   array $data
	 * @param   int $customerAddressId
	 * @return  Mage_Checkout_Model_Type_Onepage
	 */
	public function saveBilling($data, $customerAddressId)
	{
		$result	=	parent::saveBilling($data, $customerAddressId);
		if(!empty($result)){
			return $result;
		}
		
		/**
		 * Check to see if flagged as disabled from admin and cookie is set
		 */
		if(!Mage::helper('ordergroove/config')->functionalityCheckInternationalBillingAddresses()){
			if($data['country_id'] != Pixafy_Ordergroove_Helper_Constants::USA_COUNTRY_ID){
				return array('error' => 1, 'message' => Mage::helper('ordergroove/config')->getInternationalBillingAddressDisabledMessage());
			}
		}
		
		if(array_key_exists('use_for_shipping', $data)){
			if($data['use_for_shipping'] == 1){
				/**
				 * Check to see if flagged as disabled from admin and cookie is set
				 */
				if(!Mage::helper('ordergroove/config')->functionalityCheckInternationalShippingAddresses()){
					if($data['country_id'] != Pixafy_Ordergroove_Helper_Constants::USA_COUNTRY_ID){
						return array('error' => 1, 'message' => Mage::helper('ordergroove/config')->getInternationalShippingAddressDisabledMessage());
					}
				}
			}
		}
		
		return array();
	}
	
	/**
	 * Save checkout shipping address
	 *
	 * 
	 * Pixafy overwrite: Call the parent save shipping function,
	 * and throw an exception if the country is not a US
	 * address if the og_autoship cookie is set and specified
	 * to be disabled.
	 * 
	 * @param   array $data
	 * @param   int $customerAddressId
	 * @return  Mage_Checkout_Model_Type_Onepage
	 */
	public function saveShipping($data, $customerAddressId)
	{
		$result	=	parent::saveShipping($data, $customerAddressId);
		if(!empty($result)){
			return $result;
		}
		
		/**
		 * Check to see if flagged as disabled from admin and cookie is set
		 */
		if(!Mage::helper('ordergroove/config')->functionalityCheckInternationalShippingAddresses()){
			if($data['country_id'] != Pixafy_Ordergroove_Helper_Constants::USA_COUNTRY_ID){
				return array('error' => 1, 'message' => Mage::helper('ordergroove/config')->getInternationalShippingAddressDisabledMessage());
			}
		}
		return array();
	}
}
