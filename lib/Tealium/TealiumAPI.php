<?php
include 'Tealium.php';

// Each action corresponds to a method and renders accordingly. 
// if attempting to use an action not defined in this array it will display the help output
// as long as "help => true" is set, "help => false" will only return "action not found"

$actions = array(
		'render' => true,
		'tag' => true,
		'udo' => true,
		'help' => true
); // setting any of these to false will disable that action

$action = ($_REQUEST['action'] && preg_match('/[.\-\w\d]+/',$_REQUEST['action'])) ? $_REQUEST['action'] : "help";
$account = ($_REQUEST['account'] && preg_match('/[.\-\w\d]+/',$_REQUEST['account'])) ? $_REQUEST['account'] : false;
$profile = ($_REQUEST['profile'] && preg_match('/[.\-\w\d]+/',$_REQUEST['profile'])) ? $_REQUEST['profile'] : false;
$target = ($_REQUEST['target'] && preg_match('/[.\-\w\d]+/',$_REQUEST['target'])) ? $_REQUEST['target'] : false;
$pageType = ($_REQUEST['page_type'] && preg_match('/[.\-\w\d]+/',$_REQUEST['page_type'])) ? $_REQUEST['page_type'] : "Home";
echo $action;
if ($actions[$action]) {
		header('Content-type: text/html');
		$action();
}
else {
		header('Content-type: text/html');
		if ($action['help']){
			help();
		}
		else {
			echo "action not found";
		}
}
function render()
{
		global $account, $profile, $target, $pageType;
		$tealium = new Tealium($account, $profile, $target, $pageType);
		echo $tealium;
}
function tag()
{
		global $account, $profile, $target;
		$tealium = new Tealium($account, $profile, $target, null);
		echo $tealium;
}
function udo()
{
		global $pageType;
		$tealium = new Tealium(null, null, null, $pageType);
		echo $tealium->udo;
}
function help()
{
		echo <<<EOD
<!-- Tealium not implemented correctly
		send GET/POST with following parameters:
		action:
			render -> will return UDO and async tag - requires account,profile,target,page_type -,
			tag -> will return async tag only - requires account,profile,target -,
			udo -> will reyrn UDO only - requires page_type -,
			
		acount: name of tealium account (.-A-Z1-10),
		profile: name of tealium profile (.-A-Z1-10),
		target: target environment [dev,qa,prod,custom] (.-A-Z1-10)
		page_type: UDO page_type to return [Home, Search, Category, ...] (.-A-Z1-10)
		-->
		
EOD;
}
?>
