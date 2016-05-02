<?php
/**
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 * 
 * @method extractField(string $key)
 * @method extractStreet(string $addrOneKey, string $addrTwoKey)
 * @method extractRegion(string $regionKey, string $countryCode)
 * @method extractRegionId(string $regionKey, string $countryCode)
 * @method loadRegionData()
 * @method getRegionData()
 * 
 * Order API parent address class. Contains helper functions to extract fields
 * from the XML based on the key, as well as the ability to extract data required
 * by both the shipping and billing address classes
 */
class Pixafy_Ordergroove_Model_Api_Order_Address extends Pixafy_Ordergroove_Model_Api_Order{
	/**
	 * Array of xml data. Changes depending
	 * on what type of data is being currently
	 * being processed (header, customer, item...)
	 * 
	 * @var array
	 */
	protected $data;
	
	/**
	 * Stores country and state data so that
	 * it does not have to be loaded multiple times
	 * 
	 * @var array
	 */
	protected $_regionData;
	
	/**
	 * Extract a field from the data object based
	 * on a specific key.
	 * 
	 * @param string $key
	 * @return string or float
	 */
	protected function extractField($key){
		/**
		 * Validation check to ensure Magento
		 * does not log a system error for
		 * blank values
		 */
		if(json_encode($this->data->$key) == '{}'){
			return '';
		}
		return (string)($this->data->$key);
	}
	
	/**
	 * Return the street to be used to the address. Magento
	 * uses and array to handle addresses with multiple lines,
	 * but just a string to handle a single line address. This
	 * function determines if the address has one or two lines
	 * and returns the correct value.
	 * 
	 * @param string $addrOneKey
	 * @param string $addrTwoKey
	 * @return array | string
	 * 
	 */
	protected function extractStreet($addrOneKey, $addrTwoKey){
		$addressOne	=	$this->extractField($addrOneKey);
		$addressTwo	=	$this->extractField($addrTwoKey);
		
		if($addressTwo){
			return array(0 => $addressOne, 1 => $addressTwo);
		}
		
		return $addressOne;
	}
	
	/**
	 * Return the state name based on the abbreviation and country
	 * provided from the XML. This function will load Magentos existing
	 * region data on its first call, and will search the saved values as
	 * a hash table for all subsequent calls.
	 * 
	 * @param string $regionKey
	 * @param string $countryCode
	 * @return string
	 */
	protected function extractRegion($regionKey, $countryCode){
		/**
		 * If we have no region data then load it
		 */
		if(!$this->getRegionData()){
			$this->loadRegionData();
		}
		
		$regionData	=	$this->getRegionData();
		$regionCode	=	$this->extractField($regionKey);
		if(array_key_exists($countryCode, $regionData)){
			if(array_key_exists($regionCode, $regionData[$countryCode])){
				return $regionData[$countryCode][$regionCode]->getName();
			}
		}
		
		/**
		 * If no data is found then return null, as Magento can accept
		 * this as a blank value without throwing an error
		 */
		return '';
	}
	
	/**
	 * Return the numerical region id for a state based on a
	 * country code and state abbreviation. This function
	 * follows similar logic to extractRegion but looks for
	 * the region id instead of the region.
	 * 
	 * @param string $regionKey
	 * @param string $countryCode
	 * @return int
	 */
	protected function extractRegionId($regionKey, $countryCode){
		if(!$this->getRegionData()){
			$this->loadRegionData();
		}
		
		$regionData	=	$this->getRegionData();
		$regionCode	=	$this->extractField($regionKey);
		if(array_key_exists($countryCode, $regionData)){
			if(array_key_exists($regionCode, $regionData[$countryCode])){
				return $regionData[$countryCode][$regionCode]->getRegionId();
			}
		}
		
		return 0;
	}
	
	/**
	 * Load the state and country data. Format this data as a hashtable with
	 * the structure [country_id][state_code] = state_data
	 * 
	 * @return array ( COUNTRY_ID => Mage_Directory_Model_Region[] )
	 */
	protected function loadRegionData(){
		$col = Mage::getResourceModel('directory/region_collection');
		foreach($col as $regionObject){
			$this->_regionData[$regionObject->getCountryId()][$regionObject->getCode()]	=	$regionObject;
		}
	}
	
	/**
	 * Return the currently loaded region data
	 * 
	 * @return boolean | array
	 */
	public function getRegionData(){
		if(!$this->_regionData){
			return FALSE;
		}
		return $this->_regionData;
	}
}
?>
