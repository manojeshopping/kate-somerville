<?php


 	// Magento 
	require_once('../app/Mage.php');
	umask(0);
	$app = Mage::app();
	$customer 	= Mage::getModel("customer/customer");
	
	///




	$x_api_url_events = "https://loyalty.500friends.com/data/events?";
	
	/* Dev account
	$x_account_id 	= "5n0NMbnnL1M9Nbo";		           // test account id 
	$x_secret_key 	= "16ecGS1lB64aT5G2R1o1RrkkeUO5Pztb";  // test secret key
	*/

	$x_account_id 	= "cUhBmYAmzWK4gqO";		           // test account id 
	$x_secret_key 	= "Ivz3YtnPBXmoC6Nr0QmqT313PjuqMAXQ";  // test secret key


	//$x_after_date 	= date("Ymd"); 
	//$x_before_date 	= date("Ymd",strtotime("+1 days"));
	$x_after_date 	= ""; 
	$x_before_date 	= "";

	//$x_after_date 	= date("Ymd",strtotime("-1 days"));  
	//$x_before_date 	= date("Ymd");

       $x_type 	  	= "tier";
	
	$api_url		   = "";	  
	$x_generate_sigkey = "";
	
	$x_customer_group_tier1 = 6;


	$client  = new SoapClient('http://katesomerville.com/api/soap/?wsdl');
	//$session = $client->login('armandroid', '2gk04LisLV');
	$session = $client->login('alliance', '#daf2a668eeaff474b1bcea3d07ef41ea!_');



Mage::log("-------------------------------------------------------------", null, "500friends_events.log" );
Mage::log("-------------------------------------------------------------", null, "500friends_events.log" );

Mage::log(" --- 500friends --- ", null, "500friends_events.log" );



//Ultimate Kate Rewards_Tier 1



function GenerateSecretKeyEvents( $id, $secret_key, $type="", $after_date="", $before_date="" ){
	
		$x_generate_sigkey 	= "";
		$x_md5_key 		= "";
	
		$x_account_id 	=  $id;
		$x_secret_key 	=  $secret_key;
	
		$x_type 	  	= $type;
		$x_after_date 	= $after_date; 
		$x_before_date  	= $before_date;
	
		$x_generate_sigkey .= $x_secret_key;  
			if(!empty($x_after_date))  $x_generate_sigkey .= "after_date".$x_after_date;
			if(!empty($x_before_date)) $x_generate_sigkey .= "before_date".$x_before_date;
			if(!empty($x_type))  $x_generate_sigkey .= "type".$x_type;
		$x_generate_sigkey .= "uuid".$x_account_id;
		
		$x_md5_key = md5($x_generate_sigkey);
	

	Mage::log("Key: ".$x_md5_key, null, "500friends_events.log" );

	return  $x_md5_key;
}

	
function GenerateAPIURLEvents( $id, $md5_key, $api_url_events, $type="", $after_date="", $before_date="" ){

		$x_api_url 	  	= "";
	
		$x_account_id 	= $id;
		$x_md5_key 		= $md5_key;
	
		$x_api_url_events 	= $api_url_events;
	
		$x_type 	  	= $type;
		$x_after_date 	= $after_date; 
		$x_before_date  	= $before_date;
	
		$x_api_url .= $x_api_url_events;
			if(!empty($x_after_date))   $x_api_url .= "after_date=".$x_after_date."&";
			if(!empty($x_before_date))  $x_api_url .= "before_date=".$x_before_date."&";
		$x_api_url .= "uuid=".$x_account_id."&";
		$x_api_url .= "type=".$x_type."&";
		$x_api_url .= "sig=".$x_md5_key;


	Mage::log("API_URL: ".$x_api_url, null, "500friends_events.log" );	
	return  $x_api_url;
}

function Get500FriendsResp( $api_url ){
	
	$x_api_url = $api_url;
	$c = curl_init($x_api_url);	
		
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_URL, $x_api_url);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 2);
	
	$resp = curl_exec($c);
	
	$parsed_resp  = json_decode($resp, true);
	

	$parsed_resp_log = print_r($parsed_resp, true);
	Mage::log("500friend_resp: ".$parsed_resp_log, null, "500friends_events.log" );	


	return $parsed_resp;
}

