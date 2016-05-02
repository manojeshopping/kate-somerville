<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('quiz')};
CREATE TABLE {$this->getTable('quiz')} (
  `quiz_id` int(11) unsigned NOT NULL auto_increment,
  `qname` varchar(255) NOT NULL default '',
  `qage` varchar(100) NOT NULL default '',
  `qgender` varchar(100) NOT NULL default '',
  `q1option` varchar(100) NOT NULL default '',
  `q2option` varchar(100) NOT NULL default '',
  `q3option` varchar(100) NOT NULL default '',
  `q4option` varchar(100) NOT NULL default '',
  `q5option` varchar(100) NOT NULL default '',
  `q6option` varchar(100) NOT NULL default '',
  `q7option` varchar(100) NOT NULL default '',
  `q8option` varchar(100) NOT NULL default '',
  `q9option` varchar(100) NOT NULL default '',
  `q10option` varchar(100) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`quiz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();