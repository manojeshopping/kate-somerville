<?php
require_once('../../app/Mage.php');  // Update Path on live
set_time_limit(0);

function generate_url( $action, $page ) {
    $params = array("uuid" => 'cUhBmYAmzWK4gqO', 'type' => 'tier', "page_number" => $page); // 
    ksort($params);
    $string_to_hash = 'Ivz3YtnPBXmoC6Nr0QmqT313PjuqMAXQ';
    foreach ($params as $key => $val){
        $string_to_hash .= $key.$val;
    }
    $params["sig"] = md5($string_to_hash);
    $api_url = "https://loyalty.500friends.com/data/". $action . "?";
    foreach ($params as $key => $val) {
         $api_url .= "$key=".urlencode($val)."&";
    }
	
	Mage::log("URL 500friends: ".$api_url, null, "500friends_movetopremier.log" );
    return $api_url; // Url of the api call.
}

   // Retrive 500 Freinds users for give page
function get_royal_customers($action, $page) {
    $link = generate_url($action, $page);
    $c = curl_init( $link );	
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($c, CURLOPT_URL, $link);
	curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 2);
    $resp = curl_exec($c);
    if (!$resp) {
      echo "Can't connect to 500 Friends API for page " . $page .  "\r\n";
    }
    $decoded_resp = json_decode($resp, true);
	  return $decoded_resp['data'];
}

$app        = Mage::app();
$store      = Mage::app()->getStore();
$customer   = Mage::getModel("customer/customer");
$group      = Mage::getModel('customer/group');

// SETTINGS
$generalGroup = 1; //General
$fbsampleGroup = 8; // Facebook Sample
$targetGroup = 6;  //Ultimate Kate Premier
$originGroup = 14; //Ultimate Kate Rewards
$employeeGroup = 9;   //Employee
$friendGroup =  10;   // Friends & Family




$i = 1;
$count = 0;
$total_users = 0;
$royaldb = array();

Mage::log("------------------------------------------------------------------", null, "500friends_movetopremier.log" );

while (true) {
  $data =  get_royal_customers('events', $i++);
if (count($data) == 0) break;
foreach ($data as $user) {
    $total_users++;	
	Mage::log(" -- Customer:  ".$user['detail']." - ".$user['email']." - ". $user['customer_id'], null, "500friends_movetopremier.log" );
    if (strpos($user['detail'], 'Ultimate Kate Premier') !== false || strpos($user['detail'], 'Tier 1') !== false) { 
		$customer->reset();
        if ($customer->loadByEmail( $user['email'] )) {
          $customer_data = $customer->getData();
			if ($customer_data['group_id'] <> $employeeGroup and $friendGroup){
				echo "\n  Email: ".$user['email'];	
				if ($customer_data['group_id'] == $originGroup || $customer_data['group_id'] == $generalGroup || $customer_data['group_id'] == $fbsampleGroup ){
					$customer->setGroupId( $targetGroup );
						try {
							$customer->save();
							$customer->setConfirmation(null);
							$customer->save();
							Mage::log("[ OK ] Customer " . $user['email'] . " was updated.", null, "500friends_movetopremier.log" );
							$count++;
						} catch (Exception $ex) {
							echo "Can't update customer " . $user['email'] . "\r\n";
							Mage::log("[ ERROR ] Customer " . $user['email'] . " was not updated: " . $ex->getMessage(), null, "500friends_movetopremier.log" );
						}
				}
			}else{  
				echo "\n Employee: ".$user['email'];	  
			} 
		}
	}
    // If nothing retrived
}
}
echo "Total updated: " . $count . " / " . $total_users . "\r\n";
/*
  $collection = $customer->getCollection()->addAttributeToSelect('*')->addFieldToFilter('group_id', $originGroup);
  
  foreach ($collection as $entry) {
    $customer = $entry;
    $customer->getData();
  }
*/  
  
   function msg( $msg ) {
    echo '* ' . $msg . "\r\n";
  }

  
?>
