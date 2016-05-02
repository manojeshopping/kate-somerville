<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'alliance_globalbanner_banners'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('alliance_globalbanner/banner'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'ID')
    ->addColumn('image', Varien_Db_Ddl_Table::TYPE_TEXT, 2083, array(
        'nullable' => false,
    ), 'Image')
    ->addColumn('image_link', Varien_Db_Ddl_Table::TYPE_TEXT, 2083, array(
        'nullable' => false,
    ), 'Image Link')
    ->addColumn('image_alt', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
    ), 'Image Alt Text')
    ->addColumn('new_tab', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
        'default'  => 'No',
    ), 'New Tab')
    ->addColumn('from_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(), 'From Date')
    ->addColumn('to_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(), 'To Date')
    ->addColumn('priority', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'default' => 0,
    ), 'Priority')
    ->addColumn('logged_in_status', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
        'default' => 'Both',
    ), 'For Customer Logged In Status')
    ->addColumn('stores', Varien_Db_Ddl_Table::TYPE_TEXT, 4000, array(
        'nullable' => false,
    ), 'Stores')
    ->addColumn('pages', Varien_Db_Ddl_Table::TYPE_TEXT, 4000, array(
        'nullable' => false,
    ), 'Pages')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
        'default'  => 'Enabled',
    ), 'Status');
$installer->getConnection()->createTable($table);

$installer->endSetup();