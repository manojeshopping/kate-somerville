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

    public function geoCheck(Varien_Event_Observer $observer) {

        if (!$this->isEnabled(Mage::app()->getStore()->getId())) {
            return;
        }

        $this->_isMessage = Mage::getStoreConfig('geoipultimatelock/basics/block_message', Mage::app()->getStore()->getId());
        $currentIp = Mage::helper('core/http')->getRemoteAddr(); // this will get the visitor ip address
        //$currentIp = '44.77.176.1'; //'58.65.183.10'; //'44.77.176.1';// // testing purpose
        $remoteAddr = ip2long($currentIp); // convert ip into remote address
        $infoByIp = Mage::helper('geoipultimatelock')->getInfoByIp($currentIp);  //echo '<pre>';print_r($infoByIp);echo '</pre>';exit;
        $currentStore = Mage::app()->getStore()->getId(); // current store id
        //$currentCms = Mage::getBlockSingleton('cms/page')->getPage()->getId(); //Mage::getSingleton('cms/page')->getIdentifier();
        $currentCms = Mage::getSingleton('cms/page')->getId();  // Alliance-Global 2014-10-01 
		$blockedIps = Mage::helper('geoipultimatelock')->getBlockedIps();

        /* priority 1 for , immidiate site block for blocked ips */
        if (in_array($currentIp, $blockedIps)) {

            if ($this->_isMessage != '') {
                echo Mage::app()->getLayout()
                        ->createBlock('geoipultimatelock/geoipultimatelock')
                        ->setBlockMessage($this->_isMessage)
                        ->setTemplate('geoipultimatelock/block.phtml')
                        ->toHtml();
                return;
            } else {
                echo Mage::helper('geoipultimatelock')->__('Temporarily Shudown!');
                exit;
            }
        }
		
		// Added by Alliance - To avoid redirect when user select country.
		$region_redirect_domain = Mage::getModel('core/cookie')->get('katesomerville_region_route');
		if(! empty($region_redirect_domain)) {
			$helper = Mage::helper('alliance_regionroute');
			$currentDomain = $helper->getCurrentDomain();
			
			if($currentDomain != $region_redirect_domain) {
				Mage::app()->getFrontController()->getResponse()->setRedirect('//' . $region_redirect_domain);
			}
			
			return;
		}
		// Added by Alliance - To avoid redirect when user select country.
		

        if (!empty($infoByIp)) {

            $country = $infoByIp['cn']; // country name
            $code = $infoByIp['cc']; // country code eg. AU for Australia
            $continentKey = Mage::helper('geoipultimatelock')->getcontinent($country); //echo $continentKey;exit;// continent
            $continentName = (string) strtolower(Mage::helper('geoipultimatelock')->getContinentsName($continentKey)); //echo $continentName;exit;

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

                $redirectTo = $i->getRedirectUrl();
                $stores = explode(',', $i->getStores());
                $pages = array();
                if ($i->getCmsPages() != '') {
					
					$pages = explode(',', $i->getCmsPages());
				}

                $ipExcepArr = explode(',', $i->getIpsException());
                $ipFtrArr = array();
                /* filter an array for bad ip input */
                foreach ($ipExcepArr as $ip) {

                    if (Mage::helper('geoipultimatelock')->validateIpFilter($ip)) {

                        $ipFtrArr[] = $ip; // new array concisting of ips that are filtered.
                    }
                }
                /* if current ip is not an exception  and not in blockedips section either, proceed */
                if (!in_array($currentIp, $ipFtrArr)) {
                    /* first to check if rules are not available */
                    $blockedCountries = unserialize($i->getBlockedCountries());
                    if ($continentName != '') { //echo '<pre>';print_r($blockedCountries[$continentName]);echo '</pre>';exit;
                        if (in_array($country, $blockedCountries[$continentName])) {
                            /* redirect or show blank if store is given */
                            if (count($i->getStores()) > 0 && in_array($currentStore, $stores)) {

                                if ($redirectTo != '') {
                                    Mage::app()
                                            ->getFrontController()
                                            ->getResponse()
                                            ->setRedirect($redirectTo);
                                } else if ($this->_isMessage != '') {
                                    echo Mage::app()->getLayout()
                                            ->createBlock('core/template')
                                            ->setBlockMessage($this->_isMessage)
                                            ->setTemplate('geoipultimatelock/block.phtml')
                                            ->toHtml();
                                    exit;
                                } else {
                                    echo Mage::helper('geoipultimatelock')->__('Temporarily Shudown!');
                                    exit;
                                }
                            }

                            if (count($pages) > 0 && in_array($currentCms, $pages)) {
                                if ($redirectTo != '') {
                                    Mage::app()
                                            ->getFrontController()
                                            ->getResponse()
                                            ->setRedirect($redirectTo);
                                } else if ($this->_isMessage != '') {
                                    echo Mage::app()->getLayout()
                                            ->createBlock('core/template')
                                            ->setBlockMessage($this->_isMessage)
                                            ->setTemplate('geoipultimatelock/block.phtml')
                                            ->toHtml();
                                    exit;
                                } else {
                                    echo Mage::helper('geoipultimatelock')->__('Temporarily Shudown!');
                                    exit;
                                }
                            }
                        }
                    }
                }
            }/* end foreach */
        }
    }

    public function filterByGeoip(Varien_Event_Observer $observer) {

        if (!$this->isEnabled(Mage::app()->getStore()->getId())) {
            return;
        }

        $currentIp = Mage::helper('core/http')->getRemoteAddr(); //echo $currentIp;// this will get the visitor ip address
        //$currentIp = '58.65.183.10';//'41.99.121.142'; //'58.65.183.10'; // testing purpose
        $remoteAddr = ip2long($currentIp); // convert ip into remote address
        $infoByIp = Mage::helper('geoipultimatelock')->getInfoByIp($currentIp); //print_r($infoByIp);exit; // get ip related info for country code and country name by remote id if exists
        $blockedIps = Mage::helper('geoipultimatelock')->getBlockedIps();
        /* priority 1 for , immidiate site block for blocked ips */
        if (in_array($currentIp, $blockedIps)) {

            if ($this->_isMessage) {

                echo Mage::app()->getLayout()
                        ->createBlock('core/template')
                        ->setBlockMessage($this->_isMessage)
                        ->setTemplate('geoipultimatelock/block.phtml')
                        ->toHtml();
                exit;
            } else {
                echo Mage::helper('geoipultimatelock')->__('Temporarily Shudown!');
                exit;
            }

            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('geoipultimatelock'));
            return;
        }

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

                        if (!empty($ids)) {
                            /* filtering collection */
                            $collection = $observer->getEvent()->getCollection();
                            $collection->addFieldToFilter('entity_id', array('nin' => $ids));
                        }
                    }
                }
            }// end foreach 
        }
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
        //$currentIp = '58.65.183.10';//'41.99.121.142'; //'58.65.183.10'; // testing purpose
        $remoteAddr = ip2long($currentIp); // convert ip into remote address
        $infoByIp = Mage::helper('geoipultimatelock')->getInfoByIp($currentIp); //print_r($infoByIp);exit; // get ip related info for country code and country name by remote id if exists
        $blockedIps = Mage::helper('geoipultimatelock')->getBlockedIps();
        /* priority 1 for , immidiate site block for blocked ips */
        if (in_array($currentIp, $blockedIps)) {

            if ($this->_isMessage) {

                echo Mage::app()->getLayout()
                        ->createBlock('core/template')
                        ->setBlockMessage($this->_isMessage)
                        ->setTemplate('geoipultimatelock/block.phtml')
                        ->toHtml();
                exit;
            } else {
                echo Mage::helper('geoipultimatelock')->__('Temporarily Shudown!');
                exit;
            }

            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('geoipultimatelock'));
            return;
        }
        
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

                        if (!empty($ids)) {
                            if (in_array($observer->getValue(), $ids)) {
								
								if ($this->_isMessage) {

									echo Mage::app()->getLayout()
											->createBlock('core/template')
											->setBlockMessage($this->_isMessage)
											->setTemplate('geoipultimatelock/block.phtml')
											->toHtml();
									exit;
								} else {
									echo Mage::helper('geoipultimatelock')->__('Temporarily Shudown!');
									exit;
								}
							}
                        }
                    }
                }
            }// end foreach 
        }
        
	}

}
