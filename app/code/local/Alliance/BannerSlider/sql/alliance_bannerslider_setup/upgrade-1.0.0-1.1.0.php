<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Update table alliance_bannerslider_banner, add column for store_code so banners can be displayed
 * only in certain stores
 */
$installer->getConnection()->addColumn(
    $installer->getTable('alliance_bannerslider/banner'),
    'store_code',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'   => 255,
        'nullable' => FALSE,
        'default'  => Mage::getModel('core/store')->load(1)->getCode(),
        'comment'  => 'Store Code',
    ));

$installer->endSetup();