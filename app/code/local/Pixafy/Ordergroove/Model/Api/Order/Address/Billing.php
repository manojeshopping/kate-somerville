<?php
/**
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 * 
 * @method __construct()
 * @method extract()
 * 
 * Order API billing address class.
 * Extract the billing address data
 * from the provided XML.
 */
class Pixafy_Ordergroove_Model_Api_Order_Address_Billing extends Pixafy_Ordergroove_Model_Api_Order_Address{
	
	/**
	 * XML keys for specific billing address data
	 */
	const FIELD_CUSTOMER_FIRST_NAME	=	'customerBillingFirstName';
	const FIELD_CUSTOMER_LAST_NAME	=	'customerBillingLastName';
	const FIELD_CUSTOMER_CITY		=	'customerBillingCity';
	const FIELD_CUSTOMER_REGION		=	'customerBillingState';
	const FIELD_CUSTOMER_POSTCODE	=	'customerBillingZip';
	const FIELD_CUSTOMER_PHONE		=	'customerBillingPhone';
	const FIELD_CUSTOMER_FAX		=	'customerBillingFax';
	const FIELD_CUSTOMER_COMPANY	=	'customerBillingCompany';
	const FIELD_CUSTOMER_COUNTRY	=	'customerBillingCountry';
	const FIELD_CUSTOMER_STREET_ONE	=	'customerBillingAddress1';
	const FIELD_CUSTOMER_STREET_TWO	=	'customerBillingAddress2';
	
	/**
	 * Set the data object to the customer section
	 * so that the billing address can be extracted
	 * through the helper functions
	 */
	public function __construct(){
		$this->data	=	$this->getFeedData();
		$this->data	=	$this->data->customer;
	}
	
	/**
	 * Extract billing address data and create the billing address object
	 * 
	 * @return Mage_Sales_Model_Quote_Address
	 */
	public function extract(){
		$billingAddress	=	Mage::getModel('sales/quote_address');
		$billingAddress->setFirstname($this->extractField(self::FIELD_CUSTOMER_FIRST_NAME));
		$billingAddress->setLastname($this->extractField(self::FIELD_CUSTOMER_LAST_NAME));
		$billingAddress->setCompany($this->extractField(self::FIELD_CUSTOMER_COMPANY));
		$billingAddress->setStreet($this->extractStreet(self::FIELD_CUSTOMER_STREET_ONE, self::FIELD_CUSTOMER_STREET_TWO));
		$billingAddress->setCity($this->extractField(self::FIELD_CUSTOMER_CITY));
		$billingAddress->setCountryId($this->extractField(self::FIELD_CUSTOMER_COUNTRY));
		$billingAddress->setRegion($this->extractRegion(self::FIELD_CUSTOMER_REGION, $billingAddress->getCountryId()));
		$billingAddress->setRegionId($this->extractRegionId(self::FIELD_CUSTOMER_REGION, $billingAddress->getCountryId()));
		$billingAddress->setPostcode($this->extractField(self::FIELD_CUSTOMER_POSTCODE));
		
		//$billingAddress->setPostcode('46282');	//force generic transaction declined in test mode
		
		
		$billingAddress->setTelephone($this->extractField(self::FIELD_CUSTOMER_PHONE));
		$billingAddress->setFax($this->extractField(self::FIELD_CUSTOMER_FAX));
		return $billingAddress;
	}
}
?>
