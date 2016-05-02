<?php
require_once dirname(dirname(__FILE__)) . '/abstract.php';

class Alliance_Shell_Alliance_Autoreplenish_Rewardpoints extends Mage_Shell_Abstract 
{
	public $dbConnection;
	public $autoReplenishDbTable;
	public $store_code = "og_store";             // Ordergroove store code

	public function run()
	{
		echo "Started : Rewarding AutoReplenish Points".PHP_EOL;
		$this->establishDatabaseConnection();
		$this->AutoreplenishReward();
		echo "Completed : Rewarding AutoReplenish Points".PHP_EOL;
	}
	
	public function establishDatabaseConnection()
	{
		$this->dbConnection = Mage::getSingleton( 'core/resource' )->getConnection( 'core_write' ); // To write to the database
		$this->autoReplenishDbTable =  Mage::getSingleton( 'core/resource' )->getTableName( 'alliance_fivehundredfriends/autoreplenishreward' );
	}
	
	protected function AutoreplenishReward() 
	{
		$query = "SELECT * from ".$this->autoReplenishDbTable." ;";
		$results = $this->dbConnection->fetchAll($query);

		if(! $results){
			$this->filloutTableDb();
		}else{ 			
			$this->processOrders();
		}
	}
	
	protected function processOrders()
	{
		echo "Processing Orders " . PHP_EOL;
		$order_collection = Mage::getModel('sales/order')->getCollection();
		$order_collection->addAttributeToFilter('store_id', array('eq' => $this->_getStoreCodeId()));
		$order_collection->addAttributeToFilter('status', array('eq' => 'complete'));
		echo $order_collection->getSelect()->__toString() . PHP_EOL;
		
		
		
		
		foreach($order_collection as $order){			
			//echo "Increment Order Id " . $order->getIncrementId() . PHP_EOL;
			$query = "SELECT * from ".$this->autoReplenishDbTable." WHERE increment_order_id = " . $order->getIncrementId() . ";";
			$results = $this->dbConnection->fetchOne($query);
			//echo $order->getIncrementId() . PHP_EOL;
			//print_r($results);
			
			if(! $results){
				echo "Order not found on the table:  " . $this->autoReplenishDbTable . PHP_EOL;
				echo $order->getIncrementId() . PHP_EOL;
			    
				if(! Mage::helper('alliance_fivehundredfriends/data')->getCustomerRestrictionBackend($order->getCustomerId()))
				{
					$response = $this->_recordSale($order);	
					if(! $response['success']){ 
						echo "This order is not UKR SaveRecord" . PHP_EOL;
						$this->_RecordValue($order, 2);
					}else{
						echo "This order is UKR SaveRecord" . PHP_EOL;
						$this->_RecordValue($order, 1);
					}
				}else{
					echo "This order has been restricted" . PHP_EOL;
					$this->_RecordValue($order, 3);
				}
				
			}
		}
	}
	
	
	protected function getCustomerGroupName($customer_id)
	{
		$customer  = Mage::getModel('customer/customer')->load($customer_id);
		$groupname = Mage::getModel('customer/group')->load($customer->getGroupId())->getCustomerGroupCode();
		
		return $groupname;	
	}
	
	
	protected function _recordSale($order) 
	{
		echo "_recordSale " . PHP_EOL;
		echo "Verify if customer and order is UKR" . PHP_EOL;
		//$customer_email = "armando@alliance-global.com";   // this is for test
        //$event_id     = "test_0002_".$order->getIncrementId(); // this is for test
		
		$customer_email = $order->getCustomerEmail();
        $event_id     	= $order->getIncrementId();
		$customer_id    = $order->getCustomerId();
        $type           = 'purchase_autoship';
        $value          = number_format($order->getGrandTotal() - $order->getTaxAmount(), 2);
        
        $items         = $order->getAllItems();
        $product_names = '';
        foreach ($items as $item) {
            $product_names .= $item->getName() . ', ';
        }
        $product_names = rtrim($product_names, ', ');
        $api = Mage::helper('alliance_fivehundredfriends/api');

        $request_parameters = array(
            'email'             => $customer_email,
            'type'              => $type,
            'value'             => $value,
            'event_id'          => $event_id,
            'detail'            => $product_names,
            'referral_tracking' => true,
        );

        foreach ($items as $i => $item) 
		{
            $product           = Mage::getModel('catalog/product')->load($item->getProductId());
            $categories_string = '';
            $first_category    = true;
            foreach ($product->getCategoryIds() as $category_id) 
			{
                if (!$first_category) $categories_string .= ',';
                $categories_string .= $category_id;
                $first_category = false;
            }
            $request_parameters["products[$i][name]"]       = $product->getName();
            $request_parameters["products[$i][url]"]        = $product->getProductUrl();
            $request_parameters["products[$i][product_id]"] = $product->getId();
            $request_parameters["products[$i][price]"]      = $product->getPrice();
            $request_parameters["products[$i][categories]"] = $categories_string;
            $request_parameters["products[$i][quantity]"]   = floatval($item['qty_ordered']);
        }
		
		$response = $api->record($request_parameters);
        return $response;
	}
	
