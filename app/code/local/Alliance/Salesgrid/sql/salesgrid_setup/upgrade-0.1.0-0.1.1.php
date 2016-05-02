<?php
$installer = $this;
$installer->startSetup();
$installer->run("

ALTER TABLE `{$installer->getTable('sales_flat_order_grid')}` ADD COLUMN customer_group_id varchar(255);

UPDATE `{$installer->getTable('sales_flat_order_grid')}` OG SET OG.customer_group_id = 
(SELECT customer_group_id FROM `{$installer->getTable('sales_flat_order')}` WHERE entity_id = OG.entity_id);

");

$installer->endSetup();