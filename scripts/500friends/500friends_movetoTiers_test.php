<?php
/****************************************
 500 Friends  -> Ultimate Kate Rewards
****************************************/
$time_start = microtime(true);

//require_once('/var/vhosts/katesomerville.com/app/Mage.php'); //Update Path on live
require_once dirname(dirname(__FILE__)) . '/../app/Mage.php';

set_time_limit(0);

class ukr {
	
	private $app;                 // Mage App Init
	private $store;               // Current store
	private $session;             // Session model
	private $email;				  // email
	private $email_code;		  // Email code
	private $email_sender;		  // Email Sender
	private $email_sender_name;	  // Email Sender Name
	public $customer;             // Customer 
	private $keys = array();      // Array with all login info
	public $counters = array();   // Counters
	public $tier_Null;			  // Tier Null/empty	
	public $tier_Rewards;		  // UltimateKate Rewards
	public $tier_Premier;         // UltimateKate Premier
	//public $tier_PremierSpace;    // UltimateKate Premier temporary fix
	public $tier_PremierPlus;     // UltimateKate Premier Plus

	
	 // Create all required instances
	public function __construct() {
	  	
		$this->app  = Mage::app();
	    $this->store = $this->app->getStore();
	    $this->session = Mage::getSingleton('core/session');
	    $this->customer = Mage::getModel("customer/customer");
		$this->email = Mage::getModel('core/email_template');
		$this->email_code = "customer_merger";
		$this->email_sender = Mage::getStoreConfig('trans_email/ident_general/email');
		$this->email_sender_name = Mage::getStoreConfig('trans_email/ident_general/name');
	
		$this->tier_Null = 'null';
		$this->tier_Rewards = 'UltimateKate Rewards';
		$this->tier_Premier = 'UltimateKate Premier';
		//$this->tier_PremierSpace = 'Ultimate Kate Premier';
		$this->tier_PremierPlus = 'UltimateKate Premier Plus';
		

		// Counters
	    $this->counters = array(
			'customers_created' => 0,
			'customers_updated' => 0,
			'customers_skipped' => 0
		);
		
	
		// Set up all keys
	    $this->keys = array(
		  '500FriendsSecret' => Mage::helper('alliance_fivehundredfriends/data')->getTokenKey(),
          '500FriendsUUID'   => Mage::helper('alliance_fivehundredfriends/data')->getUserId()
		);
	}
	
	// Reset model
	public function reset_model() {
		$this->customer->reset();
	}	
	// Message
	public function msg( $msg ) {
		echo '* ' . $msg . "\r\n";
	}
	
	public function display_counters() {
		$total  = $this->counters['customers_created'] + $this->counters['customers_updated'] + $this->counters['customers_skipped'];
		$this->msg("Customers created: " . $this->counters['customers_created'] . " / " . $total);
		$this->msg("Customers updated: " . $this->counters['customers_updated'] . " / " . $total);
		$this->msg("Customers skipped: " . $this->counters['customers_skipped'] . " / " . $total);
	}
	
	// Get a user by email
	public function get_customer( $email ) {
		return $this->customer->loadByEmail($email); 
	}

	// Create new customer
	public function create_customer( $email, $name = "") {
		$password = $this->create_password();
		$lastname = " ";
		$groupid = $this->get_group_id($this->tier_Rewards);
		
		$this->customer->setEmail( $email );
		$this->customer->setFirstname( $name );
		$this->customer->setLastname($lastname );
		$this->customer->setPassword( $password );
		$this->customer->setGroupId($groupid);
		try {
			$this->customer->setConfirmation(null);
			$this->customer->save();
			// Notify User
            $this->notify_customer($email, $password, $name . " " . $lastname);
			$this->msg("CREATED: Customer " . $email . " has been created with password " . $password . " & name " . $name . " " . $lastname); 
		} catch (Exception $ex) {
			$this->msg("ERROR: Can't create customer with email " . $email);  
		}
	}
  
    // Update existing Magneto customer
	public function update_customer($group = false, $email) {
		$groupid = $this->get_group_id($group);
		$this->customer->setGroupId( $groupid );
		try {
			$this->customer->save();
			$this->msg("UPDATED: Customer " . $email . " has been updated"); 
			//Mage::log(" OK  : Customer " . $email . " has been updated.", null, "500friends_ukr_updated.log" );
			Mage::log(" OK  : Customer - Email: " . $email . " - Group: " . $group . " - has been updated.", null, "500friends_ukr_updated.log" );			
		} catch (Exception $ex) {
			$this->msg("ERROR: Can't update customer with email " . $email); 
    	    //Mage::log("ERROR: Customer " . $email . " can not be updated.", null, "500friends_ukr_update.log" );
    	    Mage::log("ERROR: Customer Email: " . $email . " - Group: " . $group . " - can not be updated.", null, "500friends_ukr_updated.log" );			
		}
	}
  	
	
	// Send an email to the customer
	public function notify_customer( $email, $pass, $name ) {
		$vars = array();
		$vars['name'] = ($name != "") ? $name : 'Valued customer';
		$vars['mail'] = $email;
		$vars['password'] = $pass;            
		$sender = array(
			'name' => $this->email_sender_name,
			'email' => $this->email_sender
		);
	
		try {
			$getTemplateId = $this->email->loadByCode($this->email_code)->getTemplateId();
			$this->email->sendTransactional($getTemplateId , $sender, $email, $vars['name'], $vars);
			$this->msg("EMAIL: Email sent to " . $email);
		} catch (Exception $e) {
			$this->msg("ERROR: Unable to send email to " . $email);
		}
	}
	
	
	// Generate random password
	public function create_password() {
		return substr( str_shuffle( "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789" ), 0, 8 );
	}
	
