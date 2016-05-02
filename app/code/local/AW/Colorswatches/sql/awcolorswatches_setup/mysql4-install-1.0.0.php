<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento enterprise edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Colorswatches
 * @version    1.0.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


$this->startSetup();
try {
    $this->run(
        "

CREATE TABLE IF NOT EXISTS `{$this->getTable('awcolorswatches/swatchattribute')}` (
`swatchattribute_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`swatch_status` tinyint(4) NOT NULL,
`display_popup` tinyint(4) NOT NULL,
`attribute_code` varchar(255) NOT NULL,
PRIMARY KEY (`swatchattribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{$this->getTable('awcolorswatches/swatch')}` (
`swatch_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`option_id` int(10) NOT NULL,
`image` text NOT NULL,
PRIMARY KEY (`swatch_id`),
KEY `option_id` (`option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE  `{$this->getTable(
            'awcolorswatches/swatch'
        )}` ADD FOREIGN KEY (  `aw_swatchattribute_id` ) REFERENCES  `{$this->getTable('eav/attribute_option_value')}` (
`swatchattribute_id`
) ON DELETE CASCADE ON UPDATE CASCADE;

"
    );
} catch (Exception $ex) {
    Mage::logException($ex);
}
$this->endSetup();