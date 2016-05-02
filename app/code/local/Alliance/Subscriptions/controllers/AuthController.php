<?php
/**
 * Class Alliance_Subscriptions_AuthController
 */
class Alliance_Subscriptions_AuthController extends Mage_Core_Controller_Front_Action
{

    public function preDispatch()
    {
        parent::preDispatch();
		$hash_key = Mage::helper('ordergroove/config')->getHashKey();
		$seconds_since_epoch = time();
		$merchant_user_id = Mage::getSingleton('customer/session')->getCustomer()->getId();  //This variable is going to be blank if you are a  guest
		//$signature = hash_hmac( 'sha256', $merchant_user_id."|".$seconds_since_epoch,  $hash_key);
		$signature = hash_hmac( 'sha256', $merchant_user_id."|".$seconds_since_epoch,  $hash_key, true);
		$signature = base64_encode($signature);
		$cookie_val = $merchant_user_id."|".$seconds_since_epoch."|".$signature;
		$cookie_val = strtr($cookie_val, array_combine(str_split($tmp=",; \t\r\n\013\014"), array_map('rawurlencode', str_split($tmp))));
		
		if(!empty($merchant_user_id)){
		setrawcookie("og_auth" , $cookie_val, time() + (60 * 60 * 2), "/", Mage::getModel('core/cookie')->getDomain(), true);
		}
	}

    /**
     * Mage::getUrl('/subscriptions/auth')
     *
     * Main Ordergroove_Customer page
     */
    public function indexAction()
    {
		$content = "<!DOCTYPE html>
		<html>
		<head>
		<script type='text/javascript' src='".Mage::helper('ordergroove/config')->getPageTaggingUrl().Mage::helper('ordergroove/config')->getMerchantId()."/auth.js'></script>
		</head>
		<body></body>
		</html>";
	echo $content;
	}
}



