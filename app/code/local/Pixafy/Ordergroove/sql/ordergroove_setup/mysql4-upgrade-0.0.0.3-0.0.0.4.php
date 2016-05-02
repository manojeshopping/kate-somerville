<?php
/**
 * Installation file for OrderGroove module. Create the subscription
 * attribute and add it to the general attribute group for the
 * default attribute set. Any additional attribute sets will
 * have to be added manually.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
$installer	=	$this;
$installer->startSetup();
	$installerHelper	=	Mage::helper('ordergroove/installer');

	$_attribute_data = array(
		'attribute_code'				=>	Pixafy_Ordergroove_Helper_Constants::ATTRIBUTE_CODE_PRODUCT_DISCONTINUED,
		'is_global' 					=>	'1',
		'frontend_input'				=>	'boolean',
		'default_value_text'			=>	'',
		'default_value_yesno'			=>	'0',
		'default_value_date'			=>	'',
		'default_value_textarea' 		=>	'',
		'is_unique' 					=>	'0',
		'is_required'					=>	'0',
		'apply_to'						=>	array(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE, Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE, Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL),
		'is_configurable'				=>	'1',
		'is_searchable'					=>	'0',
		'is_visible_in_advanced_search'	=>	'0',
		'is_comparable'					=>	'0',
		'is_used_for_price_rules'		=>	'0',
		'is_wysiwyg_enabled'			=>	'0',
		'is_html_allowed_on_front'		=>	'0',
		'is_visible_on_front'			=>	'0',
		'used_in_product_listing'		=>	'1',
		'used_for_sort_by'				=>	'0',
		'frontend_label'				=>	array('OrderGroove Product Discontinued Flag')
	);

	$model = Mage::getModel('catalog/resource_eav_attribute');
	if (!isset($_attribute_data['is_configurable'])) {
		$_attribute_data['is_configurable'] = 0;
	}

	if (!isset($_attribute_data['is_filterable'])) {
		$_attribute_data['is_filterable'] = 0;
	}

	if (!isset($_attribute_data['is_filterable_in_search'])) {
		$_attribute_data['is_filterable_in_search'] = 0;
	}

	if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
		$_attribute_data['backend_type'] = $model->getBackendTypeByInput($_attribute_data['frontend_input']);
	}

	$_attribute_data['default_value'] = '0';


	$model->addData($_attribute_data);

	$entityTypeId		=	$installerHelper->getCategoryProductEntityTypeId();
	$attributeSetId		=	$installerHelper->getDefaultAttributeSetIdFromEntityTypeId($entityTypeId);
	$attributeGroupId	=	$installerHelper->getGeneralGroupIdByAttributeSetId($attributeSetId);

	$model->setEntityTypeId($entityTypeId);
	$model->setAttributeSetId($attributeSetId);
	$model->setAttributeGroupId($attributeGroupId);
	$model->setIsUserDefined(1);
	try {
		$model->save();
	}
	catch (Exception $e) { echo '<p>Sorry, error occured while trying to save the attribute. Error: '.$e->getMessage().'</p>'; }
	$installer->endSetup();
?>
