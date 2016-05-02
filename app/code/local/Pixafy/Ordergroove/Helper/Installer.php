<?php
/**
 * Ordergroove installer helper class. This helper is utilized in the sql installation files
 * to provide access and functionality during module installation and upgrades.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Helper_Installer extends Mage_Core_Helper_Abstract{
	
	/**
	 * Class constants the represent default Magento codes and values
	 */
	const ATTRIBUTE_SET_NAME_DEFAULT	=	'Default';
	const ATTRIBUTE_GROUP_NAME_GENERAL	=	'General';
	const COLUMN_ATTR_SET_NAME			=	'attribute_set_name';
	const COLUMN_ATTR_GROUP_NAME		=	'attribute_group_name';
	const COLUMN_ENTITY_TYPE_ID			=	'entity_type_id';
	const COLUMN_ATTRIBUTE_SET_ID		=	'attribute_set_id';
	
	/**
	 * OrderGroove Website code
	 * 
	 * @var string
	 */
	public $WEBSITE_CODE_ORDERGROOVE	=	'og_website';
	
	/**
	 * OrderGroove website name
	 * 
	 * @var string
	 */
	public $WEBSITE_NAME_ORDERGROOVE	=	'OrderGroove Website';
	
	/**
	 * OrderGroove store name
	 * 
	 * @var string
	 */
	public $STORE_NAME_ORDERGROOVE		=	'OrderGroove Store';
	
	/**
	 * OrderGroove store view code
	 * 
	 * @var string
	 */
	public $STORE_VIEW_CODE_ORDERGROOVE	=	'og_store';
	
	/**
	 * OrderGroove store view name
	 * 
	 * @var string
	 */
	public $STORE_VIEW_NAME_ORDERGROOVE	=	'OrderGroove Store View';
	
	/**
	 * OrderGroove language code
	 * 
	 * @var string
	 */
	public $LANGUAGE_CODE_ORDERGROOVE	=	'en';
	
	/**
	 * Return the category_product entity type id
	 * 
	 * @return int
	 */
	public function getCategoryProductEntityTypeId(){
		return Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
	}
	
	/**
	 * Return the default attribute set id for a given entity type
	 * 
	 * @param int $entityTypeId
	 * @return mixed
	 */
	public function getDefaultAttributeSetIdFromEntityTypeId($entityTypeId){
		if(!$entityTypeId){
			return FALSE;
		}
		
		$attrSet	=	Mage::getResourceModel('eav/entity_attribute_set_collection');
		$attrSet->addFieldToFilter(self::COLUMN_ATTR_SET_NAME, self::ATTRIBUTE_SET_NAME_DEFAULT);
		$attrSet->addFieldToFilter(self::COLUMN_ENTITY_TYPE_ID, $entityTypeId);
		if(is_object($attrSet->getFirstItem())){
			return $attrSet->getFirstItem()->getAttributeSetId();
		}
		return FALSE;
	}
	
	/**
	 * Return the attribute group id named General for a specific attribute set
	 * 
	 * @param int $attrSetId
	 * @return mixed
	 */
	public function getGeneralGroupIdByAttributeSetId($attrSetId){
		if(!$attrSetId){
			return FALSE;
		}
		$attrSet	=	Mage::getResourceModel('eav/entity_attribute_group_collection');
		$attrSet->addFieldToFilter(self::COLUMN_ATTR_GROUP_NAME, self::ATTRIBUTE_GROUP_NAME_GENERAL);
		$attrSet->addFieldToFilter(self::COLUMN_ATTRIBUTE_SET_ID, $attrSetId);
		if(is_object($attrSet->getFirstItem())){
			return $attrSet->getFirstItem()->getAttributeGroupId();
		}
		return FALSE;
	}
	
	/**
	 * Create a new Magento website
	 * 
	 * @param string $websiteCode
	 * @param string $websiteName
	 * 
	 * return Mage_Core_Model_Website
	 */
	public function createWebsite($websiteCode, $websiteName) {
		$website = Mage::getModel('core/website');
		if($websiteCode && $websiteName) {
			$website->setCode($websiteCode)->setName($websiteName)->save();
		}
		return $website;
	}

	/**
	 * Create a new Magento store
	 * 
	 * @param int $websiteId
	 * @param string $storeName
	 * @param int $rootCategoryId (optional)
	 * 
	 * @return Mage_Core_Model_Store_Group
	 */
	public function createStore($websiteId, $storeName, $rootCategoryId = '') {
		if(!$rootCategoryId) {
			$rootCategoryId = Mage::app()->getStore()->getRootCategoryId();
		}

		$storeGroup = Mage::getModel('core/store_group');
		if($websiteId && $storeName && !is_null($rootCategoryId)) {
			$storeGroup->setWebsiteId($websiteId)->setName($storeName)->setRootCategoryId($rootCategoryId)->save();
		}
		return $storeGroup;
	}

	/**
	 * Create a new Magento store view
	 * 
	 * @param int $websiteId
	 * @param int $storeId
	 * @param string $storeViewCode
	 * @param string $storeViewName
	 * @param string $languageCode
	 * @param int $isActive
	 */
	public function createStoreView($websiteId, $storeId, $storeViewCode, $storeViewName, $languageCode, $isActive=0) {
		$store = Mage::getModel('core/store');
		$store->setCode($storeViewCode)
				->setWebsiteId($websiteId)
				->setGroupId($storeId)
				->setName($storeViewName)
				->setIsActive($isActive)
				->setLanguageCode($languageCode)
				->save();
		return $store;
	}
}
?>
