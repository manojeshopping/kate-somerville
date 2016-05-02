<?php

/**
 * Class Alliance_PromoLink_CartController
 */
class Alliance_PromoLink_CartController extends Mage_Core_Controller_Front_Action
{

    public function addAction()
    {
        $params = $this->getRequest()->getParams();

        $skus = explode(',', $params['skus']);
        $promo = $params['promo'];

        if (!$promo && is_null($params['skus'])) {
            $this->_redirect('/');

            return;
        }

        if ($promo && is_null($params['skus'])) {
            $this->_emptyPromo($promo);

            $this->_redirect('/');

            return;
        }

        $cart = Mage::getSingleton('checkout/cart');
        $cart = $this->_clearCart($cart);

        // Add products
        $unavailable = array();
        foreach ($skus as $sku) {
            if (!$this->_addToCart($sku, $cart)) $unavailable[] = $sku;
        }
        $unavailable = array_unique($unavailable);

        // Add promo code
        if ($promo) $cart->getQuote()->setCouponCode($promo)->collectTotals();

        $cart->save();

        $messages = array();
        // Handle product availability messaging
        if (count($unavailable) > 0)
            $messages[] = Mage::getSingleton('core/message')->error('Sorry, the following products were unavailable: ' . implode(', ', $unavailable));
        else $messages[] = Mage::getSingleton('core/message')->success('The requested product(s) have been added to your cart.');

        // Handle promo code messaging
        if ($promo && $cart->getQuote()->getCouponCode())
            $messages[] = Mage::getSingleton('core/message')->success('Coupon code "' . $promo . '" has been applied!');
        elseif ($promo) $messages[] = Mage::getSingleton('core/message')->error('Sorry, that coupon code is unavailable.');

        Mage::getSingleton('customer/session')->addUniqueMessages($messages);

        $this->_redirect('checkout/cart');
    }

    protected function _emptyPromo($promo)
    {
        Mage::getSingleton('customer/session')->setEmptyPromo($promo);

        $cart = Mage::getSingleton('checkout/cart');
        $cart = $this->_clearCart($cart);
        $cart->save();

        $messages = array();
        $messages[] = $this->_promoIsValid($promo)
            ? Mage::getSingleton('core/message')->success('Coupon code "' . $promo . '" has been applied! Continue shopping and you\'ll see your discount at checkout.')
            : Mage::getSingleton('core/message')->error('Sorry, that promo code is no longer valid.');

        Mage::getSingleton('customer/session')->addUniqueMessages($messages);
    }

    protected function _promoIsValid($promo)
    {
        $coupon = Mage::getModel('salesrule/coupon')->load($promo, 'code');
        $salesrule = Mage::getModel('salesrule/rule')->load($coupon->getRuleId());

        $from_date = $salesrule->getFromDate();
        $to_date = $salesrule->getToDate();
        $gmt_date = Mage::getModel('core/date')->gmtDate();

        return (bool) $salesrule->getIsActive() && (!$from_date || $from_date < $gmt_date) && (!$to_date || $to_date > $gmt_date);
    }

    protected function _clearCart($cart)
    {
        foreach ($cart->getItems() as $item) {
            $item_id = $item->getItemId();
            $cart->removeItem($item_id);
        }

        $cart->getQuote()->setCouponCode('');

        return $cart;
    }

    protected function _addToCart($sku, $cart)
    {
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

        if ($product && $product->getPrice() > 0 && $product->isSalable()) {
            $cart->addProduct($product->getId());
            if ($cart->getCheckoutSession()->getLastAddedProductId() == $product->getId())
                return true;
        }

        return false;
    }

}
