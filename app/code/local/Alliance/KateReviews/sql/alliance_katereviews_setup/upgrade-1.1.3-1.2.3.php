<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('alliance_katereviews/review'), 'customer_name', array(
			'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
			'nullable' => false,
			'comment'  => 'Customer Name'
		));

$installer->endSetup();