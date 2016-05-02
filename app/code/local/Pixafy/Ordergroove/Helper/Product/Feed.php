<?php
/**
 * Product feed generation class. Export all products
 * and generate a csv file which is posted to a specific
 * SFTP server
 * 
 * @package     Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Helper_Product_Feed extends Mage_Core_Helper_Abstract{
	
	/**
	 * Ordergroove column keys
	 */
	const KEY_PRODUCT_NAME	=	'product_name';
	const KEY_PRODUCT_ID	=	'product_id';
	const KEY_SKU			=	'product_sku';
	const KEY_PRICE			=	'product_price';
	const KEY_PRODUCT_URL	=	'product_url';
	const KEY_IMAGE_URL		=	'product_image_url';
	const KEY_SUB_ENABLED	=	'product_subscription_enabled';
	const KEY_IN_STOCK		=	'product_is_in_stock';
	const KEY_DISC_FLAG		=	'product_discontinued';
	
	/**
	 * Array of the products
	 * 
	 * @var array Mage_Catalog_Model_Product[]
	 */
	protected	$_products;
	
	/**
	 * The database connection resource
	 * 
	 * @var Mage_Core_Model_Resource
	 */
	protected	$_resource;
	
	/**
	 * Array of product data that will
	 * be processed and sent to the CSV
	 * file.
	 * 
	 * @var array (sku => productData)
	 */
	protected	$_productData;
	
	/**
	 * Array of key maps that map the
	 * ordergroove_key => magento_key
	 * 
	 * @var array
	 */
	protected	$_keyMappings;
	
	/**
	 * Flag indicating whether or not the 
	 * feed was executed manually or by
	 * cron.
	 * 
	 * @var boolean
	 */
	protected $_isManual;
	
	/**
	 * Flag to indicate whether or not a 
	 * fatal error has occurred that
	 * will cause the script to fail.
	 * 
	 * @var boolean
	 */
	protected $_hasFatalError;
	
	/**
	 * Array of error messages
	 * 
	 * @var array
	 */
	protected $_errorMessages;
	
	/**
	 * An array of merchant ids, containing
	 * and array of the website ids that 
	 * they belong to.
	 * 
	 * @var array
	 */
	protected $_merchantWebsites = array();
	
	/**
	 * The current merchant id being processed
	 * 
	 * @var string
	 */
	protected $_currentMerchantId;
	
	/**
	 * The current array of websites being processed
	 * 
	 * @var array
	 */
	protected $_currentWebsiteIds = array();
	
	/**
	 * FTP credentials for the different websites
	 * 
	 * @var array
	 */
	protected $_ftpCredentialsPerWebsite = array();
	
	/**
	 * Build an array of keys so we do not have to manually
	 * set each key in the final array. Specific keys will
	 * require special action, that is taken care of with
	 * the switch statement in the buildData function
	 */	
	protected function _buildKeyArray(){
		$this->_keyMappings	=	array(
			self::KEY_PRODUCT_NAME	=>	'name',
			self::KEY_PRODUCT_ID	=>	'entity_id',
			self::KEY_SKU			=>	'sku',
			self::KEY_PRICE			=>	'price',
			self::KEY_PRODUCT_URL	=>	'url_path',
			self::KEY_IMAGE_URL		=>	Mage::helper('ordergroove/config')->getProductFeedImageType(),
			self::KEY_SUB_ENABLED	=>	Pixafy_Ordergroove_Helper_Constants::ATTRIBUTE_CODE_SUBSCRIPTION_ENABLED,
			self::KEY_IN_STOCK		=>	'is_in_stock',
			self::KEY_DISC_FLAG		=>	Pixafy_Ordergroove_Helper_Constants::ATTRIBUTE_CODE_PRODUCT_DISCONTINUED
		);
	}
	
	/**
	 * Initialize the database resource
	 */
	protected function _initResource(){
		$this->_resource	=	Mage::getSingleton('core/resource');
	}
	
	/**
	 * Return an array of the attributes to select
	 * 
	 * @return array
	 */
	protected function getAttributesToSelect(){
		return array(
			'name',
			'price', 
			'url_path',
			'stock_item',
			Pixafy_Ordergroove_Helper_Constants::ATTRIBUTE_CODE_SUBSCRIPTION_ENABLED,
			Mage::helper('ordergroove/config')->getProductFeedImageType(),
			'image',
			Pixafy_Ordergroove_Helper_Constants::ATTRIBUTE_CODE_PRODUCT_DISCONTINUED
		);
	}
	
	/**
	 * High level execution of the product feed that will
	 * generate a feed file for each website that
	 * has a different merchant id.
	 */
	protected function _initMultiSite(){
		try{
			$merchantIds 		=	array();
			$websiteMerchants 	=	array();
			$websites = Mage::getModel('core/website')->getCollection();
			foreach($websites as $website){
				if($website->getCode() == Mage::helper('ordergroove/installer')->WEBSITE_CODE_ORDERGROOVE){
					continue;
				}
				foreach($website->getStores() as $store){
					if($merchantId = Mage::helper('ordergroove/config')->getMerchantId($store)){
						if(!array_key_exists($merchantId, $merchantIds)){
							$merchantIds[$merchantId] = array('websites' => array());
							$merchantIds[$merchantId] = array('ftp' => array());
						}
						if(!in_array($website->getId(), $merchantIds[$merchantId]['websites'])){
							$merchantIds[$merchantId]['websites'][] = $website->getId();
							$merchantIds[$merchantId]['ftp']['server'] 	= $this->getConfig()->getFtpServer($store);
							$merchantIds[$merchantId]['ftp']['username'] = $this->getConfig()->getFtpUsername($store);
							$merchantIds[$merchantId]['ftp']['password'] = $this->getConfig()->getFtpPassword($store);
						}
						break;
					}
				}
			}
		}
		catch(Exception $e){
			Mage::logException($e);
		}
		$this->_merchantWebsites = $merchantIds;
	}
	
	/**
	 * High level function that can be called 
	 * externally to start the product feed
	 * generation process.
	 * 
	 * @param boolean $isManual - indicates whether script is run from the admin by a user or automatically by the cron
	 * @return boolean
	 */
	public function generate($isManual=FALSE){
		$this->_isManual	=	$isManual;
		try{
			/**
			 * Init db resource
			 */
			$this->_initResource();
			
			
			/**
			 * Init for multi website support
			 */
			$this->_initMultiSite();
			
			/**
			 * Build key mapping array
			 */
			$this->_buildKeyArray();
			
			foreach($this->_merchantWebsites as $merchantId => $merchantSites){
				$this->_currentMerchantId	=	$merchantId;
				$this->_currentWebsiteIds	=	$merchantSites['websites'];
				
				/**
				 * Load the products from db
				 */
				$this->_loadProducts();
				
				/**
				 * Convert the collection to the array
				 * that will be used to create the 
				 * csv file.
				 */
				$this->_buildData();
				
				/**
				 * Generate the CSV file from the processed data
				 */
				$this->_generateFile();
				
				/**
				 * Attempt to upload the file
				 */
				if(!$this->_uploadFile()){
					$this->log("Failed to upload feed for merchant ".$this->_currentMerchantId);
				}
				
				$this->_currentMerchantId	=	NULL;
				$this->_currentWebsiteIds	=	NULL;
				$this->_products			=	NULL;
				$this->_productData			=	NULL;
			}
			return TRUE;
		}
		catch(Exception $e){
			$this->log("ERROR|".$e->getMessage());
		}
		return FALSE;
	}
	
	/**
	 * Load all products. Select attributes defined in
	 * getAttributesToSelect() and join with the
	 * catalog stock table to get the inventory
	 * levels for each product.
	 */
	protected function _loadProducts(){
		$this->_products	=	Mage::getResourceModel('catalog/product_collection')->addAttributeToSelect($this->getAttributesToSelect());
		$this->_products->addWebsiteFilter($this->_currentWebsiteIds);
		$this->_products->getSelect()->join(array('cisi'=>$this->getStockTableName()), 'e.entity_id = cisi.product_id', array('cisi.is_in_stock'));
	}
	
	/**
	 * Get the stock inventory table name.
	 * Do not hardcode value so that this
	 * will work with db prefixes
	 * 
	 * @return string
	 */
	protected function getStockTableName(){
		return $this->_resource->getTableName('cataloginventory/stock_item');
	}
	
	/**
	 * Build a CSV friendly array based
	 * on the product collection that
	 * has already been loaded.
	 */
	protected function _buildData(){
		foreach($this->_products as $product){
			$data	=	array();
			foreach($this->_keyMappings as $key => $attribute){
				/**
				 * Take specific action based on the product
				 * attribute that is being dealt with.
				 */
				switch($key){
					/**
					 * Product URL. Build full path based on Magento LINK url and product URL key
					 */
					case self::KEY_PRODUCT_URL:
						$data[$key]	=	Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).$product->getUrlPath();
						break;
					/**
					 * Product Image. Resize based on image type
					 */
					case self::KEY_IMAGE_URL:
						try{
							$data[$key]	=	Mage::helper('catalog/image')->init($product, $attribute)->resize(Mage::helper('ordergroove/config')->getDefaultImageDimensions($attribute))->__toString();
						}
						catch(Exception $e){
							$data[$key]	=	'';
						}
						break;
					/**
					 * All remaining fields set straight key value pair, with a 0 if no value is found
					 */
					default:
						$data[$key]	= (string)($product->getData($attribute) ? str_replace('', '', $product->getData($attribute)) : 0);
				}
			}
			/**
			 * Add product data to the array
			 */
			$this->_productData[$product->getSku()]	=	$data;
		}
	}
	
	/**
	 * Convert the product data array into a csv file
	 */
	protected function _generateFile(){
		if(!is_dir($this->getExportDirectory())){
			mkdir($this->getExportDirectory());
		}
		$fp = fopen($this->getCsvFilePath(), 'w');
		foreach ($this->_productData as $data) {
			$this->fputcsv($fp, $data);
		}
		fclose($fp);
	}
	
	/**
	 * Return the local export directory
	 */
	public function getExportDirectory(){
		return Mage::getBaseDir().'/var/export/';
	}
	
	/**
	 * Get the full path of the CSV file
	 * 
	 * @return string
	 */
	public function getCsvFilePath(){
		return $this->getExportDirectory().$this->getCsvFileName();
	}
	
	/**
	 * Return the filename of the csv file
	 * 
	 * @return sring
	 */
	public function getCsvFileName(){
		return $this->_currentMerchantId.'.Products.csv';
	}
	
	/**
	 * Return the full url path
	 */
	public function getCsvFileUrl(){
		return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'var/export/'.$this->getCsvFileName();
	}
	
	/**
	 * Write a line to a file
	 * $filePointer = the file resource to write to
	 * $dataArray = the data to write out
	 * $delimeter = the field separator
	 * 
	 * @param resource $filePointer
	 * @param array $dataArray
	 * @param string $delimiter
	 * @param string $enclosure
	 */
	public function fputcsv($filePointer,$dataArray,$delimiter=',',$enclosure='"'){
		/**
		 * Build the string
		 */
		$string = "";
		
		/**
		 * No leading delimiter
		 */
		$writeDelimiter = FALSE;
		foreach($dataArray as $dataElement)
		{
			/**
			 * Replaces a double quote with two double quotes
			 */
			$dataElement=str_replace('"', '\"', $dataElement);
			
			/**
			 * Adds a delimiter before each field (except the first)
			 */
			if($writeDelimiter){ 
				$string .= $delimiter;
			}
			
			/**
			 * Encloses each field with $enclosure and adds it to the string
			 */
			$string .= $enclosure . $dataElement . $enclosure;
			
			/**
			 * Delimiters are used every time except the first.
			 */
			$writeDelimiter = TRUE;
		}
		
		/**
		 * Append new line
		 */
		$string .= "\n";
		
		/**
		 * Write the string to the file
		 */
		fwrite($filePointer,$string);
	}
	
	/**
	 * Return the OrderGroove configuration class
	 * 
	 * @return Pixafy_Ordergroove_Helper_Config
	 */
	public function getConfig(){
		return Mage::helper('ordergroove/config');
	}
	
	/**
	 * Return the remote file upload full path
	 * 
	 * @return string
	 */
	public function getRemoteFilePath(){
		return '/incoming/'.$this->getCsvFileName();
	}
	
	/**
	 * Upload the created CSV file to the FTP server.
	 * The FTP credentials are designed in the System
	 * Configuration panel
	 * 
	 * @return boolean
	 */
	protected function _uploadFile(){
		$ftp_server		=	$this->_checkField($this->_merchantWebsites[$this->_currentMerchantId]['ftp']['server'], 'ftp_server');
		$ftp_user_name	=	$this->_checkField($this->_merchantWebsites[$this->_currentMerchantId]['ftp']['username'],	'ftp_username');
		$ftp_user_pass	=	$this->_checkField($this->_merchantWebsites[$this->_currentMerchantId]['ftp']['password'],	'ftp_password');
		
		if($this->hasFatalError()){
			return FALSE;
		}
		
		$connection = ssh2_connect($ftp_server, 22) or die('could not connect');
		ssh2_auth_password($connection, $ftp_user_name, $ftp_user_pass);
		$sftp		=	ssh2_sftp($connection);
		$stream 	=	@fopen("ssh2.sftp://$sftp".$this->getRemoteFilePath(), 'w');
		
		if (!$stream){
			$this->logFatalError("Could not open file: ".$this->getRemoteFilePath());
			@fclose($stream);
			return FALSE;
		}
		$dataToSend	=	@file_get_contents($this->getCsvFilePath());
		if ($dataToSend === false){
			$this->logFatalError("Could not open local file: ".$this->getCsvFilePath());
			@fclose($stream);
			return FALSE;
		}

		if (@fwrite($stream, $dataToSend) === false){
			$this->logFatalError("Could not send data from file: ".$this->getCsvFilePath());
			@fclose($stream);
			return FALSE;
		}
		
		@fclose($stream);
		
		$stream 	=	@fopen("ssh2.sftp://$sftp".$this->getRemoteFilePath(), 'r');
		if(!$stream){
			$this->logFatalError("Unable to upload file to remote server: ".$this->getRemoteFilePath());
			return FALSE;
		}
		return TRUE;
	}
	
	/**
	 * Ensure that the string is not null.
	 * If the string is null then log the error
	 * and exit.
	 * 
	 * @param string $string
	 * @param string $fieldName
	 * @return string
	 */
	protected function _checkField($string, $fieldName){
		$string	=	trim($string);
		if($string){
			return $string;
		}
		$this->logFatalError("Field not found: ".$fieldName);
	}
	
	/**
	 * Log to an error
	 * 
	 * @param string $message
	 */
	public function log($message){
		Mage::log($message, null, 'og_product_feed.log', true);
	}
	
	/**
	 * Log a message and exit.
	 * 
	 * @param string $errorMessage
	 */
	public function logFatalError($errorMessage){
		$this->_addError($errorMessage);
		$this->log("ERROR|".$errorMessage);
		if(!$this->isManualExecution()){
			exit;
		}
	}
	
	/**
	 * Return whether or not the script 
	 * was executed manually.
	 * 
	 * @return boolean
	 */
	public function isManualExecution(){
		return $this->_isManual;
	}
	
	/**
	 * Return whether or not a fatal
	 * error has been detected
	 * 
	 * @return boolean
	 */
	public function hasFatalError(){
		return $this->_hasFatalError;
	}
	
	/**
	 * Return a string of error messages
	 * 
	 * @return string
	 */
	public function getErrorMessages(){
		return implode('|', $this->_errorMessages);
	}
	
	/**
	 * Add an error message to the array of errors
	 * 
	 * @param string $message
	 */
	protected function _addError($message){
		$this->_errorMessages[]	=	$message;
		$this->_hasFatalError	=	TRUE;
	}
	
	/**
	 * Set the current merchant id
	 * 
	 * @param string $id
	 */
	public function setCurrentMerchantId($id){
		$this->_currentMerchantId = $id;
	}
}
