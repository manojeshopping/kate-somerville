<?php
/****************************************
 500 Friends  -> Ultimate Kate Rewards
****************************************/
$time_start = microtime(true);

require_once('../app/Mage.php');

set_time_limit(0);
/*

  STEP 1 - Import users from 500 friends
  STEP 2 - Update first and last name for freshly created users
  STEP 3 - Send emails to freshly created users

*/


class ukr {

  
  private $app;                 // Mage App Init
  private $store;               // Current store
  private $session;             // Session model
  private $customer;            // Customer 
  private $mail;                // Email model
  private $template_id;         // Email template id
  public $group_id;             // Ultimate Kate Rewards group ID
  private $keys = array();      // Array with all login info
  public $xls_users = array();  // DB of users from XLS files
  public $counters = array();   // Counters
  
  
  // Create all required instances
  public function __construct() {
  	  $this->app  = Mage::app();
	    $this->store = $this->app->getStore();
	    $this->session = Mage::getSingleton('core/session');
	    $this->customer = Mage::getModel("customer/customer");
	    $this->empty_customer = $this->customer;
	    $this->customer->setWebsiteId($this->app->getWebsite()->getId());
	    $this->group_id = 14;
	    // Using Template "Customer Merger"
	    $this->mail = Mage::getModel('core/email_template'); 
	    $this->template_id = $this->mail->loadByCode('customer_merger')->getTemplateId();
	    
	    // Set up all keys
	    $this->keys = array(
          '500FriendsSecret' => 'Ivz3YtnPBXmoC6Nr0QmqT313PjuqMAXQ',
          '500FriendsUUID'   => 'cUhBmYAmzWK4gqO'
      );
      $this->counters = array(
          'customers_created' => 0,
          'customers_updated' => 0,
          'customers_skipped' => 0
          );
      $this->populate_xls_user();
  }
  public function display_counters() {
    $total  = $this->counters['customers_created'] + $this->counters['customers_updated'] + $this->counters['customers_skipped'];
    $this->msg("Customers created: " . $this->counters['customers_created'] . " / " . $total);
    $this->msg("Customers updated: " . $this->counters['customers_updated'] . " / " . $total);
    $this->msg("Customers skipped: " . $this->counters['customers_skipped'] . " / " . $total);
  }
  // Read CSV files and create one big array
  public function populate_xls_user() {
  // TEST1.CSV
    if (($handle = fopen("test1.csv", "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
           $this->xls_users[$data[0]]['firstname'] = $data[1];
           $this->xls_users[$data[0]]['lastname'] = $data[2];
        }
    }
  // TEST2.CSV
    if (($handle = fopen("test2.csv", "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
         $this->xls_users[$data[0]]['firstname'] = $data[8];
         $this->xls_users[$data[0]]['lastname'] = $data[9];
      }
    }
    $this->msg("XLS is populated");
  }
  
  // Exit properly
  public function __deconstruct() {
    $this->msg("Magento disconnected");
   }
   
  // Clear model
  public function clear_model() {
    $this->customer->loadByEmail("");
  }
  // Update existing Magneto customer
  public function update_customer($group_only = false, $email) {
    $this->customer->setGroupId($this->group_id);
    try {
      $this->customer->save();
      $this->customer->setConfirmation(null);
      $this->customer->save();
      $this->msg("UPDATED: Customer " . $email . " has been updated"); 
      $this->counters['customers_updated']++;
    } catch (Exception $ex) {
    $this->msg("ERROR: Can't update customer with email " . $email);  
    }
  }
  
  // Create new customer
  public function create_customer( $email, $firstname = "", $lastname = "") {
    $password = $this->create_password();
    $this->customer->setEmail( $email );
    $this->customer->setFirstname( $firstname );
    $this->customer->setLastname( $lastname );
    $this->customer->setPassword( $password );
    try {
      $this->customer->save();
      $this->customer->setConfirmation(null);
      $this->customer->save();
      // Notify User
      // $this->notify_customer($email, $password, $firstname . " " . $lastname);
      $this->msg("CREATED: Customer " . $email . " has been created with password " . $password . " & name " . $firstname . " " . $lastname); 
      $this->counters['customers_created']++;
    } catch (Exception $ex) {
    $this->msg("ERROR: Can't create customer with email " . $email);  
    }
  }
  
  // Send an email to the customer
  public function notify_customer( $email, $pass, $name ) {
    $vars = array();
    $vars['name'] = ($name != "") ? $name : 'Valued customer';
    $vars['mail'] = $email;
    $vars['password'] = $pass;            
    $sender = array(
      'name' => "KateSomerville.com",
      'email' => "Info@KateSomerville.com"
      );
    try {
      $this->mail->sendTransactional($this->template_id, $sender, $email, $vars['name'], $vars);
      $this->msg("EMAIL: Email sent to " . $email);
    } catch (Exception $e) {
      $this->msg("ERROR: Unable to send email to " . $email);
    }
  }
  
  // Generate random password
  public function create_password() {
    return substr( str_shuffle( "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789" ), 0, 8 );
  }
  
  // Get a user by email
  public function get_customer( $email ) {
    $this->customer->loadByEmail($email);
    return $this->customer->getData(); 
  }
  
  // Message
  public function msg( $msg ) {
    echo '* ' . $msg . "\r\n";
  }
  
  // Generate 500 Friends URL for any given page
  public function generate_url( $page ) {
    $params = array("uuid" => $this->keys['500FriendsUUID'], "page_number" => $page);
    ksort($params);
    $string_to_hash = $this->keys['500FriendsSecret'];
    foreach ($params as $key => $val) {
          $string_to_hash .= $key.$val;
    }
    $params["sig"] = md5($string_to_hash);
    $api_url = "https://loyalty.500friends.com/data/customers?";
    foreach ($params as $key => $val) {
         $api_url .= "$key=".urlencode($val)."&";
    }
    return $api_url; // Url of the api call.
  }
  
  // Retrive 500 Freinds users for give page
  public function get_royal_customers($page) {
    if (!function_exists("curl_init")) {
      $this->msg('Install php_curl');
      return false;
    }
    $link = $this->generate_url($page);
    $c = curl_init( $link );	
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_URL, $link);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 2);
    $resp = curl_exec($c);
    
    if ($resp) {
     $this->msg("500 Friends users are retrived for page " . $page);
    } else {
     $this->msg("Can't connect to 500 Friends API for page " . $page);
    }
    $decoded_resp = json_decode($resp, true);
    $this->msg(count($decoded_resp));
	  return $decoded_resp['data'];
  }

}



// CLINET

$ukr = new ukr();

// Iterate through all pages of 500 Freinds

for ($page = 1; $page < 5; $page++) {
  $data = $ukr->get_royal_customers($page);
  
  // For each user in 500 Friends
  foreach ($data as $entry) {
   // Load user by email
   $magento_user = $ukr->get_customer($entry['email']);
     
   // If user exists, update group
   if ($magento_user) {
       if ($magento_user['group_id'] != $ukr->group_id) {
        $ukr->update_customer(true, $entry['email']);
      } else {
        $ukr->counters['customers_skipped']++;
        $ukr->msg("Customer " . $magento_user['entity_id'] . " is already in UKR.");
      }
   // If users does not exists - create one with random password
   } else {
      $firstname = isset($ukr->xls_users[$entry['email']]) ? $ukr->xls_users[$entry['email']]['firstname'] : "";
      $lastname = isset($ukr->xls_users[$entry['email']]) ? $ukr->xls_users[$entry['email']]['lastname'] : "";
      $ukr->create_customer($entry['email'], $firstname, $lastname);
   }
  }
}

$ukr->display_counters();
$time_end = microtime(true);
$execution_time = ($time_end - $time_start);
$ukr->msg("Total execution time: " . date("H:i:s",$execution_time));




?>
