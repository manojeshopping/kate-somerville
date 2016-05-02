<?php


function insertFile($fileName, $tableName, $conn, $useAttributeCode = false, $checkForDuplicate = false, $ignoreDuplicated = false)
{
	printLog("===============", true);
	printLog("insertFile - File: ".$fileName." - Table: ".$tableName, true);
	printLog("===============", true);
	
	// Get csv file.
	$fp = openFile($fileName);
	if(! $fp) return;
	
	// Walk through the file.
	$line = 0;
	$processedCount = 0;
	$errorCount = 0;
	while(($data = fgetcsv($fp, 5000, ',')) !== false) {
		$line++;
		printLog("#".$line);

		// First Line.
		if($line == 1) {
			printLog(" - Firs line: TITLES", true);
			
			// Get columns names.
			$metadata = $conn->describeTable($tableName);
			$columnNames = array_keys($metadata);
			
			// Special fields id.
			$attribute_id_row = "";
			$value_row = "";
			$real_value_row = "";
			
			// Get titles names in $titles array.
			$num = count($data);
			for($i = 0; $i < $num; $i++) {
				// Get special fields id.
				if($data[$i] == "attribute_id") $attribute_id_row = $i;
				elseif($data[$i] == "value" || $data[$i] == "value_index") $value_row = $i;
				elseif($data[$i] == "real_value") $real_value_row = $i;
				
				if(in_array($data[$i], $columnNames)) $titles[$i] = $data[$i];
			}
			
			continue;
		}
		
		// Make par key value.
		$insertData = array();
		for($i = 0; $i < $num; $i++) {
			$fieldName = $titles[$i];
			$fieldValue = $data[$i];
			
			// Exceptions. This fields are custom fields, so, we need to continue.
			if(empty($fieldName)) continue;
			
			$insertData[$fieldName] = (($fieldValue == "") ? null : $fieldValue);
		}
		
		// Reemplace attribute.
		if($useAttributeCode && ! empty($data[$attribute_id_row])) {
			// Set all entities types.
			if(! isset($entityTypes)) {
				$sql = "SELECT entity_type_id, entity_type_code FROM eav_entity_type";
				$entityTypesRows = $conn->fetchAll($sql);
				foreach($entityTypesRows as $_entityType) {
					$entityTypes[$_entityType['entity_type_code']] = $_entityType['entity_type_id'];
				}
			}
			
			// Get entity type id.
			if(! is_numeric($insertData['entity_type_id']) && isset($entityTypes[$insertData['entity_type_id']])) {
				$entity_type_id = $entityTypes[$insertData['entity_type_id']];
				$insertData['entity_type_id'] = $entity_type_id;
			} else {
				if(strpos($tableName, 'catalog_product') !== false) {
					$entity_type_id = $entityTypes['catalog_product'];
				} else {
					$entity_type_id = $entityTypes['catalog_category'];
				}
			}
			
			// Get new attribute id.
			$sql = "SELECT attribute_id FROM eav_attribute WHERE attribute_code = '".$data[$attribute_id_row]."' AND entity_type_id = ".$entity_type_id;
			$attribute_id = $conn->fetchOne($sql);
			if(isset($insertData['attribute_id'])) $insertData['attribute_id'] = $attribute_id;
			
			if(empty($attribute_id)) {
				printLog(" - sql: ".$sql, true);
				$errorCount++;
				continue;
			}
			
			// Get value for int table.
			if($tableName == "catalog_product_entity_int" || $tableName == "catalog_product_entity_varchar" || $tableName == "catalog_product_super_attribute_pricing") {
				$realValue = $data[$real_value_row];
				
				if(! empty($data[$value_row]) && $data[$value_row] != $realValue && $realValue != "") {
					if($tableName != "catalog_product_entity_varchar") $realValue = "'".$realValue."'";
					
					$sql = "SELECT AOV.option_id ";
					$sql .= "FROM eav_attribute_option_value AOV ";
					$sql .= "INNER JOIN eav_attribute_option AO ON AO.option_id = AOV.option_id ";
					$sql .= "WHERE AO.attribute_id = ".$attribute_id." AND AOV.value IN(".$realValue.")";
					$values = $conn->fetchAll($sql);
					$value = "";
					foreach($values as $_value) {
						if(! empty($value)) $value .= ",";
						$value .= $_value['option_id'];
					}
					
					if(isset($insertData['value'])) $insertData['value'] = $value;
					else $insertData['value_index'] = $value;
				}
			}
		}
		
		// Check product type.
		if($tableName == "catalog_product_entity" && ! empty($insertData['type_id'])) {
			$insertData['type_id'] = getEquivalentType($insertData['type_id']);
		}
		
		// Save data in table.
		printLog(" - tableName: ".$tableName." - #".$data[0]);
		try {
			if($checkForDuplicate) {
				$condition = array($conn->quoteInto($titles[0].' = ?', $data[0]));
				$delete = $conn->delete($tableName, $condition);
				printLog(" - deleted prev item");
			}
			
			if($ignoreDuplicated) {
				$sql = "SET FOREIGN_KEY_CHECKS = 0;";
				$conn->query($sql);
			}
			
			$insert = $conn->insert($tableName, $insertData);
			
			// Populate catalog_product_entity_url_key
			if($tableName == "catalog_product_entity_varchar" && $data[$attribute_id_row] == "url_key") {
				$insert = $conn->insert('catalog_product_entity_url_key', array(
					'entity_type_id' => $insertData['entity_type_id'],
					'attribute_id' => $insertData['attribute_id'],
					'store_id' => $insertData['store_id'],
					'entity_id' => $insertData['entity_id'],
					'value' => $insertData['value'],
				));
			}
			
			if($ignoreDuplicated) {
				$sql = "SET FOREIGN_KEY_CHECKS = 1;";
				$conn->query($sql);
			}
		} catch (Exception $e) {
			printLog(" - WRONG - ".$e->getMessage(), true);
			$errorCount++;
			continue;
		}
		
		printLog(" - OK", true);
		$processedCount++;
	}
	fclose($fp);

	printLog("===============", true);
	printLog("Processed Count: ".$processedCount, true);
	printLog("Error Count: ".$errorCount, true);
	printLog("===============", true);
	
	
	return $errorCount;
}

