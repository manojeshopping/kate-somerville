<?php
// Configuration.
define('MAGENTO_ROOT', "/var/vhosts/katedev113.dev3.alliance-global.com");
define('CSV_NAME', "katesomerville-1403575171.csv");

$csvFile = __DIR__ . "/".CSV_NAME;

set_time_limit(0); // Set max limit.


// Load Magento environment.
require_once MAGENTO_ROOT.'/app/Mage.php';
$app = Mage::app('default');

// Load Magento DB Resource
$resource = Mage::getSingleton('core/resource');
$conn = $resource->getConnection('core_write');


printLog("===============", true);
printLog("Starts import cms process - ".date('m/d/Y H:i:s'), true);
printLog("===============", true);


// Open File.
$fp = fopen($csvFile, "r");
if($fp === false) {
	printLog("The file ".$file." does not exist.", true, true);
}

$line = 0;
$processedCount = 0;
$errorCount = 0;
while(($data = fgetcsv($fp, 5000, ',')) !== false) {
	$line++;
	printLog("#".$line);

	// First Line.
	if($line == 1) {
		printLog(" - Firs line: TITLES", true);
		
		// Get titles names in $titles array.
		$num = count($data);
		for($i = 0; $i < $num; $i++) {
			$titles[$i] = $data[$i];
		}
		
		continue;
	}
	
	// Get data.
	$rowData = array();
	for($i = 0; $i < $num; $i++) {
		if($data[$i] == "") {
			$rowData[$titles[$i]] = "null";
		} else {
			$rowData[$titles[$i]] = "'".mysql_escape_string($data[$i])."'";
		}
		
		// Get table.
		if($titles[$i] == "table") $table = $data[$i];
		elseif($titles[$i] == "stores") $stores = explode(',', $data[$i]);
	}
	
	
	$identifier = $rowData['identifier'];
	printLog(" - table: ".$table." - identifier: ".$identifier);
	
	if($table == "block") {
		foreach($stores as $store_id) {
			printLog(" - store_id: ".$store_id);
			
			// Check if block exists.
			$sql = "SELECT B.block_id ";
			$sql .= "FROM cms_block B ";
			$sql .= "INNER JOIN cms_block_store BS ON B.block_id = BS.block_id ";
			$sql .= "WHERE B.identifier = ".$identifier." AND BS.store_id = ".$store_id;
			$block_id = $conn->fetchOne($sql);
			if(! empty($block_id)) {
				$sql = "UPDATE cms_block SET ";
				$sql .= "title = ".$rowData['title'].", content = ".$rowData['content'].", ";
				$sql .= "creation_time = ".$rowData['creation_time'].", update_time = ".$rowData['update_time'].", ";
				$sql .= "is_active = ".$rowData['is_active']." ";
				$sql .= "WHERE block_id = ".$block_id;
				
				try {
					$result = $conn->query($sql);
					printLog(" - block_id: ".$block_id." - updated (#".$result->rowCount()." affected)");
				} catch (Exception $e) {
					printLog(" - ERROR - ".$e->getMessage(), true);
					
					$errorCount++;
					continue;
				}
			} else {
				$sql = "INSERT INTO cms_block (identifier, title, content, creation_time, update_time, is_active) VALUES (";
				$sql .= $identifier.", ".$rowData['title'].", ".$rowData['content'].", ".$rowData['creation_time'].", ";
				$sql .= $rowData['update_time'].", ".$rowData['is_active'];
				$sql .= ");";
				try {
					$result = $conn->query($sql);
					printLog(" - inserted (#".$result->rowCount()." affected)");
					
					$block_id = $conn->lastInsertId();
				} catch (Exception $e) {
					printLog(" - ERROR - ".$e->getMessage(), true);
					
					$errorCount++;
					continue;
				}
				
				// Insert Block.
				printLog(" | cms_block_store");
				$sql = "INSERT INTO cms_block_store VALUES (".$block_id.", ".$store_id.");";
				try {
					$result = $conn->query($sql);
					printLog(" - inserted (#".$result->rowCount()." affected)");
					
					$block_id = $conn->lastInsertId();
				} catch (Exception $e) {
					printLog(" - ERROR - ".$e->getMessage(), true);
					
					$errorCount++;
					continue;
				}
			}
		}
	} else {
		foreach($stores as $store_id) {
			printLog(" - store_id: ".$store_id);
			
			// Check if page exists.
			$sql = "SELECT P.page_id ";
			$sql .= "FROM cms_page P ";
			$sql .= "INNER JOIN cms_page_store PS ON P.page_id = PS.page_id ";
			$sql .= "WHERE P.identifier = ".$identifier." AND PS.store_id = ".$store_id;
			$page_id = $conn->fetchOne($sql);
			if(! empty($page_id)) {
				$sql = "UPDATE cms_page SET ";
				$sql .= "title = ".$rowData['title'].", content = ".$rowData['content'].", ";
				$sql .= "creation_time = ".$rowData['creation_time'].", update_time = ".$rowData['update_time'].", ";
				$sql .= "is_active = ".$rowData['is_active'].", root_template = ".$rowData['root_template'].", ";
				$sql .= "meta_keywords = ".$rowData['meta_keywords'].", meta_description = ".$rowData['root_template'].", ";
				$sql .= "content_heading = ".$rowData['root_template'].", sort_order = ".$rowData['root_template'].", ";
				$sql .= "layout_update_xml = ".$rowData['root_template'].", custom_theme = ".$rowData['root_template'].", ";
				$sql .= "custom_root_template = ".$rowData['root_template'].", custom_layout_update_xml = ".$rowData['root_template'].", ";
				$sql .= "custom_theme_from = ".$rowData['root_template'].", custom_theme_to = ".$rowData['root_template'].", ";
				$sql .= "published_revision_id = ".$rowData['root_template'].", website_root = ".$rowData['root_template'].", ";
				$sql .= "under_version_control = ".$rowData['root_template']." ";
				$sql .= "WHERE page_id = ".$page_id;
				
				try {
					$result = $conn->query($sql);
					printLog(" - page_id: ".$page_id." - updated (#".$result->rowCount()." affected)");
				} catch (Exception $e) {
					printLog(" - ERROR - ".$e->getMessage(), true);
					
					$errorCount++;
					continue;
				}
			} else {
				$sql = "INSERT INTO cms_page (identifier, title, content, creation_time, update_time, is_active, root_template, meta_keywords, meta_description, ";
				$sql .= "content_heading, sort_order, layout_update_xml, custom_theme, custom_root_template, custom_layout_update_xml, custom_theme_from, ";
				$sql .= "custom_theme_to, published_revision_id, website_root, under_version_control) VALUES (";
				$sql .= $identifier.", ".$rowData['title'].", ".$rowData['content'].", ".$rowData['creation_time'].", ";
				$sql .= $rowData['update_time'].", ".$rowData['is_active'].", ".$rowData['root_template'].", ";
				$sql .= $rowData['meta_keywords'].", ".$rowData['meta_description'].", ".$rowData['content_heading'].", ";
				$sql .= $rowData['sort_order'].", ".$rowData['layout_update_xml'].", ".$rowData['custom_theme'].", ";
				$sql .= $rowData['custom_root_template'].", ".$rowData['custom_layout_update_xml'].", ".$rowData['custom_theme_from'].", ";
				$sql .= $rowData['custom_theme_to'].", ".$rowData['published_revision_id'].", ".$rowData['website_root'].", ";
				$sql .= $rowData['under_version_control'];
				$sql .= ");";
				try {
					$result = $conn->query($sql);
					printLog(" - inserted (#".$result->rowCount()." affected)");
					
					$page_id = $conn->lastInsertId();
				} catch (Exception $e) {
					printLog(" - ERROR - ".$e->getMessage(), true);
					
					$errorCount++;
					continue;
				}
				
				printLog(" | cms_page_store");
				$sql = "INSERT INTO cms_page_store VALUES (".$page_id.", ".$store_id.");";
				try {
					$result = $conn->query($sql);
					printLog(" - inserted (#".$result->rowCount()." affected)");
					
					$page_id = $conn->lastInsertId();
				} catch (Exception $e) {
					printLog(" - ERROR - ".$e->getMessage(), true);
					
					$errorCount++;
					continue;
				}
			}
		}
	}
	
	
	$processedCount++;
	printLog(" - OK", true);
}

printLog("", true);
printLog("===============", true);
printLog("Processed: ".$processedCount, true);
printLog("Errors: ".$errorCount, true);
printLog("Finished import cms process - ".date('m/d/Y H:i:s'), true);
printLog("===============", true);
die();




function printLog($msg, $break = false, $fatal = false)
{
	// Print on screen.
	echo $msg;
	if($break) echo "\n";
	
	// Save log.
	// Mage::log($msg, null, 'export_orders.log');
	
	if($fatal) die("\n");
}
?>