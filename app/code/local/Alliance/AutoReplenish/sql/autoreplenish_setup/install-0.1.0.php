<?php
 
$installer = $this;
 
$installer->startSetup();

$installer->run("DROP TABLE IF EXISTS {$installer->getTable('autoreplenish/autoreplenish')}");

$table = $installer->getConnection()
    ->newTable($installer->getTable('autoreplenish/autoreplenish'))
	 ->addColumn('autoreplenish_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'auto_increment' => true,
		'nullable'  => false,
		'primary' => true,
        ), 'AutoReplenish Id')
	->addColumn('og_subscription_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
		), 'Og Subscription Id')
	->addIndex($installer->getIdxName('autoreplenish/autoreplenish', array('og_subscription_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('og_subscription_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Customer Id')
    ->addColumn('customer_email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Customer Email')
    ->addColumn('order_create_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
     	), 'Order Create Date')
	->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Order Id')
	->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Product Id')
	->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'SKU')
	->addColumn('qty', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Quantity')
	->addColumn('frequency', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Frequency')
	->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Status')
	->addColumn('next_order_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Next Order Date')
	->addColumn('loyalty_points', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Loyalty Points');
		
$installer->getConnection()->createTable($table);

$installer->endSetup();