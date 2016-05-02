<?php

class Alliance_Msi_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Returns an array of allowed msi methods
	 *
	 * @return array
	 */
	public function getAllowedMethods()
	{
		$allowed_methods = array();

		for ($i = 1; $i < 3; $i++) {
			if ($method_name = Mage::getStoreConfig('carriers/alliance_msi/method_'.$i.'_name')) {
				$slug = $this->convertToSlug($method_name);
				$allowed_methods[$slug] = $method_name;
			}
		}

		return $allowed_methods;
	}

	/**
	 * Takes a name and formats it as a slug for use as a method code
	 *
	 * @param $string
	 * @return mixed
	 */
	public function convertToSlug($string)
	{
		return str_replace('__', '_', str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower(trim($string)))));
	}
}
