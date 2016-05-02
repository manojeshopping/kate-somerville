<?php
/**
 * Rewrite the country collection so we can
 * remove non-US countries when og_autoship 
 * is set.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Model_Rewrite_Directory_Resource_Country_Collection extends Mage_Directory_Model_Resource_Country_Collection
{
	/**
	 * Define main table
	 *
	 */
	protected function _construct(){
		parent::_construct();
	}

	/**
	 * Remove items from the collection if
	 * specified by system configuration
	 *
	 * @param string $emptyLabel
	 * @return array
	 */
	public function toOptionArray($emptyLabel = ' ')
	{
		$options	=	parent::toOptionArray($emptyLabel);
		if(Mage::helper('ordergroove/config')->removeCountriesFromDropdown()){
			foreach($options as $i => $option){
				if($option['value'] != 'US'){
					unset($options[$i]);
				}
			}
		}
		return $options;
	}
}
