<?php
/**
 * @package Pixafy_Ordergroove
 * @author Pixafy Engineering Team <info@pixafy.com>
 */
class Pixafy_Ordergroove_Block_Pagetag_Product extends Pixafy_Ordergroove_Block_Pagetag_Abstract{
	public function _construct(){
		parent::_construct();
	}
	
	/**
	 * Get the current product for the product page
	 * 
	 * @return Mage_Catalog_Model_Product
	 */
	public function getProduct(){
		return Mage::registry('current_product');
	}
	
	/**
	 * Return whether or not this content can be shown
	 * 
	 * @return boolean
	 */
	protected function _canShow(){
		return $this->getConfig()->isProductPagePagetagEnabled();
	}
}
?>
