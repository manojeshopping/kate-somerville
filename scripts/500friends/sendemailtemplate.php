<?php
/****************************************
 500 Friends  -> Ultimate Kate Rewards
****************************************/
$time_start = microtime(true);
require_once('../app/Mage.php');
set_time_limit(0);

class FiveHunderdFriends {

    private $session_url;
    public $soap; 
    public $db;
    private $mail;
    private $store;
    public $session;
    public $customer;
    public $app;
    public $total_friends;
    public function msg($msg, $type = "ok") {
      $sign = ($type === "e") ? "[ ERROR ] " : "[ OK ] ";
      echo $sign . $msg . "\r\n";
    }


// Sets to display all errors & tries to connect to the url
public function __construct( $magento_user, $magento_key, $magento_url ) {
  
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    $this->total_friends = 0;
	  umask(0);
	  $this->app  = Mage::app();
	  $this->store = $this->app->getStore();
	  $this->session = Mage::getSingleton('core/session');
	  $this->customer = Mage::getModel("customer/customer");
	  try {
	  $this->mail = Mage::getModel('core/email_template')->loadByCode('customer_merger')->getTemplateId();;
	  
	  } catch (Exception $e) {
	  $this->msg($e, "e");
	  }
	  $this->soap = new SoapClient( "http://" . $magento_url . "/api/soap/?wsdl"); 
    $session_id = $this->soap->login( $magento_user, $magento_key );
    
    if ($session_id) {
      $this->session_url = $session_id;
      $this->msg("Magento is connected");
    } else {
       $this->msg("Can't connect to the Magento URL", "e");
    }     
    
}

public function __destruct() {
$this->soap->endSession( $this->session_url );
$this->msg("Magento disconnected");
}

public function update( $customer_id ) {
$result = $this->soap->call($this->session_url, 'customer.update', array('customerId' => $customer_id, 'customerData' => array('group_id' => '14')));
if ($result) {
$this->msg("Customer with ID = " . $customer_id . " updated"); 
} else {

$this->msg("There was an error with customer: " . $customer_id, "e");
}

}

public function notify( $email , $pass ) {

                $vars = array();

                $vars['mail'] = $email;
                $vars['password'] = $pass;            
                
           
         
                
                $sender = array(
                'name' => "KateSomerville.com",
                'email' => "Info@KateSomerville.com");
               
                
               
              
              
                

try {
Mage::getModel('core/email_template')->sendTransactional($this->mail, $sender, $email, 'Valued Customer', $vars);

}
catch (Exception $e) {
$this->msg("Unable to send email to " . $email, "e");
}
$this->msg("Email sent to " . $email);

}
public function generateRandomPassword() {
  $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $password = substr( str_shuffle( $chars ), 0, 8 );
    return $password;
}
public function updateUser ($customerID, $firstname, $lastname ) {

$result = $this->soap->call($this->session_url, 'customer.update', array(
                          'customerId' =>  $customerID, 
                          'customerData' => array(
                          'firstname' => $firstname, 
                          'lastname' => $lastname
                          )));
    $this->msg("Customer " . $customerID . " updated " . $firstname . " " . $lastname);                      
}
public function create( $email ) {
$pass = $this->generateRandomPassword();




try {
$result = $this->soap->call($this->session_url, 'customer.create', array(
array('email' => $email, 
      'password' => $pass,
      'website_id' => 1, 
      'store_id' => 1, 
      'group_id' => 14
      )));
$this->msg("User " . $email . " created with password " . $pass);
return $result;
// $this->notify($email, $pass);
}
catch (Exception $e) {
$this->msg("Unable to create user " . $email, "e");
}

}

// Get all customers
public function getCustomersList() {
$this->db = $this->customer->getCollection()->addAttributeToSelect('*')->addFieldToFilter('group_id', 1);
$this->total_friends += sizeof($this->db);
return $this->db;
}
public function checkEmail($email) {
$this->customer->loadByEmail($email);
$resp = $this->customer->getData(); 
return $resp;
}


// Generated a URL 
public function createRoyalUrl($page) {
  $secret_key = "Ivz3YtnPBXmoC6Nr0QmqT313PjuqMAXQ";
  $params = array("uuid" => "cUhBmYAmzWK4gqO", "page_numbe" => $page);
  ksort($params);
  $string_to_hash = $secret_key;
  foreach ($params as $key => $val) {
        $string_to_hash .= $key.$val;
  }
  $params["sig"] = md5($string_to_hash);
  $api_url = "https://loyalty.500friends.com/data/customers?";
  foreach ($params as $key => $val) {
       $api_url .= "$key=".urlencode($val)."&";
  }
  $this->msg("500 Friends URL is generated");
  return $api_url; // Url of the api call.
}




// Makes a request to 500 Friends
public function getRoyalUsers($page) {
/*
{ "data":[
    {"name":"daiglesm@roadrunner.com",
      "unsubscribed":false,
      "created_at":"2013-07-16T15:58:30-07:00",
      "last_activity":"2013-07-16T16:07:12-07:00",
      "image_url":null,
      "updated_at":"2013-07-16T16:07:12-07:00",
      "id":3788828,
      "lifetime_balance":150,
      "status":"active",
      "email":"daiglesm@roadrunner.com",
      "balance":150,
      "top_tier_name":null
      }

*/
$link = $this->createRoyalUrl($page);
$c = curl_init($link);	
		
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_URL, $link);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 2);
	
	  $resp = curl_exec($c);
	
	  $parsed_resp  = json_decode($resp, true);
    if ($parsed_resp) {
     $this->msg("500 Users are retrived");
     $this->total_friends += sizeof($parsed_resp['data']);
    } else {
     $this->msg("Can't connect to 500", "e");
    }
	return $parsed_resp;
}

}





