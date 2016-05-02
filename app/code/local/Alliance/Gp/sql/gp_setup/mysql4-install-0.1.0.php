<?php

/**
 * Description of class 
 *  adding an order level attribute for GP module
 * 
 * @author     Alliance Dev Team <>
 */

//$installer = $this;
$installer = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup('sales_setup');

/* @var $installer  */
// $installer = $this;

/* Custom Module create a new column in sale/order table and also create new attribute in the 
 * sales order module
 */
$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS gp_exported;
CREATE TABLE gp_exported (
	`order_id` 				INT(11) unsigned NOT NULL,
	`increment_id` 		INT(11) unsigned NOT NULL,
	`status` 				ENUM('exported', 'reimported') NOT NULL,
	`customer_email` 	VARCHAR(150) NOT NULL,
	`file` 					VARCHAR(150) NOT NULL,
	`timestamp` 			TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS gp_files;
CREATE TABLE gp_files (
	`file_id` 				INT(11) unsigned AUTO_INCREMENT NOT NULL,
	`file_name` 			VARCHAR(150) NOT NULL,
	`file_size` 				INT(11) NOT NULL,
	`orders` 				INT(11) NOT NULL,
	`timestamp` 			TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");


$installer->endSetup();

