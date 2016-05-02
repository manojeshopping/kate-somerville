<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'alliance_bannerslider_banner'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('alliance_bannerslider/banner'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'ID')
    ->addColumn('slider_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 'homepage',
    ), 'Slider Code')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
    ), 'Title')
    ->addColumn('image', Varien_Db_Ddl_Table::TYPE_TEXT, 2083, array(
        'nullable'  => false,
    ), 'Image')
    ->addColumn('link', Varien_Db_Ddl_Table::TYPE_TEXT, 2083, array(
        'nullable'  => false,
    ), 'Link')
    ->addColumn('new_tab', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => 'No',
    ), 'New Tab')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Sort Order')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => 'Enabled',
    ), 'Status');
$installer->getConnection()->createTable($table);

$installer->endSetup();