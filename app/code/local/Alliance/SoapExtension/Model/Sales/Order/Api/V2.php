<?php

/**
 * Class Alliance_SoapExtension_Model_Sales_Order_Api_V2
 */
class Alliance_SoapExtension_Model_Sales_Order_Api_V2 extends Mage_Sales_Model_Order_Api_V2
{

    /**
     * Appends additional information to the salesOrderPayment entity if that information exists
     *
     * @param string $orderIncrementId
     * @return array
     */
    public function info($order_increment_id)
    {
        $result = parent::info($order_increment_id);

        if (!isset($result['payment']['cc_type'])) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($order_increment_id);

            if ($order->getId()) {

                $additional_info = $order->getPayment()->getAdditionalInformation();
                if (array_key_exists('authorize_cards', $additional_info)) {
                    foreach ($additional_info['authorize_cards'] as $auth_card) {
                        $cc_type_abbrev = $auth_card['cc_type'];

                        $types   = Mage::getSingleton('payment/config')->getCcTypes();
                        $cc_type = $types[$cc_type_abbrev];

                        break;
                    }
                }
            }

            $result['payment']['cc_type'] = isset($cc_type) && !is_null($cc_type) ? $cc_type : 'N/A';
        }

        return $result;
    }
}