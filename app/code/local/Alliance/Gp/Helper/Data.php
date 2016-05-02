<?php

/**
 * Description of class 
 *  helper to show header msg
 * 
 * @author     Alliance Dev Team <>
 */
class Alliance_Gp_Helper_Data extends Mage_Core_Helper_Abstract {
	
	private $_filename_prefix = "basket_";
	
	const GIFT_PRODUCT_ID = 'gp/expglobal/expglobal_giftproductid';

	const FTP_HOST = 'gp/expglobal/expglobal_hostname';
	const FTP_USERNAME = 'gp/expglobal/expglobal_ftpusername';
	const FTP_PASSWORD = 'gp/expglobal/expglobal_ftppassword';
	
	const CSV_ORDER_LOCAL_FILE_PATH = 'gp/expglobal/expglobal_orderlocal';
	const CSV_ORDER_REMOTE_FILE_PATH = 'gp/expglobal/expglobal_orderremote';
	
	const LIMIT_PER_EXPORT = 'gp/expglobal/expglobal_limitperexport';
	
	const START_EXPORT_DATE = 'gp/expglobal/expglobal_startexportdate';
	
	
	public function getCcTypeArray()
	{
		return array(
			'Visa' => 'Visa',
			'MasterCard' => 'MasterCard',
			'Discover' => 'Discover',
			'American Express' => 'AMEX'
		);
	}
	
	public function getCcTypeAuthArray()
	{
		return array(
			'VI' => 'Visa',
			'MC' => 'MasterCard',
			'Di' => 'Discover',
			'AE' => 'AMEX'
		);
	}

	
	public function getShippingAddress($order)
	{
		if (is_object($order->getShippingAddress())) {
			return $order->getShippingAddress();
		} else {
			return $order->getBillingAddress();
		}
	}
	
	public function getBillAddress($bill)
	{
		$billAddress = $bill->getStreet();
		if(is_array($billAddress)) $billAddress = implode(" , ", $billAddress);
		
		return $billAddress;
	}
	
	
	public function getStreet($ship)
	{
		$shipAddress = $ship->getStreet();		
		if(is_array($shipAddress)) $shipAddress = implode(" , ", $shipAddress);
		
		return $shipAddress;
	}
	
	public function getRegion($region, $country_id)
	{
		$region = Mage::getSingleton('directory/region')->loadByName($region, $country_id)->getCode();
		return $region;
	}
	
	public function getCountry($country_id)
	{
		$country = Mage::getModel('directory/country')->load($country_id)->getData('iso3_code');
		
		return $country;
	}
	
	public function getShippingDescription($order, $shipCountry)
	{
		if($order->getOrderMode() == "Amazon Order" && $shipCountry == "USA") {
			$shippingDescription = "Select Shipping Method - USPS Priority Mail";
		} elseif($order->getOrderMode() == "Amazon Order" && $shipCountry != "USA") {
			$shippingDescription = "Select Shipping Method - USPS Priority Mail International";
		} else {
			$shippingDescription = $order->getShippingDescription();
		}
		
		return $shippingDescription;
	}
	
	
	public function getGiftProduct()
	{
		$product = Mage::getSingleton('catalog/product')->load(self::GIFT_PRODUCT_ID);
		
		return $product;
	}
	
	public function getFptConnection()
	{
		return array(
			'host' => Mage::getStoreConfig(self::FTP_HOST),
			'username' => Mage::getStoreConfig(self::FTP_USERNAME),
			'password' => Mage::getStoreConfig(self::FTP_PASSWORD),
		);
	}
	
	public function getRemoteFilePath()
	{
		$remoteFolder = Mage::getStoreConfig(self::CSV_ORDER_REMOTE_FILE_PATH);
		if(strpos($remoteFolder, -1) != "/") $remoteFolder .= "/";
		
		return $remoteFolder;
	}
	
	public function getLimitPerExport()
	{
		return Mage::getStoreConfig(self::LIMIT_PER_EXPORT);
	}
	
	public function getStartExportDate()
	{
		$date = date("Y-m-d", strtotime(Mage::getStoreConfig(self::START_EXPORT_DATE)));
		return $date;
	}
	
	public function getLocalFolder()
	{
		$localFolder = Mage::getStoreConfig(self::CSV_ORDER_LOCAL_FILE_PATH);
		if(strpos($localFolder, -1) != "/") $localFolder .= "/";
		
		return $localFolder;
	}
	
	public function getFileNamePrefix()
	{
		return $this->_filename_prefix;
	}
}