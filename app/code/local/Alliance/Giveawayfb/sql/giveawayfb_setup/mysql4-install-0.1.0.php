<?php
$installer = $this;
$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('giveawayfb')};
CREATE TABLE {$this->getTable('giveawayfb')} (
	`giveawayfb_id` 				int(11) unsigned NOT NULL auto_increment,
	`name` 							varchar(150) NULL,
	`lastname` 					varchar(150) NULL,
	`email` 							varchar(150) NULL,
	`birthdate_month` 			varchar(2) NULL,
	`birthdate_day` 				varchar(2) NULL,
	`birthdate_year` 			varchar(4) NULL,
	`newsletter` 					tinyint(1) NULL default 0,
	`telephone` 					varchar(100) NULL,
	`zip` 							varchar(20) NULL,
	`address1` 					varchar(150) NULL,
	`address2` 					varchar(150) NULL,
	`city` 							varchar(255) NULL,
	`state` 							int(11) unsigned NULL,
	`skin_concern1` 				int(11) unsigned NULL,
	`skin_concern2` 				int(11) unsigned NULL,
	`samplekit` 					int(11) unsigned NULL,
	`confirmid` 					varchar(32) NULL,
	`register_creation` 			TIMESTAMP NOT NULL default CURRENT_TIMESTAMP,
	`confirm_creation` 			DATETIME default NULL,
	`customer_id` 				INT(10) unsigned default NULL,
	`customer_password` 		varchar(50) default NULL,
	`order_creation` 				DATETIME default NULL,
	`order_id` 						INT(10) unsigned default NULL,
	`increment_id` 				VARCHAR(50) default NULL,
	PRIMARY KEY (`giveawayfb_id`),
	KEY (`confirmid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();
