<?php

/**
 * Class Alliance_RegionRoute_Block_Switch
 */
class Alliance_RegionRoute_Block_Switch extends Mage_Core_Block_Template
{
	/**
	 * Defines which country is selected by default on route region/index/index
	 *
	 * @var string
	 */
	public $default_country = null;

	/**
	 * Constructor loads default country on block instantiation
	 */
	public function __construct()
	{
		$parsed_url = parse_url(Mage::helper('core/url')->getCurrentUrl());
		$this->default_country = preg_replace('#^www\.(.+\.)#i', '$1', $parsed_url['host']);
	}

	/**
	 * Returns JSON-encoded array of redirect domains from 'alliance_regionroute/data' helper
	 *
	 * @return bool|float|string
	 */
	public function getRedirectDomainsJson()
	{
		$redirect_domains = Mage::helper('alliance_regionroute')->getRedirectDomains();
		return json_encode(array_values($redirect_domains));
	}
}