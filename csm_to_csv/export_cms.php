<?php
// Configuration.
define('MAGENTO_ROOT', "/var/vhosts/katedev113.dev2.alliance-global.com");
define('PROYECT_NAME', "katesomerville");
$CMS_array = array(

'anti-aging-skin-care-products',
'skin-library',
'acne-skin-care-products',
'combination-skin-care-products',
'anti-aging-acne-skin-care-products',
'sensitive-skin-care-products',
'oily-skin-care-products',
'normal-skin-care-products',
'sensitive-oily-skin-care-products',
'dry-skin-care-products',
'anti-aging-skin-products',
'acne-treatment-skin-care',
'skin-care-for-acne',
'supplements-for-acne',
'body-acne-treatment',
'acne-scar-treatment',
'rosacea-skin-care-products',
'get-rid-of-blackheads',
'back-acne-treatment',
'facial-acne-treatments',
'acne-skin-cream',
'cystic-acne-treatment',
'acne-treatment-cream',
'effective-acne-treatment',
'best-acne-treatment',
'acne-care-treatment',
'acne-treatment-system',
'vitamins-for-acne',
'acne-cleanser',
'acne-skin-care-treatment',
'skin-care-products-online',
'serious-skin-care-products',
'acne-skin-care-system',
'aging-skin-care',
'anti-aging-skin-care',
'peptides-skin-care',
'hydrating-moisturizer',
'anti-aging-creams',
'younger-looking-skin',
'antioxidant-cream',
'anti-aging-vitamins',
'anti-aging-face-creams',
'anti-aging-skin',
'anti-aging-treatment',
'anti-wrinkle-face-cream',
'anti-aging-systems',
'anti-aging-supplements',
'acne-vitamins',
'anti-aging-serum',
'anti-aging-wrinkle-cream',
'age-spots-treatment',
'repair-sun-damaged-skin',
'anti-aging-skin',
'anti-aging-acne-skin-care',
'adult-acne-skin-care',
'adult-acne-products',
'moisturizer-for-acne-prone-skin',
'adult-acne-treatment',
'adult-acne-causes',
'adult-acne-solution',
'adult-acne-solution2',
'treating-adult-acne',
'dry-skin-care',
'dry-skin-care-polisher',
'dry-skin-products',
'dry-sensitive-skin-care',
'facial-dry-skin-care',
'dry-skin-moisturizer',
'moisturizer-for-dry-skin',
'sunscreen-for-dry-skin',
'dry-skin-treatment',
'oil-free-facial-moisturizer',
'oily-skin-care',
'skin-care-oily-skin',
'sensitive-skin-moisturizer',
'care-for-sensitive-skin',
'sensitive-skin-cleanser',
'sensitive-skin-treatment',
'sensitive-skin-serum',
'sensitive-skin-mask',
'sensitive-skin-acne',
'sensitive-skin-products',
'sensitive-skin-care',
'discoloration',
'cleanser-combination-skin',
'combination-skin-products',
'combination-skin-moisturizer',
'combination-skin-cream',
'combination-skin-treatment',
'face-cream-for-combination-skin',
'winter-skin-care',
'combination-skin-care',
'combination-skin-serum',
'sensitive-oily-skin-care',
'skin-care-for-oily-skin',
'acne-prone-sensitive-skin',
'skin-care-products-for-oily-skin',
'cleanser-normal-skin',
'moisturizer-normal-skin',
'serum-for-normal-skin',
'skin-care-for-normal-skin',
'anti-aging-and-acne',

'anti-aging-cms-links',
'acne-cms-links',
'anti-aging-acne-skin-care-cms-links',
'anti-aging-acne-cms-links',
'combination-cms-links',
'dry-skin-cms-links',
'discoloration-cms-links',
'normal-cms-links',
'oily-cms-links',
'skin-library-left-menu', 
'sensitive-cms-links',
'sensitive-oily-cms-links',		
	
	
);


// Check CMS Array.
if(empty($CMS_array)) {
	printLog("Empty CMS_array", false, true);
}


$csvFile = __DIR__ . "/".PROYECT_NAME."-".time().".csv";

set_time_limit(0); // Set max limit.


