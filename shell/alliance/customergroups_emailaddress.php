<?php 

require_once dirname(dirname(__FILE__)) . '/abstract.php';

class Alliance_Shell_Alliance_Customergroups_Emailaddress extends Mage_Shell_Abstract 
{
	public $dbConnection;
	public $autoReplenishDbTable;
	
	public function run()
	{
		echo "Started.......".PHP_EOL;
		$listOfCustomerEmailAddresses = $this->getListOfCustomerEmailAddresses();
		$this->exportToCSV($listOfCustomerEmailAddresses);
		echo "Completed.........".PHP_EOL;
		echo "Location And Name Of the CSV file : \033[32m var/log/CustomerGroups_EmailAddresses.csv \033[37m ".PHP_EOL;
	}
	
	public function getListOfCustomerEmailAddresses()
	{
		Mage::getModel('customer/group')->load($customerGroupId)->getCustomerGroupCode();
		
		$customerGroupCodes = array('Ultimate Kate Premier', 'Ultimate Kate Rewards', 'UltimateKate Premier Plus');

		$emailAddresses = array();
		foreach ($customerGroupCodes as $customerGroupCode) {
			
			$customerGroup = Mage::getModel('customer/group');
			$customerGroup->load($customerGroupCode, 'customer_group_code');
			$customerGroupId =  $customerGroup->getCustomerGroupId();
			$emailAddresses[$customerGroupCode] = array();
			
			$emailAddresses[$customerGroupCode] = Mage::getModel('customer/customer')
				->getCollection()
				->addAttributeToSelect('email')
				->addFieldToFilter('group_id', $customerGroupId)
				->getColumnValues('email');
		}
		return $emailAddresses;
	}

	public function exportToCSV($emailAddresses)
	{
		$filename = 'CustomerGroups_EmailAddresses.csv';
		$handle       = fopen(Mage::getBaseDir('base') . '/var/log/' . $filename, 'w');
		$csv_headings = array('Email Address', 'Customer Group');
		                     
		fputcsv($handle, $csv_headings, ',');

		foreach ($emailAddresses as $customerGroup=>$customerGroupAddresses) {
			foreach ($customerGroupAddresses as $cutomerAddress) {
				$row = array($cutomerAddress, $customerGroup);
				fputcsv($handle, $row, ',');
			}
		}
		fclose($handle);
	}	
}
$shell = new Alliance_Shell_Alliance_Customergroups_Emailaddress();
$shell->run();
	