<?php

class FME_Geoipultimatelock_Block_Product_Related extends Mage_Catalog_Block_Product_List_Related  {

	protected function _prepareData()
    {	
		$excludeId = $this->_geoipUltimate();
        $product = Mage::registry('product');
        /* @var $product Mage_Catalog_Model_Product */

        $this->_itemCollection = $product->getRelatedProductCollection()
            ->addAttributeToSelect('required_options')
            ->setPositionOrder()
            ->addStoreFilter()
            
        ;
        
        if (count($excludeId) > 0) {
			$this->_itemCollection->addFieldToFilter('entity_id', array('nin' => $excludeId));
		}

        if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')) {
            Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($this->_itemCollection,
                Mage::getSingleton('checkout/session')->getQuoteId()
            );
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
//        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($this->_itemCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_itemCollection);

        $this->_itemCollection->load();

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }
    
    protected function _geoipUltimate() {
		
		$ids = array();
		if (!$this->isEnabled(Mage::app()->getStore()->getId())) {
            return;
        }

        $currentIp = Mage::helper('core/http')->getRemoteAddr(); //echo $currentIp;// this will get the visitor ip address
        //$currentIp = '58.65.183.10';//'41.99.121.142'; //'58.65.183.10'; // testing purpose
        $remoteAddr = ip2long($currentIp); // convert ip into remote address
        $infoByIp = Mage::helper('geoipultimatelock')->getInfoByIp($currentIp); //print_r($infoByIp);exit; // get ip related info for country code and country name by remote id if exists
        $blockedIps = Mage::helper('geoipultimatelock')->getBlockedIps();
        
        
        if (!empty($infoByIp)) {

            $country = $infoByIp['cn']; // country name
            $code = $infoByIp['cc']; // country code eg. AU for Australia
            $continentKey = Mage::helper('geoipultimatelock')->getcontinent($country); // echo $country; // continent
            $continentName = (string) strtolower(Mage::helper('geoipultimatelock')->getContinentsName($continentKey));

            $collection = Mage::getModel('geoipultimatelock/geoipultimatelock')
                    ->getCollection()
                    ->addStatusFilter();

            $theData = $collection->getData();
            $total = $collection->getSize(); //total count of acl

            $rules_id = array();
            foreach ($theData as $_c) {

                $id = $_c['geoipultimatelock_id'];
                $blocked_countries = unserialize($_c['blocked_countries']);

                // if (in_array($country, $blocked_countries[$continentName])) {
				/*
				Changed by Alliance - 10/13/2014-10-01
				Added more controls to avoid PHP Warning with $blocked_countries array.
				*/
                if (
					is_array($blocked_countries) && 
					isset($blocked_countries[$continentName]) && 
					is_array($blocked_countries[$continentName]) && 
					in_array($country, $blocked_countries[$continentName])
				) {
                    $rules_id[] = $id;
                }
            }

            if (count($rules_id) > 0 && count($rules_id) == 1) {
                /* filter an ip if has one acl */
                $collection = Mage::getModel('geoipultimatelock/geoipultimatelock')
                        ->getCollection()
                        ->addStatusFilter()
                        ->addIdFilter($rules_id)
                        ->applyLimit(1);
            } elseif (count($rules_id) > 1) {
                /* prioritize result when an ip falls in more than 1 acls and limit 1 */
                $collection = Mage::getModel('geoipultimatelock/geoipultimatelock')
                        ->getCollection()
                        ->addStatusFilter()
                        ->setPriorityOrder()
                        ->addIdFilter($rules_id)
                        ->applyLimit(1);
            } else {
                /* no acl apply if not rule found */
                $collection = array();
            }

            foreach ($collection as $i) {

                $ipExcepArr = explode(',', $i->getIpsException());
                $ipsFilteredArr = array();
                /* filter an array for bad ip input */
                foreach ($ipExcepArr as $ip) {

                    if (Mage::helper('geoipultimatelock')->validateIpFilter($ip)) {

                        $ipsFilteredArr[] = $ip; // new array concisting of ips that are with correct format.
                    }
                }
                /* if current ip is not an exception and is not blocked individually, proceed */
                if (!in_array($currentIp, $ipsFilteredArr)) {
                    /* if acl being applied has rules defined, proceed */
                    if ($i->getRules() != '') {

                        $block = new FME_Geoipultimatelock_Block_Geoipultimatelock();
                        $ids = $block->filterByRule($i->getId());

                        
                    }
                }
            }// end foreach 
        }
        
        return $ids;
	}
	
	public function isEnabled($storeId = null) {
        return Mage::getStoreConfig('geoipultimatelock/main/enable', $storeId);
    }
	
}
