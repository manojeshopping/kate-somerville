<?php
class Alliance_StoreSelector{
		private $x_domain;
		private $x_is_subdomain;
		private $z_search_subdomain;
		private $z_subdomains;
		private $x_dom;
		private $x_store; 
		private $x_store_code;
		private $x_dom_found;

	public function main(){

		$x_domain = $this->getUrlProtocol().$_SERVER['HTTP_HOST'];
		$z_subdomains       = array("http://ca.", "https://ca.", "http://uk.", "https://uk.");

		foreach ( $z_subdomains as $value) {
			$x_search_subdomain  = strstr($x_domain, $value);
			if( !empty($x_search_subdomain) ){ 
				$x_dom_found = $value;
				break;
			}
		}
		
		if(!empty($x_dom_found)){
			$x_store = $this->getExtractUnit( $x_dom_found, "://", ".");
						
			$x_store_code = $this->getStoreCode($x_store);

			$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : $x_store;
			$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'website';
			
			Mage::run($mageRunCode, $mageRunType);	
		}else{
		
			$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';
			$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';
					
			$this->getUserAgentStore($mageRunCode, $mageRunType);  //UserAgent
		}
	}

	private function getStoreCode($store){
		$x_b2b = "professional";
		$x_b2b_code = "base_b2b";
		$x_store = $store;
				
		if($x_store == $x_b2b) return $x_b2b_code;
		return FALSE;
	}
	
	
	private function getUrlProtocol(){
		$protocol = "";
		if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == "on" || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == "https") {
			$protocol = "https://";
		}else {
			$protocol = "http://";
		}
		return	$protocol;
	}	
	
	
	private function getExtractUnit($string, $start, $end){
		$pos = stripos($string, $start);
		$str = substr($string, $pos);
		$str_two = substr($str, strlen($start)); 
		$second_pos = stripos($str_two, $end);
		$str_three = substr($str_two, 0, $second_pos);
		$unit = trim($str_three); // remove whitespaces

		return $unit;
	}
	
	
	private function getUserAgentStore($mageRunCode, $mageRunType){
		// ** STORE VIEW AGENT ** //
		if(isset($_COOKIE['iphone_store'])) $x_cookie_store = $_COOKIE['iphone_store'];
		else $x_cookie_store = '';

		if(isset($_GET['___store'])) $x_get_store = $_GET['___store'];
		else $x_get_store = "";
		if(!empty($x_get_store)) setcookie("store_switcher", $x_get_store, time() + (86400 * 30), "/");
		if(!empty($_COOKIE['store_switcher'])) $x_get_store = $_COOKIE['store_switcher'];
		if(!empty($x_get_store) && empty($x_cookie_store)  ) $x_cookie_store = $x_get_store;
				
		switch($x_cookie_store) {
			case 'm_kate':
				Mage::run('m_kate');
			break;
			case 't_kate':
				Mage::run('t_kate');
			break;
			case 'default':
				Mage::run($mageRunCode, $mageRunType);
			break;
			case 'ca':
				Mage::run($mageRunCode, $mageRunType);
			break;	
			case 'uk':
				Mage::run($mageRunCode, $mageRunType);
			break;
			default:
				$usedDevice = $this->getUsedDevice();				
				if($usedDevice == "mobile"){
					Mage::run('m_kate'); 
				} elseif($usedDevice == "tablet") {
					Mage::run('t_kate'); 
				}else{	
					Mage::run($mageRunCode, $mageRunType);
				}		
			break;
		}
		return TRUE;
	}
	
	
	private function getUsedDevice(){
	
		$tablet_browser = 0;
		$mobile_browser = 0;
		
		if(isset($_SERVER['HTTP_USER_AGENT'])) $sl_UserAgent = $_SERVER['HTTP_USER_AGENT'];
		else $sl_UserAgent = "";
		
		if(isset($_SERVER['HTTP_ACCEPT'])) $sl_Accept = $_SERVER['HTTP_ACCEPT'];
		else $sl_Accept = "";

		if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($sl_UserAgent))) {
			$tablet_browser++;
		}

		if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($sl_UserAgent))) {
			$mobile_browser++;
		}

		if ((strpos(strtolower($sl_Accept),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
			$mobile_browser++;
		}

		$mobile_ua = strtolower(substr($sl_UserAgent, 0, 4));
		$mobile_agents = array(
			'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
			'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
			'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
			'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
			'newt','noki','palm','pana','pant','phil','play','port','prox',
			'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
			'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
			'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
			'wapr','webc','winw','winw','xda ','xda-'
		);

		if (in_array($mobile_ua,$mobile_agents)) {
			$mobile_browser++;
		}

		if (strpos(strtolower($sl_UserAgent),'opera mini') > 0) {
			$mobile_browser++;
			//Check for tablets on opera mini alternative headers
			$stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
			if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
				$tablet_browser++;
			}
		}

		if ($tablet_browser > 0) {
			return "tablet";
		} else if ($mobile_browser > 0) {
			return "mobile";
		}
		return "desktop";
	}
}
$StoreSelector = new Alliance_StoreSelector();
$StoreSelector->main();