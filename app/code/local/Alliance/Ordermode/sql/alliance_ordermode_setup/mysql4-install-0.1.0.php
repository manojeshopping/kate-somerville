<?php
/**
 * Setup scripts, add new column and fulfills
 * its values to existing rows
 *
 */
/* @var $this Mage_Sales_Model_Mysql4_Setup */

$installer = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup('sales_setup');

/* @var $installer  */
// $installer = $this;

/* Custom Module create a new column in sale/order table and also create new attribute in the 
 * sales order module
 */
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'order_mode', " varchar(15) DEFAULT 'Website' ");

$installer->addAttribute('order', 'order_mode', array(
    'type' => 'varchar',
    'label' => 'Order Mode',
    'input' => 'text',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'required' => false,
    'default' => 'Website'
));
$installer->endSetup();

$this->startSetup();


// Add column to grid table
$this->getConnection()->addColumn(
    $this->getTable('sales/order_grid'),
    'order_mode',
    "varchar(255) not null default 'Website'"
);
// Add key to table for this field,
// it will improve the speed of searching & sorting by the field
$this->getConnection()->addKey(
    $this->getTable('sales/order_grid'),
    'order_mode',
    'order_mode'
);

$this->endSetup();
