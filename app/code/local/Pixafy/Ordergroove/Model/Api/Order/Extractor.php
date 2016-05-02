<?php
/**
 * Extractor class that parses the XML data at its highest level
 * 
 * @package     Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 * 
 * @method extract(array $data)
 * @method extractCustomer(stdClass Object $data)
 * @method extractOgOrderId(stdClass Object $data)
 */
class Pixafy_Ordergroove_Model_Api_Order_Extractor extends Varien_Object{
	/**
	 * Extract relevant data from the POST to a friendly PHP format
	 * 
	 * @param array $data
	 * @return stdClass Object
	 */
	public function extract($data){
		return json_decode(json_encode(simplexml_load_string($data['xml'])));
	}
	
	/**
	 * Extract the Magento customer object from the customer id in the XML
	 * 
	 * @param stdClass Object $data
	 * @return Mage_Customer_Model_Customer
	 */
	public function extractCustomer($data){
		$customer	=	Mage::getModel('customer/customer');
		$customerId	=	'';
		
		/**
		 * Validation check to ensure Magento
		 * does not log a system error for
		 * blank values
		 */
		if(json_encode($data->customer->customerPartnerId) != '{}'){
			$customerId	=	(string)$data->customer->customerPartnerId;
		}
		
		if(is_numeric($customerId)){
			$customer->load($customerId);
		}
		return $customer;
	}
	
	/**
	 * Extract the Ordergroove order id
	 * 
	 * @param stdClass Object $data
	 * @return string
	 */
	public function extractOgOrderId($data){
		return (string)$data->head->orderOgId;
	}
	
	/**
	 * Extract customer email address from
	 * the XML
	 * 
	 * @param stdClass Object $data
	 * @return string
	 */
	public function extractCustomerEmail($data){
		return (string)$data->customer->customerEmail;
	}
}
?>