$mage_url = 'katesomerville.dev.alliance-global.com'; 

$mage_user = 'Pavel'; 
$mage_api_key = 'verysecretkey'; 

$id = new FiveHunderdFriends($mage_user, $mage_api_key, $mage_url);
$id->notify("armando@alliance-global.com", $id->generateRandomPassword());
$id->notify("char@alliance-global.com", $id->generateRandomPassword());
$id->notify("pavel@alliance-global.com", $id->generateRandomPassword());

/*
$i = 1;
$count_updated = 0;
$count_created = 0;
while ($i < 5) {
$rawdata = $id->getRoyalUsers($i++);

$data = $rawdata['data'];

// Parse Royal Emails; 
// $db = $id->getCustomersList(); 


foreach ($data as $entry) {
    $resp = $id->checkEmail($entry['email']);
   if ($resp) {
   // Update group
   if ($resp['group_id'] != 14) {
      $id->update($resp['entity_id']);
      $count_updated++;
      } else {
      
      $id->msg("Customer " . $resp['entity_id'] . " is already in UKR.");
      }
   } else {
   $id->create($entry['email']);
   $count_created++;
   }
}


}









$users_toupdate = 0;
$users_with_name = 0;
$total_users = 0;

$row = 1;
if (($handle = fopen("test.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
   
       $row++;

      
       $resp = $id->checkEmail($data[0]);   // query user from magento DB
       
        if ($resp) {
        // If users exists 
        
              if ($resp['firstname']) {
                  $users_with_name++;
                  $id->msg($resp['firstname'] . " exists and has a name");
              } else {

                  $users_toupdate++;
                  $id->updateUser($resp['entity_id'], $data[1], $data[2]);
              }
       $total_users++;
       
       } else {
       // Create user
       $customerID = $id->create($data[0]);
       $id->updateUser($customerID, $data[1], $data[2]);
       }
       
       
     /*
          
 
      try {
      
       
           $countryCode = ($data[2] == "") ? "US" : $data[2];
           $regionModel = Mage::getModel('directory/region')->loadByCode($data[5], $countryCode);
           $regionId = $regionModel->getId();
       
  
       
        $customerID =  $resp['entity_id'];
        
        $result = $client->call($session, 'customer.update', array(
        'customerId' =>  $customerID, 
        'customerData' => array(
          'firstname' => $data[1], 
          'lastname' => $data[2]
          )));
        
        
        $result = $client->call($session,'customer_address.create', array(
        'customerId' => $customerID, 
        'addressdata' => array(
          'firstname' => $data[1], 
          'lastname' => $data[2], 
          'street' => array($data[3]), 
          'city' => $data[4], 
          'country_id' => $countryCode, 
          'region' => $data[5], 
          'region_id' => $region_id, 
          'postcode' => $data[6], 
          'telephone' => $data[7], 
          'is_default_billing' => FALSE, 
          'is_default_shipping' => FALSE)));
          
          $users_updated++;
          } catch (Exception $e) {
           $id->msg($e, "e");
      
          }
        */  
        /*
    
    
    }
    fclose($handle);
    }













$id->msg($users_toupdate . " / " . $total_users . " customers need to be updated");
$id->msg($users_with_name . " / " . $total_users . " already have name");



/*
$id->msg($count_updated . " users were updated");
$id->msg($count_created . " users were created");
$id->msg($id->total_friends . " counts");


*/

/*
$time_end = microtime(true);
$execution_time = ($time_end - $time_start)/60;
$id->msg("Total execution time: " . $execution_time);

*/

?>
