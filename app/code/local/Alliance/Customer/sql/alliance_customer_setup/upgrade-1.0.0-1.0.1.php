<?php

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->removeAttribute('customer', 'secondary_skin_concern');

$installer = $this;

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer->startSetup();

$installer->addAttribute('customer', 'secondary_skin_concern', array(
    'label'         => 'Secondary Skin Concern',
    'user_defined'  => true,
    'visible'       => true,
    'required'      => false,
    'type'          => 'int',
    'input'         => 'select',
    'source'        => 'eav/entity_attribute_source_table',
));

$table_options       = $installer->getTable('eav_attribute_option');
$table_option_values = $installer->getTable('eav_attribute_option_value');

$attribute_id = (int)$installer->getAttribute('customer', 'secondary_skin_concern', 'attribute_id');
$options = array(
    'Acne',
    'Anti-Aging',
    'Acne & Anti-Aging',
    'Sensitive',
    'Discoloration',
    'Dry',
    'Oily',
);
foreach ($options as $sort_order => $label) {

    // add option
    $data = array(
        'attribute_id' => $attribute_id,
        'sort_order'   => $sort_order,
    );
    $installer->getConnection()->insert($table_options, $data);

    // add option label
    $option_id = (int)$installer->getConnection()->lastInsertId($table_options, 'option_id');
    $data = array(
        'option_id' => $option_id,
        'store_id'  => 0,
        'value'     => $label,
    );
    $installer->getConnection()->insert($table_option_values, $data);

}

$test_attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'secondary_skin_concern');
$test_attribute->setData('sort_order', 130);
$used_in_forms = array(
    'checkout_register',
    'customer_account_create',
    'customer_account_edit',
    'adminhtml_checkout',
    'adminhtml_customer',
);
$test_attribute->setData('used_in_forms', $used_in_forms);
$test_attribute->save();

$installer->endSetup();
