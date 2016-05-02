<?php

/**
 * Class Alliance_RegionRoute_Helper_Data
 */
class Alliance_RegionRoute_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Fetches geographical information on an IP using the geoplugin.net API
     * If no IP is supplied, will default to $_SERVER['REMOTE_ADDR']
     * If no purpose is supplied, will default to 'location' (returns full array)
     *
     * @param null $ip
     * @param string $purpose
     * @param bool $deep_detect
     * @return array|null|string
     */
    public function getIpInfo($ip = NULL, $purpose = "location", $deep_detect = TRUE)
    {
        $output = NULL;
        if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
            $ip = $_SERVER["REMOTE_ADDR"];
            if ($deep_detect) {
                if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
        }
        $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
        $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
        $continents = array(
            "AF" => "Africa",
            "AN" => "Antarctica",
            "AS" => "Asia",
            "EU" => "Europe",
            "OC" => "Australia (Oceania)",
            "NA" => "North America",
            "SA" => "South America"
        );
        if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
            $ipdat = @json_decode($this->curl_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
            if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
                switch ($purpose) {
                    case "location":
                        $output = array(
                            "city"           => @$ipdat->geoplugin_city,
                            "state"          => @$ipdat->geoplugin_regionName,
                            "country"        => @$ipdat->geoplugin_countryName,
                            "country_code"   => @$ipdat->geoplugin_countryCode,
                            "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                            "continent_code" => @$ipdat->geoplugin_continentCode
                        );
                        break;
                    case "address":
                        $address = array($ipdat->geoplugin_countryName);
                        if (@strlen($ipdat->geoplugin_regionName) >= 1)
                            $address[] = $ipdat->geoplugin_regionName;
                        if (@strlen($ipdat->geoplugin_city) >= 1)
                            $address[] = $ipdat->geoplugin_city;
                        $output = implode(", ", array_reverse($address));
                        break;
                    case "city":
                        $output = @$ipdat->geoplugin_city;
                        break;
                    case "state":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "region":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "country":
                        $output = @$ipdat->geoplugin_countryName;
                        break;
                    case "countrycode":
                        $output = @$ipdat->geoplugin_countryCode;
                        break;
                }
            }
        }
        return $output;
    }

    /**
     * Replacement for core function file_get_contents() using cURL instead
     *
     * @param $url
     * @return mixed
     */
    public function curl_get_contents($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    /**
     * Returns an array of redirect domains
     *
     * @return array
     */
    public function getRedirectDomains()
    {
        $parsed_default_url = parse_url(Mage::app()->getStore('default')->getBaseUrl());
		$universal_base_url = preg_replace('#^www\.(.+\.)#i', '$1', $parsed_default_url['host']);

        $redirect_domains = array();
        $redirect_domains['us'] = $universal_base_url;
        $redirect_domains['uk'] = 'uk.'.$universal_base_url;
        $redirect_domains['ca'] = 'ca.'.$universal_base_url;
        $redirect_domains['hk'] = 'hk.'.$universal_base_url;
        $redirect_domains['kr'] = 'www.katesomerville.co.kr';

        return $redirect_domains;
    }

    /**
     * Returns the base domain by country code. If no domain is found, returns false
     *
     * Defaults to US domain
     *
     * @param string $country_code
     * @return bool
     */
    public function getDomain($country_code = 'us')
    {
        if ($redirect_domains = $this->getRedirectDomains()) {
            if (array_key_exists($country_code, $redirect_domains)) {
                return $redirect_domains[$country_code];
            }
        }
        return false;
    }

	public function getCookieDomain()
	{
		$parsed_default_url = parse_url(Mage::app()->getStore('default')->getBaseUrl());
		$universal_base_url = preg_replace('#^www\.(.+\.)#i', '$1', $parsed_default_url['host']);
		$cookie_domain = '.' . $universal_base_url;
		return $cookie_domain;
	}

	public function getCurrentDomain()
	{
		$parsed_url = parse_url(Mage::helper('core/url')->getCurrentUrl());
		$current_domain = preg_replace('#^www\.(.+\.)#i', '$1', $parsed_url['host']);
		
		return $current_domain;
	}
}