function GetCustomerTierList( $z_get_500_friends_resp  ){
	$z_get_resp 		= $z_get_500_friends_resp;
	$flag_resp		= $z_get_resp["success"];
	$cont_resp		= count($z_get_resp["data"]);	
	$z_customer_email 	= array();

	if( $flag_resp === true && $cont_resp>0  ){

			for($i=0; $i < $cont_resp; $i++   ){
				$x_email = $z_get_resp["data"][$i]["email"];
				if($z_get_resp["data"][$i]["detail"] == "Earned a Tier 1 tier"   ){
					$z_customer_email[$i]["email"] = $x_email;
					$z_customer_email[$i]["detail"] = $z_get_resp["data"][$i]["detail"];
				}
			}


			for($i=0; $i < $cont_resp; $i++   ){
				$x_email = $z_get_resp["data"][$i]["email"];
				if($z_get_resp["data"][$i]["detail"] == "Earned a Tier 2 tier"   ){
					$z_customer_email[$i]["email"] = $x_email;
					$z_customer_email[$i]["detail"] = $z_get_resp["data"][$i]["detail"];
				}
			}

			for($i=0; $i < $cont_resp; $i++   ){
				$x_email = $z_get_resp["data"][$i]["email"];
				if($z_get_resp["data"][$i]["detail"] == "Earned a Tier 3 tier"   ){
					$z_customer_email[$i]["email"] = $x_email;
					$z_customer_email[$i]["detail"] = $z_get_resp["data"][$i]["detail"];
				}
			}


			for($i=0; $i < $cont_resp; $i++   ){
				$x_email = $z_get_resp["data"][$i]["email"];
				if($z_get_resp["data"][$i]["detail"] == "Earned a Tier 4 tier"   ){
					$z_customer_email[$i]["email"] = $x_email;
					$z_customer_email[$i]["detail"] = $z_get_resp["data"][$i]["detail"];
				}
			}
	

		$z_customer_email_log = print_r($z_customer_email, true);
		Mage::log("Customer Email list: ".$z_customer_email_log, null, "500friends_events.log" );	


		if( count($z_customer_email) > 0 ){
			 $z_customer_email = array_values($z_customer_email);
			 return $z_customer_email;		
		}else{ 
			return false;
		}	
	}else{
		return false;
	}	
}
	 




function GetCustomerIDList( $customer_email_list, $customer_tier_group_id ){ 
	
	global $app;
	global $customer;


	$z_customer_id 	    = array();
	$z_customer_email_list   = $customer_email_list;
	$x_customer_tier_group_id= $customer_tier_group_id;
	
	$j=0;
		for( $i=0; $i < count( $z_customer_email_list ); $i++ ){
			
			$customer_email = $z_customer_email_list[$i]['email'];
			$customer->loadByEmail($customer_email);
			$customer->getGroupId();
			
			$group = Mage::getModel('customer/group')->load($customer->getGroupId());
			$x_customer_group_name = $group->getCode();
			
			
			Mage::log("Customer EMAIL-ID : ".$customer_email." - ".$customer->getId(), null, "500friends_events.log" );	
	

			if( $customer->getId() != "" ){
				Mage::log("Customer EMAIL-ID : ".$customer_email." - ".$customer->getId(), null, "500friends_events.log" );	
				/*if( $customer->getGroupId() != $x_customer_tier_group_id ){			
					$z_customer_id[$j] = $customer->getId();		
					$j++;
				}*/
					$z_customer_id[$j]['id']    	= $customer->getId();		
					$z_customer_id[$j]['email'] 	= $customer_email;
					$z_customer_id[$j]['detail']	= $z_customer_email_list[$i]['detail'];
					$z_customer_id[$j]['current_group']= $x_customer_group_name;

					$j++;
	
			}else{
				Mage::log("Not found - Customer EMAIL-ID : ".$customer_email." - ".$customer->getId(), null, "500friends_events.log" );	
			}
		}
	
	$z_customer_id_log = print_r($z_customer_id, true);
	Mage::log("Customer id list: ".$z_customer_id_log, null, "500friends_events.log" );	

	return 	$z_customer_id;
}








