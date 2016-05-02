<?php
/****************************************
 500 Friends  -> Ultimate Kate Rewards
****************************************/
$time_start = microtime(true);

require_once('../app/Mage.php');

set_time_limit(0);
/*

  STEP 1 - Import users from CSV
  STEP 2 - Update first name, last name and customergroup_id for freshly created users
  STEP 3 - Send emails to freshly created users

*/


class ukr {

  
  private $app;                 // Mage App Init
  private $store;               // Current store
  private $session;             // Session model
  public $customer;             // Customer 
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
		
	    // Using Template "Customer Merger"
	    $this->mail = Mage::getModel('core/email_template'); 
	    $this->template_id = $this->mail->loadByCode('customer_merger')->getTemplateId();
	    Mage::log("------------------------------------------------------------------", null, "employees_new.log" );
           Mage::log("------------------------------------------------------------------", null, "employees_updated.log" );
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

  // customers EMPLOYEES_092413.CSV

      if (($handle = fopen("employees.csv", "r")) !== FALSE) { 
     //if (($handle = fopen("test_armando.csv", "r")) !== FALSE) {

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		   if(!empty($data[0]) && $data[0] != 'Email'){
           $this->xls_users[$data[0]]['firstname'] = $data[1];
           $this->xls_users[$data[0]]['lastname'] = $data[2];
		   $customergroup_id = 0;
		   
		   switch ($data[3]){
			case "Employee":
				$customergroup_id = "9";
				break;
			case "Friends & Family":
				$customergroup_id = "10";
				break;
			default :
				$customergroup_id = "0";			    
		   }
           $this->xls_users[$data[0]]['customergroup_id'] = $customergroup_id;
		   }
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
  public function update_customer($group_only = false, $email, $group_id) {
    $this->customer->setGroupId($group_id);
    try {
      $this->customer->save();
      $this->customer->setConfirmation(null);
      $this->customer->save();
      $this->msg("UPDATED: Customer " . $email . " has been updated"); 
      Mage::log(" OK  : Customer " . $email . " has been updated.", null, "employees_updated.log" );
      $this->counters['customers_updated']++;
    } catch (Exception $ex) {
    $this->msg("ERROR: Can't update customer with email " . $email); 
    	    Mage::log("ERROR: Customer " . $email . " can not be updated.", null, "employees_updated.log" );
      
    }
  }
  
  // Create new customer
  public function create_customer( $email, $firstname = "", $lastname = "", $group_id) {
    $password = $this->create_password();
    
    $this->customer->setEmail( $email );
    $this->customer->setFirstname( $firstname );
    $this->customer->setLastname( $lastname );
    $this->customer->setPassword( $password );
    $this->customer->setGroupId($group_id);
    try {
		$this->customer->save();
		$this->customer->setConfirmation(null);
		$this->customer->save();
		// Notify User
		$this->notify_customer($email, $password, $firstname . " " . $lastname);
		Mage::log(" OK  : Customer " . $email . " was created with password " . $password, null, "employees_new.log" );      	    
		$this->msg("CREATED: Customer " . $email . " has been created with password " . $password . " & name " . $firstname . " " . $lastname); 
		$this->counters['customers_created']++;
    } catch (Exception $ex) {
		$this->msg("ERROR: Can't create customer with email " . $email);  
		Mage::log(" OK  : Customer " . $email . " was not created.", null, "employees_new.log" );  
    }
  }
  // Reset model
  public function reset_model() {
    $this->customer->reset();
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

}



// CLINET

$ukr = new ukr();

// For each user in CSV
$data = $ukr->xls_users;
foreach ($data as $mail => $entry) {
	// Load user by email
	$ukr->reset_model();

$mail = strtolower($mail);	

	if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
	    $magento_user = $ukr->get_customer($mail);
		
	    $entry_group_id = $entry['customergroup_id'];
	   
		// If user exists, update group
		if ($magento_user) {
		//	if ($magento_user['group_id'] != $entry_group_id) {
				$ukr->update_customer(true, mail, $entry_group_id);
		//	} else {
		//		$ukr->counters['customers_skipped']++;
		//		$ukr->msg("Customer " . $magento_user['entity_id'] . " is already in UKR.");
		//	}
		// If users does not exists - create one with random password
		} else {
		  $firstname = $entry['firstname'];
		  $lastname = $entry['lastname'];
		  $ukr->create_customer($mail, $firstname, $lastname, $entry_group_id);
		}		
	}else{
		$ukr->msg("Mail '" . $key . "' is not correct.");
	}
}

$ukr->display_counters();
$time_end = microtime(true);
$execution_time = ($time_end - $time_start);
$ukr->msg("Total execution time: " . date("H:i:s",$execution_time));


?>