function insertAttributes($fileName, $conn)
{
	printLog("===============", true);
	printLog("insertAttributes - File: ".$fileName, true);
	printLog("===============", true);
	
	// Get csv file.
	$fp = openFile($fileName);
	if(! $fp) return;
	
	// Walk through the file.
	$line = 0;
	$processedCount = 0;
	$errorCount = 0;
	while(($data = fgetcsv($fp, 5000, ',')) !== false) {
		$line++;

		// First Line.
		if($line == 1) {
			printLog("Firs line: TITLES", true);
			
			// Get titles names in $titles array.
			$num = count($data);
			for($i = 0; $i < $num; $i++) {
				$titles[$data[$i]] = $i;
			}
			
			continue;
		}
		
		// Get data.
		$customer_id = $data[$titles['customer_id']];
		$attribute_code = $data[$titles['attribute_code']];
		$table = $data[$titles['table']];
		$value = $data[$titles['attribute_value']];
		$realValue = $data[$titles['real_value']];
		printLog("#".$line." - code: ".$attribute_code." - table: ".$table);
		
		// Get attribute values.
		$sql = "SELECT attribute_id, entity_type_id FROM eav_attribute WHERE attribute_code = '".$attribute_code."';";
		$attribute = $conn->fetchRow($sql);
		printLog(" - attribute_id: #".$attribute['attribute_id']);
		
		// Get local id if real value not equal to value.
		if($value != $realValue && $table == "customer_entity_int") {
			$sql = "SELECT AOV.option_id ";
			$sql .= "FROM eav_attribute_option_value AOV ";
			$sql .= "INNER JOIN eav_attribute_option AO ON AO.option_id = AOV.option_id ";
			$sql .= "WHERE AO.attribute_id = ".$attribute['attribute_id']." AND AOV.value = '".$realValue."'";
			$value = $conn->fetchOne($sql);
		}
		
		$insertData = array(
			'entity_type_id' => $attribute['entity_type_id'],
			'attribute_id' => $attribute['attribute_id'],
			'entity_id' => $customer_id,
			'value' => $value,
		);
		printLog(" - customer_id: #".$customer_id);
		
		
		// Check for duplicates.
		$sql = "SELECT value_id FROM ".$table." WHERE attribute_id = ".$attribute['attribute_id']." AND entity_id = ".$customer_id;
		$value_id = $conn->fetchOne($sql);
		if(! empty($value_id)) {
			$sql = "DELETE FROM ".$table." WHERE value_id = ".$value_id;
			$conn->query($sql);
			
			printLog(" - Delete value: #".$value_id);
		}
		
		
		// Save data in table.
		try {
			$insert = $conn->insert($table, $insertData);
			$id = $conn->lastInsertId();
			printLog(" - value_id: #".$id, true);
		
			$processedCount++;
		} catch (Exception $e) {
			printLog(" - WRONG - ".$e->getMessage(), true);
			
			$errorCount++;
			continue;
		}
	}
	fclose($fp);

	printLog("===============", true);
	printLog("Processed Count: ".$processedCount, true);
	printLog("Error Count: ".$errorCount, true);
	printLog("===============", true);
	
	return $errorCount;
}