function UpdateCustomerTierGroupAPI( $customer_id_list, $customer_tier_group_id ){

	global $client;
	global $session;
	$z_result = array();
	
	$z_customer_list = $customer_id_list;
	$x_customer_tier_group_id = $customer_tier_group_id;


	Mage::log("Update Customer Tier Group ", null, "500friends_events.log" );	


	$customer_group 	= new Mage_Customer_Model_Group();
	$allGroups  		= $customer_group->getCollection()->toOptionHash();
	$i=0;
	foreach($allGroups as $key=>$allGroup){
		$customerGroup[$i]=array('id'=>$key, 'group_name'=>$allGroup);
	$i++;
	}


	$z_result = "";


	for($i=0; $i<count( $z_customer_list ); $i++ ){
		//$result = $client->call($session, 'customer.update', array('customerId' => $z_customer_id[$i], 'customerData' => array(  'group_id' => $x_customer_tier_group_id  )));
		//$z_result[$i] = $result;	
	
	     	$x_customer_id 	= $z_customer_list[$i]['id']; 
            	$x_customer_email 	= $z_customer_list[$i]['email'];
            	$x_customer_detail 	= $z_customer_list[$i]['detail'];
            	$x_customer_group 	= $z_customer_list[$i]['current_group'];

		echo "\n ID: ".$x_customer_id;	
		echo "\n email: ".$x_customer_email;
		echo "\n Detail: ".$x_customer_detail;
		echo "\n Group: ".$x_customer_group;
		
		switch( $x_customer_detail ){
			case "Earned a Tier 1 tier":
				echo "\n tier 1";	
				if( $x_customer_group == 'Ultimate Kate Rewards_Tier 1' || $x_customer_group == 'Ultimate Kate Rewards_Tier 2' || $x_customer_group == 'Ultimate Kate Rewards_Tier 3' || $x_customer_group == 'Ultimate Kate Rewards_Tier 4'  ){
					echo "\n error customer group";
					break;
				}else{
					for( $j=0; $j <= count($customerGroup); $j++ ){
						
						if($customerGroup[$j]['group_name'] == 'Ultimate Kate Rewards_Tier 1'){
							echo "\n Customer group name: ".$customerGroup[$j]['group_name'];
							echo "\n Customer group: ".$customerGroup[$j]['id'];
							echo "\n Print new customer group";
							Mage::log("TIER_1 : ".$x_customer_id." - ".$x_customer_email, null, "500friends_events.log" );
							$z_new_customer_group = $customerGroup[$j]['id'];
							$result = $client->call($session, 'customer.update', array('customerId' => $x_customer_id, 'customerData' => array(  'group_id' => $z_new_customer_group )));
							$z_result[$i] = $result;	

            					}

					}	
				}
			break;


			case "Earned a Tier 2 tier":
				echo "<br> tier 2";
				if( $x_customer_group == 'Ultimate Kate Rewards_Tier 2' || $x_customer_group == 'Ultimate Kate Rewards_Tier 3' || $x_customer_group == 'Ultimate Kate Rewards_Tier 4'  ){
					echo "<br> error customer group";
					break;
				}else{
					for( $j=0; $j <= count($customerGroup); $j++ ){
						
						if($customerGroup[$j]['group_name'] == 'Ultimate Kate Rewards_Tier 2'){
							echo "\n Customer group name: ".$customerGroup[$j]['group_name'];
							echo "\n Customer group: ".$customerGroup[$j]['id'];
							echo "\n Print new customer group";
							Mage::log("TIER_2 : ".$x_customer_id." - ".$x_customer_email, null, "500friends_events.log" );
							$z_new_customer_group = $customerGroup[$j]['id'];
							$result = $client->call($session, 'customer.update', array('customerId' => $x_customer_id, 'customerData' => array(  'group_id' => $z_new_customer_group )));
							$z_result[$i] = $result;
            					}

					}	
				}


			break;







			case "Earned a Tier 3 tier":
				echo "\n tier 3";
				if( $x_customer_group == 'Ultimate Kate Rewards_Tier 3' || $x_customer_group == 'Ultimate Kate Rewards_Tier 4'  ){
					echo "\n error customer group";
					break;
				}else{
					for( $j=0; $j <= count($customerGroup); $j++ ){
						
						if($customerGroup[$j]['group_name'] == 'Ultimate Kate Rewards_Tier 3'){
							echo "\n Customer group name: ".$customerGroup[$j]['group_name'];
							echo "\n Customer group: ".$customerGroup[$j]['id'];
							echo "\n Print new customer group";
							Mage::log("TIER_3 : ".$x_customer_id." - ".$x_customer_email, null, "500friends_events.log" );
							$z_new_customer_group = $customerGroup[$j]['id'];
							$result = $client->call($session, 'customer.update', array('customerId' => $x_customer_id, 'customerData' => array(  'group_id' => $z_new_customer_group )));
							$z_result[$i] = $result;
            					}

					}	
				}

			break;

			case "Earned a Tier 4 tier":
				echo "\n tier 4";
				if( $x_customer_group == 'Ultimate Kate Rewards_Tier 4'  ){
					echo "\n error customer group";
					break;
				}else{
					for( $j=0; $j <= count($customerGroup); $j++ ){
						
						if($customerGroup[$j]['group_name'] == 'Ultimate Kate Rewards_Tier 4'){
							echo "\n Customer group name: ".$customerGroup[$j]['group_name'];
							echo "\n Customer group: ".$customerGroup[$j]['id'];
							echo "\n Print new customer group";
							Mage::log("TIER_4 : ".$x_customer_id." - ".$x_customer_email, null, "500friends_events.log" );
							$z_new_customer_group = $customerGroup[$j]['id'];
							$result = $client->call($session, 'customer.update', array('customerId' => $x_customer_id, 'customerData' => array(  'group_id' => $z_new_customer_group )));
							$z_result[$i] = $result;
            					}

					}	
				}

			break;
			
			default;
				echo "\n error customer";
			break;
		}
		
		echo "\n \n";

	/*
	$z_new_customer_group = 1;
	$result = $client->call($session, 'customer.update', array('customerId' => $x_customer_id, 'customerData' => array(  'group_id' => $z_new_customer_group )));
	$z_result[$i] = $result;		
	*/
	}
	

	$client->endSession($session);
       $z_result_log =  print_r($z_result, true);
	Mage::log("Update Customer Tier Group Result: ".$z_result_log , null, "500friends_events.log" );	
	return $z_result;
}

	
	



	$getkey  = GenerateSecretKeyEvents($x_account_id, $x_secret_key, $x_type , $x_after_date, $x_before_date ); 	
	$api_url = GenerateAPIURLEvents( $x_account_id, $getkey, $x_api_url_events, $x_type, $x_after_date, $x_before_date  );
	$z_get_500_friends_resp = Get500FriendsResp( $api_url );
	$z_customer_tier_list = GetCustomerTierList($z_get_500_friends_resp);
	$z_customer_id_list = GetCustomerIDList( $z_customer_tier_list,  $x_customer_group_tier1);
	$z_result = UpdateCustomerTierGroupAPI( $z_customer_id_list, $x_customer_group_tier1 );



