<?php
/**
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 * 
 * @method __construct()
 * @method extract()
 * 
 * Order API shipping address class.
 * Extract the shipping address data
 * from the provided XML.
 */
class Pixafy_Ordergroove_Model_Api_Order_Address_Shipping extends Pixafy_Ordergroove_Model_Api_Order_Address{
	
	/**
	 * XML keys for specific shipping address data
	 */
	const FIELD_CUSTOMER_FIRST_NAME	=	'customerShippingFirstName';
	const FIELD_CUSTOMER_LAST_NAME	=	'customerShippingLastName';
	const FIELD_CUSTOMER_CITY		=	'customerShippingCity';
	const FIELD_CUSTOMER_REGION		=	'customerShippingState';
	const FIELD_CUSTOMER_POSTCODE	=	'customerShippingZip';
	const FIELD_CUSTOMER_PHONE		=	'customerShippingPhone';
	const FIELD_CUSTOMER_FAX		=	'customerShippingFax';
	const FIELD_CUSTOMER_COMPANY	=	'customerShippingCompany';
	const FIELD_CUSTOMER_COUNTRY	=	'customerShippingCountry';
	const FIELD_CUSTOMER_STREET_ONE	=	'customerShippingAddress1';
	const FIELD_CUSTOMER_STREET_TWO	=	'customerShippingAddress2';
	
	/**
	 * Set the data object to the customer section
	 * so that the shipping address can be extracted
	 * through the helper functions
	 */
	public function __construct(){
		$this->data	=	$this->getFeedData();
		$this->data	=	$this->data->customer;
	}
	
	/**
	 * Extract shipping address data and create the shipping address object
	 * 
	 * @return Mage_Sales_Model_Quote_Address
	 */
	public function extract(){
		$shippingAddress	=	Mage::getModel('sales/quote_address');
		$shippingAddress->setFirstname($this->extractField(self::FIELD_CUSTOMER_FIRST_NAME));
		$shippingAddress->setLastname($this->extractField(self::FIELD_CUSTOMER_LAST_NAME));
		$shippingAddress->setCompany($this->extractField(self::FIELD_CUSTOMER_COMPANY));
		$shippingAddress->setStreet($this->extractStreet(self::FIELD_CUSTOMER_STREET_ONE, self::FIELD_CUSTOMER_STREET_TWO));
		$shippingAddress->setCity($this->extractField(self::FIELD_CUSTOMER_CITY));
		$shippingAddress->setCountryId($this->extractField(self::FIELD_CUSTOMER_COUNTRY));
		$shippingAddress->setRegion($this->extractRegion(self::FIELD_CUSTOMER_REGION, $shippingAddress->getCountryId()));
		$shippingAddress->setRegionId($this->extractRegionId(self::FIELD_CUSTOMER_REGION, $shippingAddress->getCountryId()));
		$shippingAddress->setPostcode($this->extractField(self::FIELD_CUSTOMER_POSTCODE));
		$shippingAddress->setTelephone($this->extractField(self::FIELD_CUSTOMER_PHONE));
		$shippingAddress->setFax($this->extractField(self::FIELD_CUSTOMER_FAX));
		return $shippingAddress;
	}
}
?>
