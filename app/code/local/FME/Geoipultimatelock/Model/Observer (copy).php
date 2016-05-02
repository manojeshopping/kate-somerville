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
class FME_Geoipultimatelock_Model_Observer {

    protected $_isMessage;

    public function isEnabled($storeId = null) {
        return Mage::getStoreConfig('geoipultimatelock/main/enable', $storeId);
    }

    public function __construct() {
        $this->_isMessage = Mage::helper('geoipultimatelock')->getDefaultBlockMessage();
    }

    public function geoCheck(Varien_Event_Observer $observer) {

        if (!$this->isEnabled(Mage::app()->getStore()->getId())) {
            return;
        }
        $currentIp = Mage::helper('core/http')->getRemoteAddr(); // this will get the visitor ip address
        //$currentIp = '44.77.176.1'; //'58.65.183.10'; //'44.77.176.1';// // testing purpose
        $remoteAddr = ip2long($currentIp); // convert ip into remote address
        $infoByIp = Mage::helper('geoipultimatelock')->getInfoByIp($currentIp);  //echo '<pre>';print_r($infoByIp);echo '</pre>';exit;
        if (empty($infoByIp)) {
            return;
        }
        $currentStore = Mage::app()->getStore()->getId(); // current store id
        $currentCms = $observer->getEvent()->getPage()->getPageId();
        /* priority 1 for , immidiate site block for blocked ips */
        $this->_blockBlackList($currentIp);

        $country = $infoByIp['cn']; // country name
        /* prioritize result when an ip falls in more than 1 acls and limit 1 */
        $_geoipCollection = $this->_getRulesByCountry($country, $currentStore, true, true, $currentCms);
        if (!$_geoipCollection->count() > 0) {
            return;
        }
        foreach ($_geoipCollection as $i) {
            //$stores = preg_split('@,@', $i->getStores(), NULL, PREG_SPLIT_NO_EMPTY);
            $ipExcepArr = explode(',', $i->getIpsException());
            /* filter an array for bad ip input */
            $ipFtrArr = Mage::helper('geoipultimatelock')->validateIpFilter($ipExcepArr);
            /* if current ip is not an exception, proceed */
            if (in_array($currentIp, $ipFtrArr)) {
                /* first to check if rules are not available */
                return;
            }
            /* redirect or show blank if store is given */
            $this->_redirectOrMessage($i->getRedirectUrl(), $i->getNotes());
        }
    }

    public function filterByGeoip(Varien_Event_Observer $observer) {

        if (!$this->isEnabled(Mage::app()->getStore()->getId())) {
            return;
        }
        $currentIp = Mage::helper('core/http')->getRemoteAddr(); //echo $currentIp;// this will get the visitor ip address
        /* priority 1 for , immidiate site block for blocked ips */
        $this->_blockBlackList($currentIp);
        //$currentIp = '58.65.183.10';//'41.99.121.142'; //'58.65.183.10'; // testing purpose
        $currentStore = Mage::app()->getStore()->getId(); // current store id
        $remoteAddr = ip2long($currentIp); // convert ip into remote address
        $infoByIp = Mage::helper('geoipultimatelock')->getInfoByIp($currentIp); //print_r($infoByIp);exit; // get ip related info for country code and country name by remote id if exists
        if (empty($infoByIp)) {
            return;
        }



        $country = $infoByIp['cn']; // country name
        $continentKey = Mage::helper('geoipultimatelock')->getcontinent($country); // echo $country; // continent
        $continentName = (string) strtolower(Mage::helper('geoipultimatelock')->getContinentsName($continentKey));
        /* get rules by country */
        $_geoipCollection = $this->_getRulesByCountry($country, $currentStore, false, false);


        /*$_currentCategory = Mage::getModel('catalog/category')
                ->load(Mage::registry('current_category')->getId());

        $_productCollection = Mage::getResourceModel('catalog/product_collection')
                ->addStoreFilter()
                ->addCategoryFilter($_currentCategory)
                ->setPage(1);

        $_excludeProducts = array();

        foreach ($_productCollection as $product) {
            $_collection = $this->_fetchProductIdsByRule($_geoipCollection, $product->getId()); //rules collection prioritized
           if ($_collection != '') {
                $_r = Mage::getModel('geoipultimatelock/geoipultimatelock')->load($_collection)->getData();
                $_excludeProducts[$product->getId()] = $_r['geoipultimatelock_id'];
            }
        } echo '<pre>';print_r($_excludeProducts);echo '</pre>';
        // need to filter each product accordingly
*/


        $_blockProductIds = array();
        foreach ($_geoipCollection as $g) {

            $ipExcepArr = explode(',', $g->getIpsException());
            /* filter an array for bad ip input */
            $ipsFilteredArr = Mage::helper('geoipultimatelock')->validateIpFilter($ipExcepArr);
            /* check if current ip is an exception */
            if (in_array($currentIp, $ipsFilteredArr)) {
                continue; //skip to next iteration
            }
            $block = new FME_Geoipultimatelock_Block_Geoipultimatelock();
            $_blockProductIds[] = array_shift($block->filterByRule($g->getId()));
        }
        
        if (empty($_blockProductIds)) {
            return;
        }
        /* filtering collection */
        $observer->getEvent()->getCollection()->addFieldToFilter('entity_id', array('nin' => array_unique($_blockProductIds)));
    }

    public function saveFmeContents(Varien_Event_Observer $observer) {

        $coreDb = Mage::getSingleton('core/config');
        if (isset($_POST['fmedescription'])) {
            $coreDb->saveConfig('geoipultimatelock/basics/block_message', stripslashes($_POST['fmedescription']));
        }
    }

