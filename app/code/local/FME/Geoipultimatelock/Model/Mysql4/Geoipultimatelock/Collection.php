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
class FME_Geoipultimatelock_Model_Mysql4_Geoipultimatelock_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('geoipultimatelock/geoipultimatelock');
    }

    public function addIdFilter($id) {
        if (!is_array($id)) {
            $id = (array) $id;
        }
        $this->getSelect()->where('geoipultimatelock_id IN (?)', $id);
        return $this;
    }

    public function addIdsFilter($id) {
        $this->addFieldToFilter('geoipultimatelock_id', array('in' => $id));
        return $this;
    }

    public function addStoreFilter($stores = null, $breakOnAllStores = false) {
        $_stores = array(Mage::app()->getStore()->getId());
        if (is_string($stores))
            $_stores = explode(',', $stores);
        if (is_array($stores))
            $_stores = $stores;
        if (!in_array('0', $_stores))
            array_push($_stores, '0');
        if ($breakOnAllStores && $_stores == array(0))
            return $this;
        $_sqlString = '(';
        $i = 0;
        foreach ($_stores as $_store) {
            $_sqlString .= sprintf('find_in_set(%s, stores)', $this->getConnection()->quote($_store));
            if (++$i < count($_stores))
                $_sqlString .= ' OR ';
        }
        $_sqlString .= ')';
        $this->getSelect()->where($_sqlString);

        return $this;
    }

    public function addStatusFilter($enabled = true) {
        $this->getSelect()->where('status = ?', $enabled ? 1 : 2);
        return $this;
    }

    public function setPriorityOrder($dir = 'ASC') { 
        $this->getSelect()->order("main_table.priority {$dir}");

        return $this;
    }
    
    public function applyLimit($limit = 1) {
        $this->getSelect()->limit($limit);
        
        return $this;
    }

}