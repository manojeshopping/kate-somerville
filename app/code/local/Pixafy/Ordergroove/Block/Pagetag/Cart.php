<?php
/**
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Block_Pagetag_Cart extends Pixafy_Ordergroove_Block_Pagetag_Abstract{
	public function _construct(){
		parent::_construct();
	}
	
	/**
	 * Return whether or not this content can be shown
	 * 
	 * @return boolean
	 */
	protected function _canShow(){
		return $this->getConfig()->isCartPagetagEnabled();
	}
}
?>