function insertDates($fileName, $conn)
{
	printLog("===============", true);
	printLog("insertDates - File: ".$fileName, true);
	printLog("===============", true);
	
	// Get csv file.
	$fp = openFile($fileName);
	if(! $fp) return;
	
	$line = 0;
	$processedCount = 0;
	$errorCount = 0;
	while(($data = fgetcsv($fp, 5000, ',')) !== false) {
		$line++;
		printLog("#".$line);

		// First Line.
		if($line == 1) {
			printLog("Firs line: TITLES", true);
			
			// Get titles names in $titles array.
			$num = count($data);
			for($i = 0; $i < $num; $i++) {
				$titles[$data[$i]] = $i;
			}
			
			continue;
		}
		
		// Get data.
		$customer_id = $data[$titles['customer_id']];
		$created_at = $data[$titles['created_at']];
		$updated_at = $data[$titles['updated_at']];
		printLog(" - customer_id: ".$customer_id);
		
		try {
			$sql = "UPDATE customer_entity SET created_at = '".$created_at."', updated_at = '".$updated_at."' WHERE entity_id = ".$customer_id;
			$result = $conn->query($sql);
			
			$affectedRows = $result->rowCount();
			printLog(" - affectedRows: ".$affectedRows);
			
			if($affectedRows != 1) {
				printLog(" - WRONG - Customer doesn't exists or already updated", true);
				$errorCount++;
				continue;
			}
			
		} catch (Exception $e) {
			printLog(" - WRONG - ".$e->getMessage(), true);
			
			$errorCount++;
			continue;
		}
		
		printLog(" - OK", true);
		$processedCount++;
	}
	fclose($fp);

	printLog("===============", true);
	printLog("Processed Count: ".$processedCount, true);
	printLog("Error Count: ".$errorCount, true);
	printLog("===============", true);
	
	return $errorCount;
}

