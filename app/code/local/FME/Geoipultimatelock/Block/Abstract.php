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
abstract class FME_Geoipultimatelock_Block_Abstract extends Mage_Core_Block_Template {

    public function getAllRules($id = null) {
        $collection = Mage::getModel('geoipultimatelock/geoipultimatelock')
                ->getCollection()
                ->addStatusFilter();

        if (!is_null($id)) {
            $collection->addIdsFilter($id);
        }
        
        $collection->setPriorityOrder();
        
        return $collection;
    }

    public function filterByRule($id) {

        $match = array();
        $allRules = $this->getAllRules($id); 
        
        if (!empty($allRules)) {
            
            foreach ($this->getAllRules($id) as $rules) { 
                
                $model = Mage::getModel('geoipultimatelock/geoipultimatelock_product_rulecss');
                $model->setWebsiteIds(Mage::app()->getStore()->getWebsite()->getId()); 
                /* in case if afterload didn't objectify the rules */
                if (!$rules['rules'] instanceof Varien_Object) {
                    
                    $str = $rules['rules'];
                    $rules['rules'] = unserialize($str);
                    $rules['rules'] = new Varien_Object($rules->getRules());
                } 

                $conditions = $rules["rules"]->getConditions(); 
                $model->getConditions()->loadArray($conditions, 'css');
                $match = $model->getMatchingProductIds();
            }// end foreach
        }
        
        return $match;
    }

}

