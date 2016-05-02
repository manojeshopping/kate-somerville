<?php

/**
 * Class Alliance_PromoLink_Model_Observer
 */
class Alliance_PromoLink_Model_Observer
{

    public function applyEmptyPromo($observer)
    {
        $promo = Mage::getSingleton('customer/session')->getEmptyPromo();

        if ($promo) {
            $cart = Mage::getSingleton('checkout/cart');
            $cart->getQuote()->setCouponCode($promo)->collectTotals();
            $cart->save();

            Mage::getSingleton('customer/session')->setEmptyPromo('');
        }
    }

}
