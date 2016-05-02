<?php
	$installer	=	$this;
	$installer->startSetup();
	$installer->getTable('sales/quote');
	$installer->getTable('sales/order');
	
	$installer->run("
		ALTER TABLE `".$installer->getTable('sales/quote_item')."` ADD ".Pixafy_Ordergroove_Helper_Constants::ORDER_COLUMN_BASE_OG_IOI_ITEM_DISCOUNT." DECIMAL(12,4);
		ALTER TABLE `".$installer->getTable('sales/order_item')."` ADD ".Pixafy_Ordergroove_Helper_Constants::ORDER_COLUMN_BASE_OG_IOI_ITEM_DISCOUNT." DECIMAL(12,4);
		ALTER TABLE `".$installer->getTable('sales/quote_item')."` ADD ".Pixafy_Ordergroove_Helper_Constants::ORDER_COLUMN_OG_IOI_ITEM_DISCOUNT." DECIMAL(12,4);
		ALTER TABLE `".$installer->getTable('sales/order_item')."` ADD ".Pixafy_Ordergroove_Helper_Constants::ORDER_COLUMN_OG_IOI_ITEM_DISCOUNT." DECIMAL(12,4);
		ALTER TABLE `".$installer->getTable('sales/quote')."` ADD ".Pixafy_Ordergroove_Helper_Constants::ORDER_COLUMN_BASE_OG_IOI_ORDER_DISCOUNT." DECIMAL(12,4);
		ALTER TABLE `".$installer->getTable('sales/order')."` ADD ".Pixafy_Ordergroove_Helper_Constants::ORDER_COLUMN_BASE_OG_IOI_ORDER_DISCOUNT." DECIMAL(12,4);
		ALTER TABLE `".$installer->getTable('sales/quote')."` ADD ".Pixafy_Ordergroove_Helper_Constants::ORDER_COLUMN_OG_IOI_ORDER_DISCOUNT." DECIMAL(12,4);
		ALTER TABLE `".$installer->getTable('sales/order')."` ADD ".Pixafy_Ordergroove_Helper_Constants::ORDER_COLUMN_OG_IOI_ORDER_DISCOUNT." DECIMAL(12,4);
	"
	);
	$installer->endSetup();