	protected function filloutTableDb()
	{
		echo "Fillout ".$this->autoReplenishDbTable." table ".PHP_EOL;
		$order_collection = Mage::getModel('sales/order')->getCollection();
		//OrderGroove Store
		$order_collection->addAttributeToFilter('store_id', array('eq' => $this->_getStoreCodeId()));
		foreach($order_collection as $order){		
			$this->_RecordValue($order, 0);
		}
	}
	
	//  Status
	//	0 = Initial no Points
	//	1 = UKR member Rewarded
	//	2 = Not UK Member, not rewarded
	//  3 = Restricted
	
	protected function _RecordValue($order, $status = 0)
	{
		$RewardCreatedAt = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time())) . PHP_EOL;	
		$query = 	"INSERT IGNORE INTO " . $this->autoReplenishDbTable.
					" SET 
					increment_order_id = '".$order->getIncrementId()."', 
					customer_id = '".$order->getCustomerId()."', 
					customer_email = '".$order->getCustomerEmail()."',
					customer_group_name = '". $this->getCustomerGroupName($order->getCustomerId()) ."',
					order_created_at = '".$order->getCreatedAt()."',
					reward_created_at = '".$RewardCreatedAt."',
					status_order = '".$order->getStatus()."',
					status_reward = '".$status."'";		
	    $this->dbConnection->query( $query );
		
		echo "Order Saved " . $order->getIncrementId() . PHP_EOL;
		echo " ---------------------------  " . PHP_EOL;
	}
	
	//Sample JSON Success Response: {"success":true,"data":{"id":204575748,"points":750}}
	//Sample JSON Failure Response: {"success":false,"data":{"code":102,"message":"Invalid or missing email address."}}
		
	protected function build500FriendsUrl($customer_email, $customer_id)
	{
		
		$uuid = Mage::helper('alliance_fivehundredfriends')->getUserId();
		
		$params = array("uuid" => $uuid, 
				  "email" => $customer_email,
				  "external_customer_id" => $customer_id,
				  "type" => "auto-replenish");
		
		//Mage::log("Params : ".  print_r($params, true),null, '500friends_autoreplenish.log');		
		$data_url = "https://loyalty.500friends.com/api/record.json?".http_build_query($params);
		//Mage::log("Data URL : ".$data_url,null, '500friends_autoreplenish.log');
		
		return $data_url ;
	}
	
	protected function _getStoreCodeId()
	{
		$storeCode = $this->store_code;
		return Mage::getModel('core/store')->load($storeCode, 'code')->getId();
	}
	
}

$shell = new Alliance_Shell_Alliance_Autoreplenish_Rewardpoints();
$shell->run();
?>