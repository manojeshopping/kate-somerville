<?php
class Alliance_CouponGiftItem_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function checkProductInCart($productId)
	{
		if(empty($productId)) return false;
		
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		if(empty($quote)) return false;
		
		// Mage::log("helper.checkProductInCart: ".$productId, null, 'CouponCodeGift.log');
		$hasProductId = $quote->hasProductId($productId);
		Mage::log("helper.checkProductInCart - hasProductId: ".$hasProductId, null, 'CouponCodeGift.log');
		if($hasProductId) {
			// Force Qty to 1.
			foreach($quote->getAllItems() as $item) {
				if ($item->getProductId() == $productId) {
					$itemQty = $item->getQty();
					Mage::log("helper.checkProductInCart - itemQty: ".$itemQty, null, 'CouponCodeGift.log');
					if($itemQty > 1) {
						$cart = Mage::getSingleton('checkout/cart');
						$cartData = array($item->getId() => array('qty' => 1));
						$cartData = $cart->suggestItemsQty($cartData);
						
						$cart->updateItems($cartData);
						$cart->save();
					}
				}
			}
		}
		
		return $hasProductId;
	}

	public function getGiftSkuByCode($code){
		$coupon_code = $code;
		$coupon = Mage::getModel('salesrule/coupon');
		$coupon->load($coupon_code, 'code');
		$coupon_rule = Mage::getModel('salesrule/rule')->load($coupon->getRuleId());

		$sku = $coupon_rule->getCouponGiftItemSkus();
	
		return $sku;
	}
}