function insertEavAttributes($csvFolder, $conn, $customer = true)
{
	printLog("===============", true);
	printLog("insertEavAttributes", true);
	printLog("===============", true);
	
	$csvAttributes = $csvFolder . "/eav_attributes.csv";
	$csvLabels = $csvFolder . "/eav_attributes_label.csv";
	$csvOptions = $csvFolder . "/eav_attributes_option.csv";
	$csvOptionValues = $csvFolder . "/eav_attributes_option_value.csv";
	if($customer) {
		$csvCustomerAttributes = $csvFolder . "/customer_eav_attributes.csv";
		$csvCustomerForm = $csvFolder . "/customer_form_attribute.csv";
	} else {
		$csvCatalogAttributes = $csvFolder . "/catalog_eav_attribute.csv";
	}
	
	// Open all files.
	$fpAttributes = openFile($csvAttributes);
	$fpLabels = openFile($csvLabels);
	$fpOptions = openFile($csvOptions);
	$fpOptionValues = openFile($csvOptionValues);
	$fpEntityAttribute = openFile($csvEntityAttribute);
	if($customer) {
		$fpCustomerAttributes = openFile($csvCustomerAttributes);
		$fpCustomerForm = openFile($csvCustomerForm);
	} else {
		$fpCatalogAttributes = openFile($csvCatalogAttributes);
	}
	
	// Take all labels.
	$labels = getAttributesAuxiliars($fpLabels);
	printLog("Labels: ".count($labels), true);
	
	$options = getAttributesAuxiliars($fpOptions);
	printLog("Options: ".count($options), true);
	
	$optionValues = getAttributesAuxiliars($fpOptionValues);
	printLog("Option Values: ".count($optionValues), true);
	
	if($customer) {
		$customerAttributes = getAttributesAuxiliars($fpCustomerAttributes);
		printLog("Customer Attributes: ".count($customerAttributes), true);
		
		$customerForm = getAttributesAuxiliars($fpCustomerForm);
		printLog("Customer Attributes Form: ".count($customerForm), true);
	} else {
		$catalogAttributes = getAttributesAuxiliars($fpCatalogAttributes);
		printLog("Catalog Attributes: ".count($catalogAttributes), true);
	}
	
	
	// Walk through the file.
	$line = 0;
	$processedCount = 0;
	$errorCount = 0;
	printLog("===============", true);
	while(($data = fgetcsv($fpAttributes, 5000, ',')) !== false) {
		$line++;
		printLog("#".$line);
		
		// Line 1 - titles.
		if($line == 1) {
			printLog("First Line - Titles", true);
			
			// Get titles names in $titles array.
			$titles = array();
			$num = count($data);
			for($i = 0; $i < $num; $i++) {
				$titles[$i] = $data[$i];
			}
			
			continue;
		}
	
		// Make par key value.
		$insertData = array();
		for($i = 0; $i < $num; $i++) {
			$fieldName = $titles[$i];
			$fieldValue = $data[$i];
			
			// Exceptions. This fields are custom fields, so, we need to continue.
			if(empty($fieldName)) continue;
			
			$insertData[$fieldName] = (($fieldValue == "") ? null : $fieldValue);
		}
		
		// Save new attribute in table.
		try {
			// Remove old attribute_id
			$attribute_id_old = $insertData['attribute_id'];
			unset($insertData['attribute_id']);
			
			// Check if attribute already exists.
			$sql = "SELECT attribute_id FROM eav_attribute ";
			$sql .= "WHERE entity_type_id = ".$insertData['entity_type_id']." AND attribute_code = '".$insertData['attribute_code']."'";
			$attribute_id = $conn->fetchOne($sql);
			
			if(empty($attribute_id)) {
				$insert = $conn->insert('eav_attribute', $insertData);
				$attribute_id = $conn->lastInsertId();
				printLog(" - new attribute.");
			} else {
				printLog(" - already exists");
			}
			printLog(" - attribute_id: ".$attribute_id." - code: ".$insertData['attribute_code']);
			$processedCount++;
		} catch (Exception $e) {
			printLog("WRONG - #".$errorCount." - ".$e->getMessage(), true);
			
			$errorCount++;
			continue;
		}
		
		// Insert Labels.
		printLog(" - Inserting Labels");
		$labelsCount = 0;
		if(! empty($labels)) {
			foreach($labels as $_label) {
				if($_label['attribute_id'] != $attribute_id_old) continue;
				
				// Check if label already exists.
				$sql = "SELECT attribute_label_id FROM eav_attribute_label WHERE attribute_id = ".$attribute_id." AND store_id = ".$_label['store_id'];
				$attribute_label_id = $conn->fetchOne($sql);
				
				if(empty($attribute_label_id)) {
					unset($_label['attribute_label_id']);
					$_label['attribute_id'] = $attribute_id;
					$insert = $conn->insert('eav_attribute_label', $_label);
				
					$labelsCount++;
				}
			}
		}
		printLog("(".$labelsCount.")");
		
		// Insert Options.
		printLog(" - Inserting Options");
		$optionsCount = 0;
		if(! empty($options)) {
			// Delete old options.
			$condition = array($conn->quoteInto('attribute_id = ?', $attribute_id));
			$delete = $conn->delete('eav_attribute_option', $condition);
			
			foreach($options as $_option) {
				if($_option['attribute_id'] != $attribute_id_old) continue;
				
				$option_id_old = $_option['option_id'];
				
				unset($_option['option_id']);
				$_option['attribute_id'] = $attribute_id;
				
				$insert = $conn->insert('eav_attribute_option', $_option);
				$option_id = $conn->lastInsertId();
				
				
				$optionsCount++;
				
				// Delete old options values.
				$condition = array($conn->quoteInto('option_id = ?', $option_id));
				$delete = $conn->delete('eav_attribute_option_value', $condition);
				
				foreach($optionValues as $_optionValue) {
					if($_optionValue['option_id'] != $option_id_old) continue;
					
					$_optionValue['option_id'] = $option_id;
					unset($_optionValue['value_id']);
					$insert = $conn->insert('eav_attribute_option_value', $_optionValue);
				}
			}
		}
		printLog("(".$optionsCount.")");
		
		
		if($customer) {
			// Customer Attributes.
			printLog(" - Inserting Customer Attributes");
			$customerCount = 0;
			if(! empty($customerAttributes)) {
				foreach($customerAttributes as $_customer) {
					if($_customer['attribute_id'] != $attribute_id_old) continue;
					
					try {
						$_customer['attribute_id'] = $attribute_id;
						$insert = $conn->insert('customer_eav_attribute', $_customer);
						
						$customerCount++;
					} catch (Exception $e) {
						printLog(" - WRONG - ".$e->getMessage(), true);
						
						$errorCount++;
					}
				}
			}
			printLog("(".$customerCount.")");
			
			// Customer Attributes.
			printLog(" - Inserting Customer Attributes Form");
			$customerCount = 0;
			if(! empty($customerForm)) {
				foreach($customerForm as $_customer) {
					if($_customer['attribute_id'] != $attribute_id_old) continue;
					
					try {
						$_customer['attribute_id'] = $attribute_id;
						$insert = $conn->insert('customer_form_attribute', $_customer);
						
						$customerCount++;
					} catch (Exception $e) {
						printLog(" - WRONG - ".$e->getMessage(), true);
						
						$errorCount++;
					}
				}
			}
			printLog("(".$customerCount.")");
		} else {
			// Catalog Attributes.
			printLog(" - Inserting Catalog Attributes");
			$catalogCount = 0;
			if(! empty($catalogAttributes)) {
				foreach($catalogAttributes as $_catalog) {
					if($_catalog['attribute_id'] != $attribute_id_old) continue;
					
					// Check if alreay insertered.
					$sql = "SELECT attribute_id FROM catalog_eav_attribute WHERE attribute_id = ".$attribute_id;
					$attribute_id_aux = $conn->fetchOne($sql);
					
					if(empty($attribute_id_aux)) {
						try {
							$_catalog['attribute_id'] = $attribute_id;
							$insert = $conn->insert('catalog_eav_attribute', $_catalog);
						} catch (Exception $e) {
							printLog(" - WRONG - ".$e->getMessage(), true);
							
							$errorCount++;
						}
					} else {
						printLog(" - Already exists - ");
					}
					
					$catalogCount++;
				}
			}
			printLog("(".$catalogCount.")");
		}
		
		
		printLog(" - OK", true);
		$processedCount++;
	}
	fclose($fpAttributes);

	printLog("===============", true);
	printLog("Processed Count: ".$processedCount, true);
	printLog("Error Count: ".$errorCount, true);
	printLog("===============", true);
	
	return $errorCount;
}