	// Get customer group name
	public function get_group_name( $groupId ) {
		$groupname = Mage::getModel('customer/group')->load( $groupId )->getCustomerGroupCode();
		return $groupname;
	}
	
	// Get customer group id
	public function get_group_id( $groupName ) {
		$group = Mage::getModel('customer/group')->load( $groupName, 'customer_group_code');
		return $group->getId();
	}
	
	// Generate URL 
    public function generate_url( $action, $page ) {
	    $params = array("uuid" => $this->keys['500FriendsUUID'], "page_number" => $page);
		ksort($params);
		
		$string_to_hash = $this->keys['500FriendsSecret'];
			foreach ($params as $key => $val) {
				$string_to_hash .= $key.$val;
			}
			$params["sig"] = md5($string_to_hash);
			$api_url = "https://loyalty.500friends.com/data/" . $action . "?";
			foreach ($params as $key => $val) {
				$api_url .= "$key=".urlencode($val)."&";
			}
		return $api_url; // Url of the api call.
	}
	
	//Get UKR Customers
	public function get_royal_customers($action, $page){
		if (!function_exists("curl_init")) {
			$this->msg('Install php_curl');
			return false;
		}
    
		$link = $this->generate_url($action, $page);
		$this->msg($link);
	
		$c =	curl_init( $link );	
				curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($c, CURLOPT_URL, $link);
				curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 2);
				$resp = curl_exec($c);
    
		if ($resp) {
			$this->msg("500 Friends users are retrieved for page " . $page);
		} else {
			$this->msg("Can't connect to 500 Friends API for page " . $page);
		}
		$decoded_resp = json_decode($resp, true);
		//$this->msg(count($decoded_resp));
		return $decoded_resp['data'];
	}
	
}


$ukr = new ukr();

$page = 1;
$i = 1;
	while (true) { 
		$data = $ukr->get_royal_customers('customers', $page++); 
	
		if (count($data) == 0) break;
		// For each user in 500 Friends
		foreach ($data as $entry){
			// Load user by email
			$ukr->reset_model();
			$ukr->msg("Customer email --- " . $entry['email'] );
			$magento_user = $ukr->get_customer($entry['email']);
			$magento_group_name = $ukr->get_group_name( $magento_user->getGroupId() );		
			//Mage::log("Customer Email: " . $magento_user->getEmail() . " - Group: " . $magento_group_name, null, "500friends_ukr_group.log" );							
			Mage::log("Customer Email: " . $magento_user->getEmail() . " - Magento Group: " . $magento_group_name . " - 500f Group: " . $entry['top_tier_name'], null, "500friends_ukr_grouptest.log" );
			
			if( $magento_user->getEmail() ){ 
				switch( $entry['top_tier_name'] ) {
			
					case $ukr->tier_Premier:
						//Mage::log("Customer Email: " . $magento_user->getEmail() . " - Group: " . $magento_group_name . " - ukr-group: " . $ukr->tier_Premier, null, "500friends_ukr_premier.log" );							
						if($magento_group_name != $ukr->tier_Premier){
							//Premier
							$ukr->msg("Update Customer - UltimateKate Premier");
							$ukr->update_customer( $ukr->tier_Premier, $magento_user->getEmail() );
							$ukr->counters['customers_updated']++;
						}else{
							$ukr->counters['customers_skipped']++;
						}
					break;
					
					//temporary tier
					/*
					case $ukr->tier_PremierSpace:
							$ukr->msg("Update Customer - UltimateKate Premier");
							$ukr->update_customer( $ukr->tier_Premier, $magento_user->getEmail() );
							$ukr->counters['customers_updated']++;
					break;
					*/
			
					case $ukr->tier_PremierPlus:
						if($magento_group_name != $ukr->tier_PremierPlus){
							//PremierPlus
							$ukr->msg("Update Customer - UltimateKate PremierPlus");
							$ukr->update_customer( $ukr->tier_PremierPlus, $magento_user->getEmail() );
							$ukr->counters['customers_updated']++;
						}else{
							$ukr->counters['customers_skipped']++;
						}
					break;
			
					default:
						if($magento_group_name != $ukr->tier_Rewards){
							//Rewards
							$ukr->msg("Update Customer - UltimateKate Rewards");
							$ukr->update_customer( $ukr->tier_Rewards, $magento_user->getEmail() );
							$ukr->counters['customers_updated']++;
						}else{
							$ukr->counters['customers_skipped']++;
						}
				}
			}else{
				$ukr->msg("Customer not found");
				$ukr->create_customer($entry['email'], $entry['name']);
				$ukr->counters['customers_created']++;
			}
			$ukr->msg(" ----------------------------------------- ");
			//$i++;
			//if($i == 5) break; 
		}
  }

$ukr->display_counters();
$time_end = microtime(true);
$execution_time = ($time_end - $time_start);
$ukr->msg("Total execution time: " . date("H:i:s",$execution_time)); 
?>
