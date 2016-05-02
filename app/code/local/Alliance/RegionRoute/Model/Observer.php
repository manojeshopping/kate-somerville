<?php

/**
 * Class Alliance_RegionRoute_Model_Observer
 */
class Alliance_RegionRoute_Model_Observer
{
	/**
	 * Handles all regional redirects
	 *
	 * Currently observing event 'controller_action_predispatch_cms_index_index'
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function regionRedirect(Varien_Event_Observer $observer)
	{	
		//google analytics id
		//$x_gclid = "";
		//if(isset($_GET['gclid'])) $x_gclid = $_GET['gclid'];
		$get_uri = "";  // Get full URI
		$get_uri = $_SERVER['QUERY_STRING'];
		
		$store_view_code = Mage::app()->getStore()->getCode();
				
		if ($store_view_code !== 'm_kate') {		
			if (@$region_redirect_domain = Mage::getModel('core/cookie')->get('katesomerville_region_route')) {			
				$parsed_url = parse_url(Mage::helper('core/url')->getCurrentUrl());
				$current_domain = preg_replace('#^www\.(.+\.)#i', '$1', $parsed_url['host']);
				if ($region_redirect_domain === $current_domain) {			
					// $this->_secondaryRedirect($current_domain);
					return;
				} else {
					$redirect_url = '//' . $region_redirect_domain;
					//if( !empty($x_gclid)) $redirect_url = $redirect_url . "?gclid=" . $x_gclid;
					if(!empty( $get_uri ) )   $redirect_url = $redirect_url . "?" . $get_uri;
					Mage::app()->getFrontController()->getResponse()->setRedirect($redirect_url)->sendResponse();
				}
			} else {			
				$redirect_url = Mage::getUrl('region');
				//if( !empty($x_gclid)) $redirect_url = $redirect_url . "?gclid=" . $x_gclid;
				if(!empty( $get_uri ) )   $redirect_url = $redirect_url . "?" . $get_uri;
				Mage::app()->getFrontController()->getResponse()->setRedirect($redirect_url)->sendResponse();
				exit;
			}
		}
	}

	/**
	 * Handles all secondary redirects (post-region redirect)
	 *
	 * @param $domain
	 * @return bool
	 */
	protected function _secondaryRedirect($domain)
	{
		$cookie = Mage::getModel('core/cookie');
		$first_visit = @$cookie->get('katesomerville_region_route_first_visit_us') == null;
		$redirect_domains = Mage::helper('alliance_regionroute')->getRedirectDomains();
		switch ($domain) {
			case $redirect_domains['us']:
				if (@$first_visit) {
					$redirect_url = '//' . $domain . '/meetkate/meet-kate/';
					$cookie->set('katesomerville_region_route_first_visit_us', 'false', 315360000, '/');
					Mage::app()->getFrontController()->getResponse()->setRedirect($redirect_url)->sendResponse();
				}
				break;
			default:
				return false;
		}
	}
}
