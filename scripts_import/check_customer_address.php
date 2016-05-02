<?php
// Configuration.
define('MAGENTO_ROOT_DEV3', "/var/vhosts/katedev113.armando.alliance-global.com");
define('MAGENTO_ROOT_MDEV', "/var/vhosts/katedev113.mdev.alliance-global.com");
$currentRoot = MAGENTO_ROOT_DEV3;

set_time_limit(0); // Set max limit.


// Load Magento environment.
require_once $currentRoot.'/scripts_import/import_functions.php';
require_once $currentRoot.'/app/Mage.php';
$app = Mage::app('default');

// Load Magento DB Resource
$resource = Mage::getSingleton('core/resource');
$readConn = $resource->getConnection('core_read');
$writeConn = $resource->getConnection('core_write');

// Walk all customers.
$sql = "SELECT entity_id FROM customer_entity";
$customers = $readConn->query($sql);
$duplicated = 0;
while($_customer = $customers->fetch()) {
	// Check for addresses.
	$sql = "SELECT entity_id FROM customer_address_entity WHERE parent_id = ".$_customer['entity_id']." ORDER BY entity_id DESC";
	$addresses = $readConn->fetchAll($sql);
	if(count($addresses) < 2) continue;
	
	// Get default billing and shipping.
	$customer = Mage::getModel('customer/customer')->load($_customer['entity_id']);
	$defaultBillingId = $customer->getDefaultBilling();
	$defaultShippingId = $customer->getDefaultShipping();
	
	// Walk all customer addresses and compare with prev data.
	$addressDataPrev = array();
	foreach($addresses as $_address) {
		$address = Mage::getModel('customer/address')->load($_address['entity_id']);
		$addressData = $address->getData();
		
		// Remove entity_id, increment_id, created_at and updated_at.
		$entity_id = $addressData['entity_id'];
		unset($addressData['entity_id']);
		unset($addressData['increment_id']);
		unset($addressData['created_at']);
		unset($addressData['updated_at']);
		
		// Delete address if applicable.
		if(in_array($addressData, $addressDataPrev)) {
			if($entity_id == $defaultBillingId || $entity_id == $defaultShippingId) {
				// If the address is default, delete previous address, and save the new.
				$entity_idOld = array_search($addressData, $addressDataPrev);
				if($entity_idOld != $defaultBillingId && $entity_idOld != $defaultShippingId) {
					// Delte address.
					$addressOld = Mage::getModel('customer/address')->load($entity_idOld);
					$addressOld->delete();
					echo "customer_id: ".$_customer['entity_id']." - entity_idOld: ".$entity_idOld."\n";
					
					unset($addressDataPrev[$entity_idOld]);
					$addressDataPrev[$entity_id] = $addressData;
				}
			} else {
				// Delte address.
				$address->delete();
				echo "customer_id: ".$_customer['entity_id']." - entity_id: ".$entity_id."\n";
			}
		}
		
		$addressDataPrev[$entity_id] = $addressData;
	}
	
	$duplicated++;
}

echo "====================\n";
echo "duplicated: ".$duplicated."\n";
echo "====================\n";
?>