function insertStructure($fileName, $tableName, $conn)
{
	printLog("===============", true);
	printLog("insertStructure - File: ".$fileName." - Table: ".$tableName, true);
	printLog("===============", true);
	
	// Get csv file.
	$fp = openFile($fileName);
	if(! $fp) return;
	
	// Walk through the file.
	$line = 0;
	$processedCount = 0;
	$errorCount = 0;
	while(($data = fgetcsv($fp, 5000, ',')) !== false) {
		$line++;
		printLog("#".$line);

		// First Line.
		if($line == 1) {
			printLog(" - Firs line: TITLES", true);
			
			continue;
		}
		
		// Get column data.
		$column_name = $data[0];
		$data_type = $data[1];
		$length = $data[2];
		
		// Get columns names.
		$currentMetadata = $conn->describeTable($tableName);
		
		// Add column to table.
		if(! isset($currentMetadata[$column_name])) {
			try {
				$sql = "ALTER TABLE ".$tableName." ADD COLUMN ".$column_name." ".$data_type;
				if(! empty($length)) $sql .= "(".$length.")";
				$conn->query($sql);
				$processedCount++;
			} catch (Exception $e) {
				printLog(" - WRONG - ".$e->getMessage(), true);
				$errorCount++;
				continue;
			}
		}
		
		printLog(" - OK", true);
	}
	fclose($fp);

	printLog("===============", true);
	printLog("Processed Count: ".$processedCount, true);
	printLog("Error Count: ".$errorCount, true);
	printLog("===============", true);
	
	
	return $errorCount;
}

