<?php
/**
 * Version 0.0.0.2 upgrade SQL file. Create
 * OrderGroove website, store group, and store
 * view.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
	$installer	=	$this;
	$installer->startSetup();

	$helper		=	Mage::helper('ordergroove/installer');
	$website	=	$helper->createWebsite($helper->WEBSITE_CODE_ORDERGROOVE, $helper->WEBSITE_NAME_ORDERGROOVE);
	if($website->getId()){
		$store	=	$helper->createStore($website->getId(), $helper->STORE_NAME_ORDERGROOVE);
		if($store->getId()){
			$helper->createStoreView($website->getId(), $store->getId(), $helper->STORE_VIEW_CODE_ORDERGROOVE, $helper->STORE_VIEW_NAME_ORDERGROOVE, $helper->LANGUAGE_CODE_ORDERGROOVE, 1);
		}
	}
	$installer->endSetup();

?>
