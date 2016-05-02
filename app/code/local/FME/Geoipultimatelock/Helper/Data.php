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
class FME_Geoipultimatelock_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getContinentsName($key) {
        $continentsArr = array(
            1 => 'Africa',
            2 => 'Asia',
            3 => 'Europe',
            4 => 'North_America',
            5 => 'Oceania',
            6 => 'South_America',
            7 => 'Others'
        );

        if (isset($continentsArr[$key])) {
            return $continentsArr[$key];
        }
    }

    public function allCmsPages() {
        $collection = Mage::getModel('cms/page')->getCollection()
                ->addFieldToFilter('is_active', 1);
        $_arr = array();

        foreach ($collection as $c) {
            $_arr[] = array(
                'label' => $c->getTitle(),
                'value' => $c->getId(),
            );
        }

        return $_arr;
    }

    public function convertFlatToRecursive(array $rule, $keys) {
        $arr = array();
        foreach ($rule as $key => $value) {
            if (in_array($key, $keys) && is_array($value)) {
                foreach ($value as $id => $data) {
                    $path = explode('--', $id);
                    $node = & $arr;
                    for ($i = 0, $l = sizeof($path); $i < $l; $i++) {
                        if (!isset($node[$key][$path[$i]])) {
                            $node[$key][$path[$i]] = array();
                        }
                        $node = & $node[$key][$path[$i]];
                    }
                    foreach ($data as $k => $v) {
                        $node[$k] = $v;
                    }
                }
            } else {
                if (in_array($key, array('from_date', 'to_date')) && $value) {
                    $value = Mage::app()->getLocale()->date(
                            $value, Varien_Date::DATE_INTERNAL_FORMAT, null, false
                    );
                }
            }
        }

        return $arr;
    }

    public function updateChild($array, $from, $to) {
        foreach ($array as $k => $rule) {
            foreach ($rule as $name => $param) {
                if ($name == 'type' && $param == $from)
                    $array[$k][$name] = $to;
            }
        }
        return $array;
    }

    public function checkVersion($version, $operator = '>=') {
        return version_compare(Mage::getVersion(), $version, $operator);
    }

    public function getcontinent($country = "") {
        switch ($country) {
            case "Algeria": {
                    $continent = 1;
                    break;
                }
            case "Angola": {
                    $continent = 1;
                    break;
                }
            case "Benin": {
                    $continent = 1;
                    break;
                }
            case "Botswana": {
                    $continent = 1;
                    break;
                }
            case "Burkina Faso": {
                    $continent = 1;
                    break;
                }
            case "Burundi": {
                    $continent = 1;
                    break;
                }
            case "Cameroon": {
                    $continent = 1;
                    break;
                }
            case "Cape Verde": {
                    $continent = 1;
                    break;
                }
            case "Central African Republic": {
                    $continent = 1;
                    break;
                }
            case "Chad": {
                    $continent = 1;
                    break;
                }
            case "Comoros": {
                    $continent = 1;
                    break;
                }
            case "Congo": {
                    $continent = 1;
                    break;
                }
            case "Congo, The Democratic Republic of the": {
                    $continent = 1;
                    break;
                }
            case "Djibouti": {
                    $continent = 1;
                    break;
                }
            case "Egypt": {
                    $continent = 1;
                    break;
                }
            case "Equatorial Guinea": {
                    $continent = 1;
                    break;
                }
            case "Eritrea": {
                    $continent = 1;
                    break;
                }
            case "Ethiopia": {
                    $continent = 1;
                    break;
                }
            case "Gabon": {
                    $continent = 1;
                    break;
                }
            case "Gambia": {
                    $continent = 1;
                    break;
                }
            case "Ghana": {
                    $continent = 1;
                    break;
                }
            case "Guinea": {
                    $continent = 1;
                    break;
                }
            case "Guinea-Bissau": {
                    $continent = 1;
                    break;
                }
            case "Cote D'Ivoire": {
                    $continent = 1;
                    break;
                }
            case "Kenya": {
                    $continent = 1;
                    break;
                }
            case "Lesotho": {
                    $continent = 1;
                    break;
                }
            case "Liberia": {
                    $continent = 1;
                    break;
                }
            case "Libyan Arab Jamahiriya": {
                    $continent = 1;
                    break;
                }
            case "Madagascar": {
                    $continent = 1;
                    break;
                }
            case "Malawi": {
                    $continent = 1;
                    break;
                }
            case "Mali": {
                    $continent = 1;
                    break;
                }
            case "Mauritania": {
                    $continent = 1;
                    break;
                }
            case "Mauritius": {
                    $continent = 1;
                    break;
                }
            case "Morocco": {
                    $continent = 1;
                    break;
                }
            case "Mozambique": {
                    $continent = 1;
                    break;
                }
            case "Namibia": {
                    $continent = 1;
                    break;
                }
            case "Niger": {
                    $continent = 1;
                    break;
                }
            case "Nigeria": {
                    $continent = 1;
                    break;
                }
            case "Rwanda": {
                    $continent = 1;
                    break;
                }
            case "Sao Tome and Principe": {
                    $continent = 1;
                    break;
                }
            case "Senegal": {
                    $continent = 1;
                    break;
                }
            case "Seychelles": {
                    $continent = 1;
                    break;
                }
            case "Sierra Leone": {
                    $continent = 1;
                    break;
                }
            case "Somalia": {
                    $continent = 1;
                    break;
                }
            case "South Africa": {
                    $continent = 1;
                    break;
                }
            case "Sudan": {
                    $continent = 1;
                    break;
                }
            case "Swaziland": {
                    $continent = 1;
                    break;
                }
            case "Tanzania, United Republic of": {
                    $continent = 1;
                    break;
                }
            case "Togo": {
                    $continent = 1;
                    break;
                }
            case "Tunisia": {
                    $continent = 1;
                    break;
                }
            case "Uganda": {
                    $continent = 1;
                    break;
                }
            case "Zambia": {
                    $continent = 1;
                    break;
                }
            case "Zimbabwe": {
                    $continent = 1;
                    break;
                }
            case "Afghanistan": {
                    $continent = 2;
                    break;
                }
            case "Bahrain": {
                    $continent = 2;
                    break;
                }
            case "Bangladesh": {
                    $continent = 2;
                    break;
                }
            case "Bhutan": {
                    $continent = 2;
                    break;
                }
            case "Brunei Darussalam": {
                    $continent = 2;
                    break;
                }
            case "Myanmar": {
                    $continent = 2;
                    break;
                }
            case "Cambodia": {
                    $continent = 2;
                    break;
                }
            case "China": {
                    $continent = 2;
                    break;
                }
            case "Timor-Leste": {
                    $continent = 2;
                    break;
                }
            case "India": {
                    $continent = 2;
                    break;
                }
            case "Indonesia": {
                    $continent = 2;
                    break;
                }
            case "Iran, Islamic Republic of": {
                    $continent = 2;
                    break;
                }
            case "Iraq": {
                    $continent = 2;
                    break;
                }
            case "Israel": {
                    $continent = 2;
                    break;
                }
            case "Japan": {
                    $continent = 2;
                    break;
                }
            case "Jordan": {
                    $continent = 2;
                    break;
                }
            case "Kazakhstan": {
                    $continent = 2;
                    break;
                }
            case "Korea, Democratic People's Republic of": {
                    $continent = 2;
                    break;
                }
            case "Korea, Republic of": {
                    $continent = 2;
                    break;
                }
            case "Kuwait": {
                    $continent = 2;
                    break;
                }
            case "Kyrgyzstan": {
                    $continent = 2;
                    break;
                }
            case "Lao People's Democratic Republic": {
                    $continent = 2;
                    break;
                }
            case "Lebanon": {
                    $continent = 2;
                    break;
                }
            case "Malaysia": {
                    $continent = 2;
                    break;
                }
            case "Maldives": {
                    $continent = 2;
                    break;
                }
            case "Mongolia": {
                    $continent = 2;
                    break;
                }
            case "Nepal": {
                    $continent = 2;
                    break;
                }
            case "Oman": {
                    $continent = 2;
                    break;
                }
            case "Pakistan": {
                    $continent = 2;
                    break;
                }
            case "Philippines": {
                    $continent = 2;
                    break;
                }
            case "Qatar": {
                    $continent = 2;
                    break;
                }
            case "Russian Federation": {
                    $continent = 2;
                    break;
                }
            case "Saudi Arabia": {
                    $continent = 2;
                    break;
                }
            case "Singapore": {
                    $continent = 2;
                    break;
                }
            case "Sri Lanka": {
                    $continent = 2;
                    break;
                }
            case "Syrian Arab Republic": {
                    $continent = 2;
                    break;
                }
            case "Tajikistan": {
                    $continent = 2;
                    break;
                }
            case "Thailand": {
                    $continent = 2;
                    break;
                }
            case "Turkey": {
                    $continent = 2;
                    break;
                }
            case "Turkmenistan": {
                    $continent = 2;
                    break;
                }
            case "United Arab Emirates": {
                    $continent = 2;
                    break;
                }
            case "Uzbekistan": {
                    $continent = 2;
                    break;
                }
            case "Vietnam": {
                    $continent = 2;
                    break;
                }
            case "Yemen": {
                    $continent = 2;
                    break;
                }
            case "Albania": {
                    $continent = 3;
                    break;
                }
            case "Andorra": {
                    $continent = 3;
                    break;
                }
            case "Armenia": {
                    $continent = 3;
                    break;
                }
            case "Austria": {
                    $continent = 3;
                    break;
                }
            case "Azerbaijan": {
                    $continent = 3;
                    break;
                }
            case "Belarus": {
                    $continent = 3;
                    break;
                }
            case "Belgium": {
                    $continent = 3;
                    break;
                }
            case "Bosnia and Herzegovina": {
                    $continent = 3;
                    break;
                }
            case "Bulgaria": {
                    $continent = 3;
                    break;
                }
            case "Croatia": {
                    $continent = 3;
                    break;
                }
            case "Cyprus": {
                    $continent = 3;
                    break;
                }
            case "Czech Republic": {
                    $continent = 3;
                    break;
                }
            case "Denmark": {
                    $continent = 3;
                    break;
                }
            case "Estonia": {
                    $continent = 3;
                    break;
                }
            case "Finland": {
                    $continent = 3;
                    break;
                }
            case "France": {
                    $continent = 3;
                    break;
                }
            case "Georgia": {
                    $continent = 3;
                    break;
                }
            case "Germany": {
                    $continent = 3;
                    break;
                }
            case "Greece": {
                    $continent = 3;
                    break;
                }
            case "Hungary": {
                    $continent = 3;
                    break;
                }
            case "Iceland": {
                    $continent = 3;
                    break;
                }
            case "Ireland": {
                    $continent = 3;
                    break;
                }
            case "Italy": {
                    $continent = 3;
                    break;
                }
            case "Latvia": {
                    $continent = 3;
                    break;
                }
            case "Liechtenstein": {
                    $continent = 3;
                    break;
                }
            case "Lithuania": {
                    $continent = 3;
                    break;
                }
            case "Luxembourg": {
                    $continent = 3;
                    break;
                }
            case "Macedonia": {
                    $continent = 3;
                    break;
                }
            case "Malta": {
                    $continent = 3;
                    break;
                }
            case "Moldova, Republic of": {
                    $continent = 3;
                    break;
                }
            case "Monaco": {
                    $continent = 3;
                    break;
                }
            case "Montenegro": {
                    $continent = 3;
                    break;
                }
            case "Netherlands": {
                    $continent = 3;
                    break;
                }
            case "Norway": {
                    $continent = 3;
                    break;
                }
            case "Poland": {
                    $continent = 3;
                    break;
                }
            case "Portugal": {
                    $continent = 3;
                    break;
                }
            case "Romania": {
                    $continent = 3;
                    break;
                }
            case "San Marino": {
                    $continent = 3;
                    break;
                }
            case "Serbia": {
                    $continent = 3;
                    break;
                }
            case "Slovakia": {
                    $continent = 3;
                    break;
                }
            case "Slovenia": {
                    $continent = 3;
                    break;
                }
            case "Spain": {
                    $continent = 3;
                    break;
                }
            case "Sweden": {
                    $continent = 3;
                    break;
                }
            case "Switzerland": {
                    $continent = 3;
                    break;
                }
            case "Ukraine": {
                    $continent = 3;
                    break;
                }
            case "United Kingdom": {
                    $continent = 3;
                    break;
                }
            case "Antigua and Barbuda": {
                    $continent = 4;
                    break;
                }
            case "Bahamas": {
                    $continent = 4;
                    break;
                }
            case "Barbados": {
                    $continent = 4;
                    break;
                }
            case "Belize": {
                    $continent = 4;
                    break;
                }
            case "Canada": {
                    $continent = 4;
                    break;
                }
            case "Costa Rica": {
                    $continent = 4;
                    break;
                }
            case "Cuba": {
                    $continent = 4;
                    break;
                }
            case "Dominica": {
                    $continent = 4;
                    break;
                }
            case "Dominican Republic": {
                    $continent = 4;
                    break;
                }
            case "El Salvador": {
                    $continent = 4;
                    break;
                }
            case "Grenada": {
                    $continent = 4;
                    break;
                }
            case "Guatemala": {
                    $continent = 4;
                    break;
                }
            case "Haiti": {
                    $continent = 4;
                    break;
                }
            case "Honduras": {
                    $continent = 4;
                    break;
                }
            case "Jamaica": {
                    $continent = 4;
                    break;
                }
            case "Mexico": {
                    $continent = 4;
                    break;
                }
            case "Nicaragua": {
                    $continent = 4;
                    break;
                }
            case "Panama": {
                    $continent = 4;
                    break;
                }
            case "Saint Kitts and Nevis": {
                    $continent = 4;
                    break;
                }
            case "Saint Lucia": {
                    $continent = 4;
                    break;
                }
            case "Saint Vincent and the Grenadines": {
                    $continent = 4;
                    break;
                }
            case "Trinidad and Tobago": {
                    $continent = 4;
                    break;
                }
            case "United States": {
                    $continent = 4;
                    break;
                }
            case "Australia": {
                    $continent = 5;
                    break;
                }
            case "Fiji": {
                    $continent = 5;
                    break;
                }
            case "Kiribati": {
                    $continent = 5;
                    break;
                }
            case "Marshall Islands": {
                    $continent = 5;
                    break;
                }
            case "Micronesia, Federated States of": {
                    $continent = 5;
                    break;
                }
            case "Nauru": {
                    $continent = 5;
                    break;
                }
            case "New Zealand": {
                    $continent = 5;
                    break;
                }
            case "Palau": {
                    $continent = 5;
                    break;
                }
            case "Papua New Guinea": {
                    $continent = 5;
                    break;
                }
            case "Samoa": {
                    $continent = 5;
                    break;
                }
            case "Solomon Islands": {
                    $continent = 5;
                    break;
                }
            case "Tonga": {
                    $continent = 5;
                    break;
                }
            case "Tuvalu": {
                    $continent = 5;
                    break;
                }
            case "Vanuatu": {
                    $continent = 5;
                    break;
                }
            case "Argentina": {
                    $continent = 6;
                    break;
                }
            case "Bolivia": {
                    $continent = 6;
                    break;
                }
            case "Brazil": {
                    $continent = 6;
                    break;
                }
            case "Chile": {
                    $continent = 6;
                    break;
                }
            case "Colombia": {
                    $continent = 6;
                    break;
                }
            case "Ecuador": {
                    $continent = 6;
                    break;
                }
            case "Guyana": {
                    $continent = 6;
                    break;
                }
            case "Paraguay": {
                    $continent = 6;
                    break;
                }
            case "Peru": {
                    $continent = 6;
                    break;
                }
            case "Suriname": {
                    $continent = 6;
                    break;
                }
            case "Uruguay": {
                    $continent = 6;
                    break;
                }
            case "Venezuela": {
                    $continent = 6;
                    break;
                }

            default: {
                    $continent = 7;
                }
        }

        return $continent;
    }

    public function getCountries() {
        try {
            //get read connection
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $table_cl = $resource->getTableName('geoip_cl');
            $query = "SELECT * FROM {$table_cl} ORDER BY cn";
            $results = $readConnection->fetchAll($query);
            return $results;
        } catch (Exception $ex) {
            
        }
    }

    public function getBlockedCountries($groupId) {
        try {
            //get read connection
            $resource = Mage::getSingleton('core/resource');

            $read = $resource->getConnection('core_read');

            $table = $resource->getTableName('geoipultimatelock');
            $query = "SELECT blocked_countries FROM {$table} WHERE geoipultimatelock_id = '{$groupId}'";
            $results = $read->fetchRow($query); //echo '<pre>';print_r(unserialize($results['blocked_countries']));echo '</pre>';//exit;

            return unserialize($results['blocked_countries']);
        } catch (Exception $ex) {
            
        }
    }

    public function isTableExists($table, $dbName = '') {

        if ($dbName == '') {
            $dbName = (string) Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');
        }
        $resource = Mage::getSingleton('core/resource');
        $table = $resource->getTableName($table);
        $isTableExists = $resource->getConnection('core_write')
                ->isTableExists($table, $dbName);

        return $isTableExists;
    }

    public function getDataByRemoteAddr($remoteAddr) {

        //filter_var($ip, FILTER_VALIDATE_IP);
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $table = $resource->getTableName('geoip_csv');
        
		try {
			$select = $read->select()
					->from(array('gcsv' => $table))
					//->where('gcsv.start >= (?)', $remoteAddr)
					->where('gcsv.end >= (?)', $remoteAddr) // reference: http://andy.wordpress.com/2007/12/16/fast-mysql-range-queries-on-maxmind-geoip-tables/
					->limit(1);
		

			return $read->fetchRow($select);
		} catch (Exception $ex) {
			
		}
    }

    public function getInfoByIp($remoteIp) {

        $remoteAddr = ip2long($remoteIp);
        $result = array();
        if (filter_var($remoteIp, FILTER_VALIDATE_IP)) {

            try {
                $res = Mage::getSingleton('core/resource');
                $read = $res->getConnection('core_read');

                /*$sql = $read->select()
                        ->from(array('gcsv' => $res->getTableName('geoip_csv')));
                $whereSql = "INET_ATON('{$remoteIp}') BETWEEN gcsv.start AND gcsv.end";*/

				$select = $read->select()
					->from(array('gcsv' => $res->getTableName('geoip_csv')))
					//->where('gcsv.start >= (?)', $remoteAddr)
					->where('gcsv.end >= (?)', $remoteAddr) // reference: http://andy.wordpress.com/2007/12/16/fast-mysql-range-queries-on-maxmind-geoip-tables/
					->limit(1);
                
                //$sql = $sql->where($whereSql);
               return $read->fetchRow($select);
            } catch (Exception $ex) {
                //Mage::getSingleton('core/session')->addError('Please import table first.');
                $result = array();
            }
        }

        return $result;
    }

    public function validateIpFilter($ip) {

        $isValid = false;

        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            $isValid = true;
        }

        return $isValid;
    }

    public function getBlockedIps($status = 2) {

        $model = Mage::getModel('geoipultimatelock/geoipblockedips');
        $collection = $model->getCollection()
                ->addFieldToFilter('status', $status);
        $ipsArr = array();

        foreach ($collection as $ip) {
            $ipsArr[] = $ip->getBlockedIp();
        }

        array_unique($ipsArr);

        return $ipsArr;
    }

    public function getFmeCmsContents() {
        return Mage::getStoreConfig("geoipultimatelock/basics/block_message");
    }
    
    /**
     * to get the filter for static block codes
     * recognition.
     * @param unknown $data contents of the editor
     * @return unknown filtered data
     * */
    public function getWysiwygFilter($data) {
        $helper = Mage::helper('cms');
        $processor = $helper->getPageTemplateProcessor();
        
        return $processor->filter($data);
    }
}
