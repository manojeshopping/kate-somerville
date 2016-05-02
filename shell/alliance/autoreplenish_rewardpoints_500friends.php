<?php 
/*
require_once dirname(dirname(__FILE__)) . '/abstract.php';

class Alliance_Shell_Alliance_Autoreplenish_Rewardpoints_500friends extends Mage_Shell_Abstract 
{
	public $dbConnection;
	public $autoReplenishDbTable;
	
	public function run()
	{
		echo "Started : Rewarding AutoReplenish Points".PHP_EOL;
		$this->establishDatabaseConnection();
		$this->reward500FriendsPoints();
		echo "Completed : Rewarding AutoReplenish Points".PHP_EOL;
	}
	
	public function establishDatabaseConnection()
	{
		$this->dbConnection = Mage::getSingleton( 'core/resource' )->getConnection( 'core_write' ); // To write to the database
		$this->autoReplenishDbTable =  Mage::getSingleton( 'core/resource' )->getTableName( 'autoreplenish/autoreplenish' );
	}
	
	protected function reward500FriendsPoints() 
	{
		$query = "SELECT * from ".$this->autoReplenishDbTable." WHERE loyalty_points = 0 AND status = 1";
		$results = $this->dbConnection->fetchAll($query);
		
		for($i=0;$i < count($results);$i++) {
			
			$customer_email = Mage::getModel('customer/customer')->load($results[$i]['customer_id'])->getEmail();
			
			$url = $this->build500FriendsUrl($customer_email, $results[$i]['customer_id']);
			$json = file_get_contents($url);
			$json_response = json_decode($json);
			$response_success = $json_response->success;
			
			Mage::log("Customer Email : ".$customer_email." ; Customer ID : ".$results[$i]['customer_id'],null, '500friends_autoreplenish.log');

			if ($response_success) {
			
				$response_id = $json_response->data->id;
				$response_points = $json_response->data->points;
				
				Mage::log("Success : true",null, '500friends_autoreplenish.log');
				Mage::log("ID : ".$response_id,null, '500friends_autoreplenish.log');
				Mage::log("Points : ".$response_points,null, '500friends_autoreplenish.log');
				
				$query = "UPDATE ".$this->autoReplenishDbTable." SET loyalty_points = 1 WHERE autoreplenish_id = ".$results[$i]['autoreplenish_id'];
				$this->dbConnection->query( $query );
				
			} else {
			
				$response_code = $json_response->data->code;
				$response_message = $json_response->data->message;
				
				Mage::log("Success : false",null, '500friends_autoreplenish.log');
				Mage::log("Code : ".$response_code,null, '500friends_autoreplenish.log');
				Mage::log("Message : ".$response_message,null, '500friends_autoreplenish.log');
				
				if ($customer_email != null && ( $response_message == "Invalid or missing email address." || $response_code == 102 )) {
					Mage::log("Customer with ID : ".$results[$i]['customer_id']." & Email Address : ".$customer_email." doesn't exist in 500friends.",null, '500friends_autoreplenish_failure.log');
					Mage::log("--------------------------------------------------------",null, '500friends_autoreplenish_failure.log');
					$query = "UPDATE ".$this->autoReplenishDbTable." SET loyalty_points = 2 WHERE autoreplenish_id = ".$results[$i]['autoreplenish_id'];
					$this->dbConnection->query( $query );
				}
			}
			
			//Mage::log("--------------------------------------------------------",null, '500friends_autoreplenish.log');
		}
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
		
		Mage::log("Params : ".  print_r($params, true),null, '500friends_autoreplenish.log');		
		$data_url = "https://loyalty.500friends.com/api/record.json?".http_build_query($params);
		Mage::log("Data URL : ".$data_url,null, '500friends_autoreplenish.log');
		
		return $data_url ;
	}
}
$shell = new Alliance_Shell_Alliance_Autoreplenish_Rewardpoints_500friends();
$shell->run();
	*/