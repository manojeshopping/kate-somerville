<?php
$installer = $this;
$installer->startSetup();
/**
* Create table 'alliance_fivehundredfriends/redemption'
*/
$table = $installer->getConnection()
	->newTable($installer->getTable('alliance_fivehundredfriends/redemption'))
	
	// Columns
	->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity'  => true,
		'unsigned'  => true,
		'nullable'  => false,
		'primary'   => true,
	), 'Entity ID')
	->addColumn('quote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned'  => true,
		'nullable'  => false,
	), 'Quote Id')
	->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned'  => true,
		'nullable'  => false,
	), 'Customer Id')
	->addColumn('customer_email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Customer Email')
	->addColumn('total_points', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned' => true,
		'nullable'  => false,
		'default'   => '0'
	), 'Total Points')
	->addColumn('redeem_points', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned' => true), 'Points to Redeem')
	->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(), 'Status')
	->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned'  => true,
		'nullable'  => true,
	), 'Order Id')
	->addColumn('order_status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(), 'Status')
	->addColumn('quote_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Quote Date')
	->addColumn('order_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Order Date')

	// Indexes
	->addIndex(
		$installer->getIdxName(
			'alliance_fivehundredfriends/redemption',
			array('quote_id'),
			Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
		),
		array('quote_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
	)
	->addIndex($installer->getIdxName('alliance_fivehundredfriends/redemption', array('customer_id')), array('customer_id'))
	->addIndex($installer->getIdxName('alliance_fivehundredfriends/redemption', array('order_id')), array('order_id'))

	// Foreing Keys
	/*
	->addForeignKey(
		$installer->getFkName('alliance_fivehundredfriends/redemption', 'quote_id', 'sales/quote', 'entity_id'),
		'quote_id', $installer->getTable('sales/quote'), 'entity_id',
		Varien_Db_Ddl_Table::ACTION_NO_ACTION, Varien_Db_Ddl_Table::ACTION_NO_ACTION
	)
	*/
	->addForeignKey(
		$installer->getFkName('alliance_fivehundredfriends/redemption', 'customer_id', 'customer/entity', 'entity_id'),
		'customer_id', $installer->getTable('customer/entity'), 'entity_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
	)
	->addForeignKey(
		$installer->getFkName('alliance_fivehundredfriends/redemption', 'order_id', 'sales/order', 'entity_id'),
		'order_id', $installer->getTable('sales/order'), 'entity_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
	)

	// Add comment.
	->setComment('Redeem Table')
;
$installer->getConnection()->createTable($table);


/**
* Create table 'alliance_fivehundredfriends/redemption_item'
*/
$table = $installer->getConnection()
	->newTable($installer->getTable('alliance_fivehundredfriends/redemption_item'))
	
	// Columns
	->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity'  => true,
		'unsigned'  => true,
		'nullable'  => false,
		'primary'   => true,
	), 'Entity ID')
	->addColumn('redeem_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned'  => true,
		'nullable'  => false,
		'default'   => '0',
	), 'FiveHundredFriends Id')
	->addColumn('reward_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned' => true), 'Reward id')
	->addColumn('redeem_points', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned' => true), 'Points to Redeem')
	->addColumn('reward_type', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array('nullable'  => false), 'Reward Type')
	->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(), 'Discount Amount')
	->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(), 'Status')
	
	// Indexes
	->addIndex($installer->getIdxName('alliance_fivehundredfriends/redemption_item', array('redeem_id')), array('redeem_id'))

	// Foreing Keys
	->addForeignKey(
		$installer->getFkName('alliance_fivehundredfriends/redemption_item', 'redeem_id', 'alliance_fivehundredfriends/redemption', 'entity_id'),
		'redeem_id', $installer->getTable('alliance_fivehundredfriends/redemption'), 'entity_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
	)
	
	// Add comment.
	->setComment('Redeem Items Table')
;
$installer->getConnection()->createTable($table);


/**
* Create table 'alliance_fivehundredfriends/autoreplenishreward'
*/
$table = $installer->getConnection()
	->newTable($installer->getTable('alliance_fivehundredfriends/autoreplenishreward'))
	
	// Columns
	->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity'  => true,
		'unsigned'  => true,
		'nullable'  => false,
		'primary'   => true,
	), 'Entity ID')

	->addColumn('increment_order_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array('nullable'  => true), 'Customer Order Id')
	->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array('nullable'  => true), 'Customer Id')
	->addColumn('customer_email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable'  => true), 'Customer Email')
	->addColumn('customer_group_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable'  => true), 'Customer Group Name')
	->addColumn('order_created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array('nullable'  => true), "Order Created At")
	->addColumn('reward_created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array('nullable'  => true), "Reward Created At")
	->addColumn('status_order', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array('nullable'  => true), 'Order Status')
	->addColumn('status_reward', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array('nullable'  => true), 'Reward Status')
	
		
	// Add comment.
	->setComment('Autoreplenish Reward Table')
;
$installer->getConnection()->createTable($table);

$installer->endSetup();