/*
echo "<br> GetKey: ";
echo "<pre>";
print_r($getkey);
echo "</pre>";

echo "<br> ApiURL: ";
echo "<pre>";
print_r($api_url);
echo "</pre>";

echo "<br> Get 500 friends resp: ";
echo "<pre>";
print_r($z_get_500_friends_resp);
echo "</pre>";	

echo "<br> Customer tier list: ";
echo "<pre>";
print_r($z_customer_tier_list);
echo "</pre>";	

echo "<br> Customer id list: ";
echo "<pre>";
print_r($z_customer_id_list);
echo "</pre>";
*/
/*
$customer_group 	= new Mage_Customer_Model_Group();
$allGroups  		= $customer_group->getCollection()->toOptionHash();
$i=0;
foreach($allGroups as $key=>$allGroup){
	$customerGroup[$i]=array('id'=>$key, 'group_name'=>$allGroup);
$i++;
}

echo "<pre>";
print_r($customerGroup);
echo "</pre>";
*/



/*
echo "<br> Result: ";
echo "<pre>";
print_r($z_result);
echo "</pre>";
*/

Mage::log(" --- END --- ", null, "500friends_events.log" );
Mage::log("-------------------------------------------------------------", null, "500friends_events.log" );
	
/////////////////////////////////////////////////////////////////////////
///Magento/////////////////////////////////

/*
echo"<pre>";
print_r($z_customer_tier_list);
echo"</pre>";

echo "<br> id_list ";
echo "<pre>";
print_r($z_customer_id_list);
echo "</pre>";

echo "<br> response ";
echo "<pre>";
print_r($z_result);
echo "</pre>";
*/

?>
