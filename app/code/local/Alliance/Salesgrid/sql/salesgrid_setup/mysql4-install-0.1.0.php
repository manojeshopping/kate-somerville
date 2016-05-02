<?php
$installer = $this;
$installer->startSetup();
$installer->run("

ALTER TABLE `{$installer->getTable('sales_flat_order_grid')}` ADD COLUMN customer_email varchar(255);

UPDATE `{$installer->getTable('sales_flat_order_grid')}` OG SET OG.customer_email = 
(SELECT customer_email FROM `{$installer->getTable('sales_flat_order')}` WHERE entity_id = OG.entity_id);

");

$installer->endSetup();