function insertEnterpriseFile($fileName, $tableName, $conn)
{
	printLog("===============", true);
	printLog("insertEnterpriseFile - File: ".$fileName." - Table: ".$tableName, true);
	printLog("===============", true);
	
	// Get csv file.
	$fp = openFile($fileName);
	if(! $fp) return;
	
	// Walk through the file.
	$attributeCodes = array();
	$attributeIds = array();
	$line = 0;
	$processedCount = 0;
	$errorCount = 0;
	while(($data = fgetcsv($fp, 5000, ',')) !== false) {
		$line++;
		printLog("#".$line);

		// First Line.
		if($line == 1) {
			printLog(" - Firs line: TITLES", true);
			
			// Get attribute codes.
			$num = count($data);
			for($i = 0; $i < $num; $i++) {
				if($data[$i] != "entity_id") {
					$attribute_code = $data[$i];
					$attributeCodes[$i] = $attribute_code;
					
					// Get attribute id.
					$sql = "SELECT A.attribute_id, A.frontend_input FROM eav_attribute A ";
					$sql .= "INNER JOIN customer_eav_attribute CA ON CA.attribute_id = A.attribute_id ";
					$sql .= "WHERE A.attribute_code = '".substr($attribute_code, 9)."'";
					$attributeIds[$attribute_code] = $conn->fetchRow($sql);
				}
			}
			
			continue;
		}
		
		// Construct insertData.
		$insertData = array();
		$insertData['entity_id'] = $data[0];
		$num = count($attributeCodes);
		for($i = 1; $i <= $num; $i++) {
			$attribute_code = $attributeCodes[$i];
			$attribute_id = $attributeIds[$attribute_code]['attribute_id'];
			$frontend_input = $attributeIds[$attribute_code]['frontend_input'];
			$value = $data[$i];
			if(! empty($value) && $frontend_input == "select") {
				$sql = "SELECT AOV.option_id ";
				$sql .= "FROM eav_attribute_option_value AOV ";
				$sql .= "INNER JOIN eav_attribute_option AO ON AO.option_id = AOV.option_id ";
				$sql .= "WHERE AO.attribute_id = ".$attribute_id." AND AOV.value = '".$value."'";
				$values = $conn->fetchAll($sql);
				$value = "";
				foreach($values as $_value) {
					if(! empty($value)) $value .= ",";
					$value .= $_value['option_id'];
				}
			}
			
			if(empty($value)) $value = null;
			
			$insertData[$attribute_code] = $value;
		}
		
		printLog(" - tableName: ".$tableName." - #".$data[0]);
		try {
			$insert = $conn->insert($tableName, $insertData);
		} catch (Exception $e) {
			printLog(" - WRONG - ".$e->getMessage(), true);
			$errorCount++;
			continue;
		}
		
		printLog(" - OK", true);
		$processedCount++;
	}
	fclose($fp);

	printLog("===============", true);
	printLog("Processed Count: ".$processedCount, true);
	printLog("Error Count: ".$errorCount, true);
	printLog("===============", true);
	
	
	return $errorCount;
}

function getEquivalentType($type_id)
{
	return str_replace('subscription_', '', $type_id);
}


function openFile($file)
{
	$fp = fopen($file, "r");
	if ($fp === false) {
		printLog("The file ".$file." does not exist.", true);
		printLog("===============", true);
		return false;
	}
	
	return $fp;
}

function getAttributesAuxiliars($fp)
{
	$values = array();
	if($fp !== false) {
		$line = 0;
		while(($data = fgetcsv($fp, 5000, ',')) !== false) {
			$line++;
			
			// Line 1 - titles.
			if($line == 1) {
				// Get titles names in $titles array.
				$titles = array();
				$num = count($data);
				for($i = 0; $i < $num; $i++) {
					$titles[$i] = $data[$i];
				}
				
				continue;
			}
		
			// Make par key value.
			$insertData = array();
			for($i = 0; $i < $num; $i++) {
				$fieldName = $titles[$i];
				$fieldValue = $data[$i];
				
				// Exceptions. This fields are custom fields, so, we need to continue.
				if(empty($fieldName)) continue;
				
				$insertData[$fieldName] = (($fieldValue == "") ? null : $fieldValue);
			}
			
			$values[] = $insertData;
		}
	}
	fclose($fp);
	
	return $values;
}


function printLog($msg, $break = false, $fatal = false)
{
	// Print on screen.
	echo $msg;
	if($break) echo "\n";
	
	// Save log.
	// Mage::log($msg, null, 'import_process.log');
	
	if($fatal) die("\n");
}


