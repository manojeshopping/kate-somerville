<?php // Tealium.php Class file


// This interface describes the methods available for the class object and usage
interface TealiumInterface {
		public function updateUdo($objectOrKey, $value);
		// update only part of the UDO currently set in object
		// $tealium->updateUdo($objectOrKey, $value)
		// to update multiple values: $tealium->updateUDO(array(key1=>"value1", key2=>"value2",...))
		// to update one value: $tealium->updateUDO("key", "value")
		//
		// to update a specific page in the UDO object: $tealium->updateUDO("key", "value", PAGE_TYPE);
		// to update a specific page in the UDO object: $tealium->updateUDO(array(key1=>"value1", key2=>"value2",...), null, PAGE_TYPE);
		
		public function pageType($pageType);
		// Set page type for udo to render: $tealium->pageType("Home")
		
		public function render();
		// return UDO and async utag.js call in HTML format
		// print $tealium->render(); 
		// alernate: print $tealium;
		//
		// to print only tag: print $tealium->render("tag");
		// to print only UDO: print $tealium->render("udo");
		// to print only json object: print $tealium->render("json");
		
}
class Tealium implements TealiumInterface

{

		private $account, $profile, $target, $udo, $access, $udoElements;
		public function __construct($accountInit = false, $profileInit = false, $targetInit = false, $pageType = "Home", $data = array(
)) {
				$this->access = array(
						'account' => array(
								'type' => "string",
								'read' => true,
								'write' => false
						) ,
						'profile' => array(
								'type' => "string",
								'read' => true,
								'write' => false
						) ,
						'target' => array(
								'type' => "string",
								'read' => true,
								'write' => false
						) ,
						'udo' => array(
								'type' => "array",
								'read' => true,
								'write' => false
						) ,
						'access' => array(
								'type' => "array",
								'read' => false,
								'write' => false
						)
				);
				require 'TealiumInit.php';

				$this->udoElements = $udoElements;
				$this->account = $accountInit;
				$this->profile = $profileInit;
				$this->target = $targetInit;
				$this->udo = $this->udoElements[$pageType] ? : ($pageType == null ? $pageType : array(
						'page_type' => $pageType
				));
		}
		public function __toString() {
				return $this->render();
		}
		public function __call($name, $arguments) {
				if ($this->{$name}){
					return false;
				}
				if ($udoElements[$name]) {
						$newUdo = array_shift($arguments);
						$this->$udo = ($newUdo) ? : $udoElements[$name];
				}
				else {
						$newUdo = array_shift($arguments);
						$this->$udo = ($newUdo) ? : array();
				}
				return $this->render();
		}
		public function __set($name, $value){
				if(!$this->{$name}){
						$this->{$name} = $value;
				}
				elseif($this->{$name} && $this->access[$name]){
						if($this->access[$name]['write']){
								settype($value, $this->access[$name]['type']);
								$this->{$name} = $value;
						}
				}
				elseif($this->{$name}) {
						$this->{$name} = $value;
				}
		}
		public function __get($name) {
				if ($this->{$name} && $this->access[$name]) {
					if ($this->access[$name]['read']){
							return (gettype($this->{$name}) == "array") ? json_encode($this->udo, JSON_PRETTY_PRINT) : $this->{$name};
					}
					else {
							return "[private]";
					}
				}
				elseif($this->{$name}) {
						return $this->{$name};
				}
				elseif(!$this->{$name}) {
						return "no value set for property: $name";
				}
		}
		public function updateUdo($objectOrKey = "", $value = "", $pageType = null) {
				if (is_array($objectOrKey)) {
						foreach($objectOrKey as $key => $value) {
								if (!$pageType) {
										$this->udo[$key] = $value;
								}
								else {
										$this->udoElements[$pageType][$key] = $value;
								}
						}
				}
				elseif ($objectOrKey != "") {
						if (!$pageType) {
								$this->udo[$objectOrKey] = $value;
						}
						else {
								$this->udoElements[$pageType][$objectOrKey] = $value;
						}
				}
				return $this->udo;
		}
		public function pageType($pageType = "Home") {
				$this->udo = $this->udoElements[$pageType] ? : array(
						'page_type' => $pageType
				);
		}
		public function render($type = null) {
				// Basic JSON object of all variables in the default data layer`
				$udoString = json_encode($this->udo);
				// Render UDO object in javaScript
				$udo = <<<EOD
<!-- Tealium Universal Data Object / Data Layer -->
<script type="text/javascript">
    utag_data = $udoString;
</script>
<!-- ****************************************** -->
EOD;
				// Render Tealium tag in javaScript
				$tag = <<<EOD
<!-- Async Load of Tealium utag.js library -->
<script type="text/javascript">
    (function(a,b,c,d){
        a='//tags.tiqcdn.com/utag/$this->account/$this->profile/$this->target/utag.js';
        b=document;c='script';d=b.createElement(c);d.src=a;d.type='text/java'+c;d. 
        async=true;
        a=b.getElementsByTagName(c)[0];a.parentNode.insertBefore(d,a);
        })();
</script>
<!-- ************************************* -->
EOD;
				// Determine what code to return
				if ($this->account && $this->profile && $this->target) {
						if ($type == "tag") {
								$renderedCode = $tag;
						}
						elseif ($type == "udo") {
								$renderedCode = $udo;
						}
						elseif ($type == "json") {
								$renderedCode = $udoString;
						}
						else {
								$renderedCode = $udo . "\n" . $tag;
						}
				}
				else {
						if ($this->udo != null) {
								$renderedCode = $udo;
						}
						else {
								// Render instructions if Tealium Object was not used correctly
								$renderedCode = <<<EOD
<!-- Tealium Universal Data Object / Data Layer -->
<!-- Account, profile, or environment was not declared in 
    object Tealium(\$account, \$profile, \$target, \$pageType) -->
EOD;
						}
				}
				return $renderedCode;
		}
}
// Open source alternative for json_encode for PHP < 5.4 ***********************************************
if (!function_exists('json_encode')) {
		function json_encode($a = false)
		{
				if (is_null($a)) return 'null';
				if ($a === false) return 'false';
				if ($a === true) return 'true';
				if (is_scalar($a)) {
						if (is_float($a)) {
								// Always use "." for floats.
								return floatval(str_replace(",", ".", strval($a)));
						}
						if (is_string($a)) {
								static $jsonReplaces = array(
										array(
												"\\",
												"/",
												"\n",
												"\t",
												"\r",
												"\b",
												"\f",
												'"'
										) ,
										array(
												'\\\\',
												'\\/',
												'\\n',
												'\\t',
												'\\r',
												'\\b',
												'\\f',
												'\"'
										)
								);
								return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
						}
						else return $a;
				}
				$isList = true;
				for ($i = 0, reset($a); $i < count($a); $i++, next($a)) {
						if (key($a) !== $i) {
								$isList = false;
								break;
						}
				}
				$result = array();
				if ($isList) {
						foreach($a as $v) $result[] = json_encode($v);
						return '[' . join(',', $result) . ']';
				}
				else {
						foreach($a as $k => $v) $result[] = json_encode($k) . ':' . json_encode($v);
						return '{' . join(',', $result) . '}';
				}
		}
}
// ***********************************************************************************************************

?>

