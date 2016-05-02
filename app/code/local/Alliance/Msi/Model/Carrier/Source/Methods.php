<?php

/**
 * Class Alliance_Msi_Model_Carrier_Source_Methods
 */
class Alliance_Msi_Model_Carrier_Source_Methods
{
	/**
	 * Returns values for "Allowed Methods" multiselect for Alliance Custom Msi carrier
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$allowed_methods = Mage::helper('alliance_msi')->getAllowedMethods();
		$source_methods = array();
		foreach ($allowed_methods as $key => $value) {
			$source_methods[] =  array('value' => $key, 'label' => $value);
		}
		return $source_methods;
	}
}
