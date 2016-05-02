<?php

$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('geoipblockedips')};
CREATE TABLE {$this->getTable('geoipblockedips')} (                              
    `geoipblockedips_id` mediumint(9) NOT NULL AUTO_INCREMENT,  
    `visitor_id` mediumint(9) DEFAULT NULL,                     
    `customer_id` mediumint(9) DEFAULT NULL,                    
    `blocked_ip` varchar(255) DEFAULT NULL,                     
    `remote_addr` mediumtext,                                   
    `status` smallint(6) DEFAULT NULL,                          
    `type` varchar(255) DEFAULT NULL,                           
    PRIMARY KEY (`geoipblockedips_id`)                          
  ) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;
DROP TABLE IF EXISTS {$this->getTable('geoipultimatelock')};
CREATE TABLE {$this->getTable('geoipultimatelock')} (                                  
    `geoipultimatelock_id` int(11) unsigned NOT NULL AUTO_INCREMENT,  
    `title` varchar(255) NOT NULL DEFAULT '',                         
    `notes` text NOT NULL,                                            
    `redirect_url` mediumtext,                                        
    `ips_exception` mediumtext,                                       
    `stores` mediumtext,                                              
    `cms_pages` mediumtext,                                           
    `categories` mediumtext,                                          
    `rules` mediumtext,                                               
    `blocked_countries` mediumtext,                                   
    `priority` mediumint(9) DEFAULT NULL,                             
    `status` smallint(6) NOT NULL DEFAULT '0',                        
    `created_time` datetime DEFAULT NULL,                             
    `update_time` datetime DEFAULT NULL,                              
    PRIMARY KEY (`geoipultimatelock_id`)                              
  ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;");


$installer->setConfigData('geoipultimatelock/main/enable','1');

$installer->endSetup();
