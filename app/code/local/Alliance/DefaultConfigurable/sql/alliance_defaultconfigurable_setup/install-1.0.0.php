<?php

/**
 * Installs new product attribute (by default, "default_configuration_id") to set the ID of the child product
 * that parent configurable products should default to
 */

$this->startSetup();
$this->addAttribute('catalog_product', Alliance_DefaultConfigurable_Helper_Data::DEFAULT_CONFIGURATION_ATTRIBUTE_CODE, array(
    'group'             => 'General',
    'type'              => 'int',
    'input'             => 'hidden',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Default configuration id',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => true,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'   => false,
    'visible_in_advanced_search'   => false,
    'unique'            => false,
    'apply_to'          => Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
    'is_configurable'   => false,
));
$this->endSetup();