    public function beforeProductLoad(Varien_Event_Observer $observer) {

        if (!$this->isEnabled(Mage::app()->getStore()->getId())) {
            return;
        }

        $currentIp = Mage::helper('core/http')->getRemoteAddr(); //echo $currentIp;// this will get the visitor ip address
        $this->_blockBlackList($currentIp);
        $currentStore = Mage::app()->getStore()->getId(); // current store id
        //$currentIp = '58.65.183.10';//'41.99.121.142'; //'58.65.183.10'; // testing purpose
        $remoteAddr = ip2long($currentIp); // convert ip into remote address
        $infoByIp = Mage::helper('geoipultimatelock')->getInfoByIp($currentIp); //print_r($infoByIp);exit; // get ip related info for country code and country name by remote id if exists
        if (empty($infoByIp)) {
            return;
        }
        $country = $infoByIp['cn']; // country name
        /* country based, store based collection of rules */
        $_geoipCollection = $this->_getRulesByCountry($country, $currentStore, false, false);
        if (!$_geoipCollection->count() > 0) {
            return;
        }
        $_filterdCollection = $this->_rulesByProduct($_geoipCollection, $observer->getValue());
        if (!$_filterdCollection->count() > 0) {
            return;
        }

        foreach ($_filterdCollection as $i) {

            $ipExcepArr = explode(',', $i->getIpsException());
            /* filter an array for bad ip input */
            $ipsFilteredArr = Mage::helper('geoipultimatelock')->validateIpFilter($ipExcepArr);
            /* if current ip is not an exception and is not blocked individually, proceed */
            if (in_array($currentIp, $ipsFilteredArr)) {
                /* first to check if rules are not available */
                return;
            }
            /* if acl being applied has rules defined, proceed */
            $this->_redirectOrMessage($i->getRedirectUrl(), $i->getNotes());
        }
    }

    protected function _blockBlackList($visitorIp) {

        $blockedIps = Mage::helper('geoipultimatelock')->getBlockedIps();
        /* priority 1 for , immidiate site block for blocked ips */
        if (in_array($visitorIp, $blockedIps)) {

            $this->_redirectOrMessage(null, $this->_isMessage);
        }
    }

    protected function _redirectOrMessage($url = null, $message = '', $template = 'geoipultimatelock/block.phtml') {

        if ($url != null || $url != '') {
            Mage::app()->getFrontController()
                    ->getResponse()
                    ->setRedirect($url);
            return;
        }
        if ($message == '') {
            $message = $this->_isMessage;
        }
        echo Mage::app()->getLayout()
                ->createBlock('core/template')
                ->setBlockMessage($message)
                ->setTemplate($template)
                ->toHtml();
        exit;
    }

    protected function _getRulesByCountry($country, $store = null, $priority = true, $limit = true, $page = '') {

        $_helper = Mage::helper('geoipultimatelock');
        $continentKey = $_helper->getcontinent($country); //echo $continentKey;exit;// continent
        $continentName = (string) strtolower($_helper->getContinentsName($continentKey)); //echo $continentName;exit;
        $collection = Mage::getModel('geoipultimatelock/geoipultimatelock')->getCollection()
                ->addStoreFilter($store)
                ->addStatusFilter();

        $rules_id = array();

        foreach ($collection as $_c) {
            $blocked_countries = unserialize($_c->getBlockedCountries());

            if (in_array($country, $blocked_countries[$continentName])) {

                $rules_id[] = $_c->getId();
            }
        }
        ///store based
        $_geoipCollection = Mage::getModel('geoipultimatelock/geoipultimatelock')
                ->getCollection()
                ->filterCollection($store, $rules_id, $priority, $limit, $page);

        return $_geoipCollection;
    }

    protected function _rulesByProduct($collection, $product) {
        $rules = array();
        foreach ($collection as $i) {
            $block = new FME_Geoipultimatelock_Block_Geoipultimatelock();
            $ids = $block->filterByRule($i->getId());
            if (!empty($ids) && in_array($product, $ids)) {

                $rules[] = $i->getId();
            }
        }

        $collection->filterCollection(null, $rules, true, true);

        return $collection;
    }

    protected function _fetchProductIdsByRule($_rulesCollection, $productId) {

        //$ruleIds = array();
        
        if ($_rulesCollection->count() > 0) {
            $_rulesCollection->setPriorityOrder();
            foreach ($_rulesCollection->getData() as $rule) {
                $counter = 0; //initializing counter
                $model = Mage::getModel('geoipultimatelock/geoipultimatelock_product_rulecss');
                $model->setWebsiteIds(Mage::app()->getStore()->getWebsite()->getId());
                /* in case if afterload didn't objectify the rules */
                if ($rule["rules"] != '') {

                    if (!$rule['rules'] instanceof Varien_Object) {

                        $str = $rule['rules'];
                        $rule['rules'] = unserialize($str);
                        $rule['rules'] = new Varien_Object($rule['rules']);
                        //echo '<pre>';print_r($rule['condition_serialized']);
                    }

                    $conditions = $rule["rules"]->getConditions(); //echo '<pre>';print_r($conditions);

                    if (isset($conditions['css'])) {

                        $match = array();
                        $model->getConditions()->loadArray($conditions, 'css');
                        $match = $model->getMatchingProductIds();
                        
                        if (in_array($productId, $match)) {
                            
                            $ruleIds[] = $rule["geoipultimatelock_id"];
                            $counter++;//increment location to be set!...
                        }
                    }
                } 
            }// end foreach
        } //echo '<pre>';print_r(reset($ruleIds));echo '</pre>';
        //reset to get the first element of an array
        return reset($ruleIds);
       
    }

}
