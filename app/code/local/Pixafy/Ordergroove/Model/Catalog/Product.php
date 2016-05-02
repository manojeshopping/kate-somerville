<?php
/**
 * @package     Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 * 
 * OrderGroove product model class. Used to
 * provide a different price function so that
 * the getPrice function returns the price
 * that is provided by the OrderGroove XML
 */
class Pixafy_Ordergroove_Model_Catalog_Product extends Mage_Catalog_Model_Product{
	
	/**
	 * Ordergroove price
	 * 
	 * @var float
	 */
	protected $_ordergroovePrice	=	NULL;
	
	protected function _construct()
	{
		parent::_construct();
	}
	
	public function setOrdergroovePrice($price){
		$this->_ordergroovePrice	=	$price;
	}
	
	public function getPrice(){
		if(!is_null($this->_ordergroovePrice)){
			return $this->_ordergroovePrice;
		}
		return parent::getPrice();
	}
}
?>
