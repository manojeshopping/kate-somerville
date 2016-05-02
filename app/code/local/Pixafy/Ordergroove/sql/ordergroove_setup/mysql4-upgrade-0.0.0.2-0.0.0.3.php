<?php
/**
 * Version 0.0.0.3 SQL upgrade file. Create
 * the logging table that will be used to
 * store various responses and requests
 * to and from OrderGroove.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
	$installer	=	$this;
	$installer->startSetup();
	
	$installer->run("
DROP TABLE IF EXISTS `".$installer->getTable('ordergroove/log')."`;
CREATE TABLE IF NOT EXISTS `".$installer->getTable('ordergroove/log')."` (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `activity` varchar(150) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `message` varchar(20000) DEFAULT NULL,
  `log_date` datetime DEFAULT NULL,
  `website_id` INT,
  `is_read` INT DEFAULT 0,
  PRIMARY KEY (`entity_id`),
  KEY `type` (`type`),
  KEY `log_date` (`log_date`),
  KEY `activity` (`activity`),
  KEY `website_id` (`website_id`),
  KEY `is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
");
	
	$installer->endSetup();

?>
