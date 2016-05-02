<?php

/**
 * Geoip Ultimate Lock extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Geoipultimatelock
 * @author     RT <rafay.tahir@unitedsol.net>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */
class FME_Geoipultimatelock_Model_Geoipultimatelock extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('geoipultimatelock/geoipultimatelock');
    }

    protected function _afterLoad() { 
        
        if ($this->getData('rules') && is_string($this->getData('rules'))) {
            $this->setData('rules', @unserialize($this->getData('rules')));
        }
        
        $this->humanizeData();

        return parent::_afterLoad();
    }

    protected function _beforeSave() {
        if ($this->getData('rules') && is_array($this->getData('rules')))
            $this->setData('rules', @serialize($this->getData('rules')));


        return parent::_beforeSave();
    }

    public function getTypeById($id) {
        $block = $this->load($id);

        return $block->getType();
    }

    public function humanizeData() {
        if (is_array($this->getData('rules')))
            $this->setData('rules', new Varien_Object($this->getData('rules')));

        return $this;
    }

    public function callAfterLoad() {
        return $this->_afterLoad();
    }

}