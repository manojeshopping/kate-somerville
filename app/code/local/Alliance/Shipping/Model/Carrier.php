<?php

/**
 * Class Alliance_Shipping_Model_Carrier
 */
class Alliance_Shipping_Model_Carrier
	extends Mage_Shipping_Model_Carrier_Abstract
	implements Mage_Shipping_Model_Carrier_Interface
{
	/**
	 * Carrier's code, as defined in parent class
	 *
	 * @var string
	 */
	protected $_code = 'alliance_shipping';

	/**
	 * Returns available shipping rates for Alliance Shipping carrier
	 *
	 * @param Mage_Shipping_Model_Rate_Request $request
	 * @return Mage_Shipping_Model_Rate_Result
	 */
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		/** @var Mage_Shipping_Model_Rate_Result $result */
		$result = Mage::getModel('shipping/rate_result');

		$helper = Mage::helper('alliance_shipping');
		$enabled_methods = explode(',', Mage::getStoreConfig('carriers/alliance_shipping/allowed_methods'));
		$discount_minimum = Mage::getStoreConfig('carriers/alliance_shipping/discount_minimum');

		if (count($enabled_methods) && $discount_minimum != NULL && $discount_minimum !== '') {
			for ($i = 1; $i < 16; $i++) {
				$method_name = Mage::getStoreConfig('carriers/alliance_shipping/method_'.$i.'_name');
				$method_slug = $helper->convertToSlug($method_name);
				$method_price = Mage::getStoreConfig('carriers/alliance_shipping/method_'.$i.'_price');
				$method_discount_price = Mage::getStoreConfig('carriers/alliance_shipping/method_'.$i.'_discount_price');

				if ($method_name != NULL && $method_name !== ''
					&& $method_slug != NULL && $method_slug !== ''
					&& $method_price != NULL && $method_price !== ''
					&& $method_discount_price != NULL && $method_discount_price !== ''
					&& in_array($method_slug, $enabled_methods)) {

					$rate = Mage::getModel('shipping/rate_result_method');
					$subtotal = $request->getBaseSubtotalInclTax();
					$rate->setCarrier($this->_code);
					$rate->setMethod($method_slug);
					$rate->setMethodTitle($method_name);
					if ($subtotal >= intval($discount_minimum)) {
						$rate->setPrice($method_discount_price);
					}
					else {
						$rate->setPrice($method_price);
					}
					$rate->setCost(0);
					$result->append($rate);
				}
			}
		}
		return $result;
	}

	/**
	 * Returns allowed shipping methods
	 *
	 * @return array
	 */
	public function getAllowedMethods()
	{
		return Mage::helper('alliance_shipping')->getAllowedMethods();
	}

	/**
	 * Returns minimum cart subtotal for discount as configured in System > Configuration > Shipping Methods
	 *
	 * @return mixed
	 */
	protected function _getDiscountMinimum()
	{
		return Mage::getStoreConfig('carriers/alliance_shipping/discount_minimum');
	}
}