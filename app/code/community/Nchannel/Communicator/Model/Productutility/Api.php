<?php
class Nchannel_Communicator_Model_Productutility_Api extends Mage_Api_Model_Resource_Abstract
{
	public function linkConfigurable($configurableProductID)
	{
		return "Hello World! My argument is : " . $configurableProductID;
	}
	public function addattributes($sku , $attrCode)
	{
		return "Success with v1";
	}
}
?>