// Load Magento environment.
require_once MAGENTO_ROOT.'/app/Mage.php';
$app = Mage::app('default');

// Load Magento DB Resource
$resource = Mage::getSingleton('core/resource');
$conn = $resource->getConnection('core_read');

// Open file.
$fp = fopen($csvFile, 'w');
fputcsv($fp, array(
	'identifier',
	'table',
	'title',
	'content',
	'creation_time',
	'update_time',
	'is_active',
	
	'stores',
	
	'root_template',
	'meta_keywords',
	'meta_description',
	'content_heading',
	'sort_order',
	'layout_update_xml',
	'custom_theme',
	'custom_root_template',
	'custom_layout_update_xml',
	'custom_theme_from',
	'custom_theme_to',
	'published_revision_id',
	'website_root',
	'under_version_control',
));

printLog("===============", true);
printLog("Blocks", true);
printLog("===============", true);
// Get all attributes codes.
$sql = "SELECT B.*, GROUP_CONCAT(DISTINCT store_id) AS stores ";
$sql .= "FROM cms_block B ";
$sql .= "LEFT JOIN cms_block_store BS ON BS.block_id = B.block_id ";
$sql .= "WHERE B.identifier IN('".implode("','", $CMS_array)."') ";
$sql .= "GROUP BY B.block_id";
$blocks = $conn->fetchAll($sql);

$blocksCount = 0;
$processedCount = 0;
foreach($blocks as $_block) {
	$blocksCount++;
	$block_id = $_block['block_id'];
	$identifier = $_block['identifier'];
	printLog("#".$blocksCount." - ID: ".$block_id." - identifier: ".$identifier);
	
	fputcsv($fp, array(
		$identifier,
		'block',
		$_block['title'],
		$_block['content'],
		$_block['creation_time'],
		$_block['update_time'],
		$_block['is_active'],
		$_block['stores'],
	));
	
	$processedCount++;
	printLog(" - OK", true);
}
printLog("===============", true);
printLog("Group Count: ".$blocksCount, true);
printLog("Processed Count: ".$processedCount, true);
printLog("===============", true);


printLog("===============", true);
printLog("Pages", true);
printLog("===============", true);
// Get all attributes codes.
$sql = "SELECT B.*, GROUP_CONCAT(DISTINCT store_id) AS stores ";
$sql .= "FROM cms_page B ";
$sql .= "LEFT JOIN cms_page_store BS ON BS.page_id = B.page_id ";
$sql .= "WHERE B.identifier IN('".implode("','", $CMS_array)."')";
$sql .= "GROUP BY B.page_id";
$pages = $conn->fetchAll($sql);

$pagesCount = 0;
$processedCount = 0;
foreach($pages as $_page) {
	$pagesCount++;
	$page_id = $_page['page_id'];
	$identifier = $_page['identifier'];
	printLog("#".$pagesCount." - ID: ".$page_id." - identifier: ".$identifier);
	
	fputcsv($fp, array(
		$identifier,
		'page',
		$_page['title'],
		$_page['content'],
		$_page['creation_time'],
		$_page['update_time'],
		$_page['is_active'],
		$_page['stores'],
	
		$_page['root_template'],
		$_page['meta_keywords'],
		$_page['meta_description'],
		$_page['content_heading'],
		$_page['sort_order'],
		$_page['layout_update_xml'],
		$_page['custom_theme'],
		$_page['custom_root_template'],
		$_page['custom_layout_update_xml'],
		$_page['custom_theme_from'],
		$_page['custom_theme_to'],
		$_page['published_revision_id'],
		$_page['website_root'],
		$_page['under_version_control'],
	));
	
	$processedCount++;
	printLog(" - OK", true);
}
printLog("===============", true);
printLog("Pages Count: ".$pagesCount, true);
printLog("Processed Count: ".$processedCount, true);
printLog("===============", true);


fclose($fp);
die("\n");



function printLog($msg, $break = false, $fatal = false)
{
	// Print on screen.
	echo $msg;
	if($break) echo "\n";
	
	// Save log.
	// Mage::log($msg, null, 'export_orders.log');
	
	if($fatal) die("\n");
}





