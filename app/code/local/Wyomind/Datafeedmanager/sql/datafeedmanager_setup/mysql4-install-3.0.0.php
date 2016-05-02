<?php

$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('datafeedmanager')};
 ");


$installer->run("

CREATE TABLE IF NOT EXISTS `{$this->getTable('datafeedmanager')}` (
  `feed_id` int(11) NOT NULL auto_increment,
  `feed_name` varchar(20) NOT NULL,
  `feed_type` tinyint(3) NOT NULL,
  `feed_path` varchar(255) NOT NULL default '/',
  `feed_status` int(1) NOT NULL default '0',
  `feed_updated_at` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `store_id` int(2) NOT NULL default '1',
  `feed_include_header` int(1) NOT NULL default '0',
  `feed_header` text,
  `feed_product` text,
  `feed_footer` text,
  `feed_separator` char(3) default NULL,
  `feed_protector` char(1) default NULL,
  `feed_required_fields` text,
  `feed_enclose_data` int(1) NOT NULL default '1',
  `datafeedmanager_categories` longtext,
  `datafeedmanager_type_ids` varchar(150) default NULL,
  `datafeedmanager_visibility` varchar(10) default NULL,
  `datafeedmanager_attributes` text,
  `cron_expr` varchar(100) default '0 4 * * *',
  PRIMARY KEY  (`feed_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
");

$installer->run('
    INSERT INTO `'.$this->getTable("datafeedmanager").'` (`feed_id`, `feed_name`, `feed_type`, `feed_path`, `feed_status`, `feed_updated_at`, `store_id`, `feed_include_header`, `feed_header`, `feed_product`, `feed_footer`, `feed_separator`, `feed_protector`, `feed_required_fields`, `feed_enclose_data`, `datafeedmanager_categories`, `datafeedmanager_type_ids`, `datafeedmanager_visibility`, `datafeedmanager_attributes`, `cron_expr`) VALUES
(NULL, \'GoogleShopping\', 1, \'/feeds/\', 1, \'2011-10-02 20:04:26\', 1, 0, \'<?xml version="1.0" encoding="utf-8" ?>\r\n<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">  \r\n<channel>  \r\n<title>Data feed Title</title>\r\n<link>http://www.website.com</link>\r\n<description>Data feed description.</description>\', \'<item>
<g:id>{sku}</g:id>
{G:ITEM_GROUP_ID}
<title>{name,[substr],[70],[...]}</title>
<link>{url parent}</link>
<g:price>{normal_price,[USD]}</g:price>
{G:SALE_PRICE,[USD]}
<g:online_only>y</g:online_only>
<description>{description parent,[html_entity_decode],[strip_tags]}</description>
<g:condition>new</g:condition>
{G:PRODUCT_TYPE parent}
{G:GOOGLE_PRODUCT_CATEGORY parent}
{G:IMAGE_LINK parent}
<g:availability>{is_in_stock parent?[in stock]:[out of stock]}</g:availability>
<g:quantity>{qty}</g:quantity>
<g:featured_product>{is_special_price?[1]:[0]} </g:featured_product>
<g:color>{color,[implode],[,]}</g:color>
<g:shipping_weight>{weight,[float],[2]} kilograms</g:shipping_weight>
{G:PRODUCT_REVIEW}
<g:manufacturer>{manufacturer}</g:manufacturer>
<!-- In most of cases brand + mpn are sufficient, eg. :-->
<g:brand>{manufacturer}</g:brand>
<g:mpn>{sku}</g:mpn>
<!-- But it is better to use one of these identifiers if available : EAN, ISBN or UPC, eg : -->
<g:gtin>{upc}</g:gtin>
</item>\', \'</channel>\r\n</rss>\', \';\', \'\', \'\', 1, \'*\', \'simple,configurable,bundle,grouped,virtual,downloadable\', \'1,2,3,4\', \'[{"line": "0", "checked": true, "code": "price", "condition": "gt", "value": "0"}, {"line": "1", "checked": true, "code": "name", "condition": "notnull", "value": ""}, {"line": "2", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "3", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "4", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "5", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "6", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "7", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "8", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "9", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "10", "checked": false, "code": "cost", "condition": "eq", "value": ""}]\', \'0 4 * * *\'),
(NULL, \'le_guide\', 1, \'/feeds/\', 1, \'2011-05-02 12:45:02\', 1, 0, \'<catalogue lang="FR" >\', \'<product place="{inc}">\r\n   <categorie>{categories}</categorie> \r\n   <identifiant_unique>{sku}</identifiant_unique>\r\n   <titre>{meta_title}</titre>\r\n   <prix currency="EUR">{price}</prix>\r\n   <url_produit>{url}</url_produit>\r\n   <url_image>{image}</url_image>\r\n   <description>{short_description}</description>\r\n   <frais_de_livraison>90</frais_de_livraison>\r\n   <D3E>0</D3E>\r\n   <disponibilite>{is_in_stock?[0]:[1]} </disponibilite>\r\n   <delai_de_livraison>5 jours</delai_de_livraison>\r\n   <? if({is_special_price}) return \'\'<prix_barre currency="EUR">{normal_price}</prix_barre>\'\';?>\r\n   <type_promotion>{is_special_price?[1]:[0]}</type_promotion>\r\n   <occasion>0</occasion>\r\n   <devise>EUR</devise>\r\n</product>\r\n\', \'</catalogue>\', \';\', \'\', \'\', 1, \'*\', \'simple,configurable,bundle,virtual,downloadable\', \'1,2,3,4\', \'[{"line": "0", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "1", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "2", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "3", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "4", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "5", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "6", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "7", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "8", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "9", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "10", "checked": false, "code": "cost", "condition": "eq", "value": ""}]\', \'0 4 * * *\'),
(NULL, \'twenga\', 3, \'/feeds/\', 1, \'2011-05-02 12:48:30\', 1, 1, \'{"header":["price", "product_url", "designation", "category", "image_url", "description", "merchant_id", "in_stock", "Stock_detail", "product_type", "condition"]}\', \'{"product":["{price}", "{url}", "{meta_title}", "{categories}", "{image}", "{short_description}", "{sku}", "{is_in_stock?[Y]:[N]}", "{qty}", "1", "0"]}\', \'\', \';\', \'"\', \'\', 1, \'*\', \'simple,configurable,bundle,virtual,downloadable\', \'1,2,3,4\', \'[{"line": "0", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "1", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "2", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "3", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "4", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "5", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "6", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "7", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "8", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "9", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "10", "checked": false, "code": "cost", "condition": "eq", "value": ""}]\', \'0 4 * * *\'),
(NULL, \'kelkoo\', 1, \'/feeds/\', 1, \'2011-05-02 10:34:41\', 1, 1, \'<?xml version="1.0" encoding="ISO-8859-1"?>\r\n<products>\', \'<product>\r\n   <id>{sku}</id>\r\n   <model>{meta_title}</model>\r\n   <description>{short_description,[substr],[180]}</description>\r\n   <price>{price}</price>\r\n   <url>{url}</url>\r\n   <merchantcat>{categories}</merchantcat>\r\n   <image>{image}</image>\r\n   <used>neuf</used>\r\n   <availability>{is_in_stock?[1]:[4]}</availability>\r\n   <deliveryprice>90.00</deliveryprice>\r\n   <deliverytime>Sous 5 jours</deliverytime>\r\n   <pricenorebate>{normal_price}</pricenorebate>\r\n   <percentagepromo><? return round(100-({special_price}*100/{normal_price}) ); ?></percentagepromo>\r\n   <promostart><? return date("Y-m-d",time()); ?></promostart>\r\n   <promoend><? return date("Y-m-d",time()+604800); ?></promoend>\r\n</product>\r\n\', \'</products>\', \';\', \'\', \'\', 1, \'*\', \'simple,configurable,bundle,virtual,downloadable\', \'1,2,3,4\', \'[{"line": "0", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "1", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "2", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "3", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "4", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "5", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "6", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "7", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "8", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "9", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "10", "checked": false, "code": "cost", "condition": "eq", "value": ""}]\', \'0 4 * * *\'),
(NULL, \'shopping_com\', 1, \'/feeds/\', 1, \'2011-05-02 12:50:50\', 1, 0, \'<Products>\', \'	<Product>			\r\n		<Merchant_SKU>{sku}</Merchant_SKU>		\r\n		<MPN></MPN>		\r\n		<UPC></UPC>		\r\n		<EAN></EAN>		\r\n		<ISBN></ISBN>		\r\n		<Manufacturer>{manufacturer}</Manufacturer>		\r\n		<Product_Name>{name}</Product_Name>		\r\n		<Product_URL>{url}</Product_URL> 		\r\n		<Mobile_URL></Mobile_URL> 		\r\n		<Current_Price>{price}</Current_Price> 		\r\n		<Original_Price>{normal_price}</Original_Price> 		\r\n		<Category_ID></Category_ID>		\r\n		<Category_Name>{categories}</Category_Name>		\r\n		<Sub-category_Name></Sub-category_Name>		\r\n		<Parent_SKU></Parent_SKU>		\r\n		<Parent_Name></Parent_Name>		\r\n		<Product_Description>{short_description}</Product_Description>		\r\n		<Stock_Description></Stock_Description>		\r\n		<Product_Bullet_Point_1></Product_Bullet_Point_1>		\r\n		<Product_Bullet_Point_2></Product_Bullet_Point_2>		\r\n		<Product_Bullet_Point_3></Product_Bullet_Point_3>		\r\n		<Product_Bullet_Point_4></Product_Bullet_Point_4>		\r\n		<Product_Bullet_Point_5></Product_Bullet_Point_5>		\r\n		<Image_URL>{image}</Image_URL>		\r\n		<Alternative_Image_URL_1>{image}</Alternative_Image_URL_1>		\r\n	\r\n		<Product_Type></Product_Type>		\r\n		<Style></Style>		\r\n		<Condition>Neuf</Condition>		\r\n		<Gender></Gender>		\r\n		<Department></Department>		\r\n		<Age_Range></Age_Range>		\r\n		<Color>Noir/Blanc</Color>		\r\n		<Material></Material>		\r\n		<Format></Format>		\r\n		<Team></Team>		\r\n		<League></League>		\r\n		<Fan_Gear_Type></Fan_Gear_Type>		\r\n		<Software_Platform></Software_Platform>		\r\n		<Software_Type></Software_Type>		\r\n		<Watch_Display_Type></Watch_Display_Type>		\r\n		<Cell_Phone_Type></Cell_Phone_Type>		\r\n		<Cell_Phone_Service_Provider></Cell_Phone_Service_Provider>		\r\n		<Cell_Phone_Plan_Type></Cell_Phone_Plan_Type>		\r\n		<Usage_Profile></Usage_Profile>		\r\n		<Size></Size>		\r\n		<Size_Unit_of_Measure></Size_Unit_of_Measure>		\r\n		<Product_Length></Product_Length>		\r\n		<Length_Unit_of_Measure></Length_Unit_of_Measure>		\r\n		<Product_Width></Product_Width >		\r\n		<Width_Unit_of_Measure></Width_Unit_of_Measure>		\r\n		<Product_Height></Product_Height>		\r\n		<Height_Unit_of_Measure></Height_Unit_of_Measure>		\r\n		<Product_Weight></Product_Weight>		\r\n		<Weight_Unit_of_Measure></Weight_Unit_of_Measure>		\r\n		<Unit_Price></Unit_Price>		\r\n		<Top_Seller_Rank></Top_Seller_Rank>		\r\n		<Product_Launch_Date></Product_Launch_Date>		\r\n		<Stock_Availability></Stock_Availability>		\r\n		<Shipping_Rate></Shipping_Rate>		\r\n		<Shipping_Weight></Shipping_Weight>		\r\n		<Estimated_Ship_Date></Estimated_Ship_Date>		\r\n		<Coupon_Code></Coupon_Code>		\r\n		<Coupon_Code_Description></Coupon_Code_Description>		\r\n		<Merchandising_Type></Merchandising_Type>		\r\n		<Bundle>Non</Bundle>		\r\n		<Related_Products></Related_Products>		\r\n	</Product>				\', \'</Products>	\', \';\', \'\', \'\', 1, \'*\', \'simple,configurable,bundle,virtual,downloadable\', \'1,2,3,4\', \'[{"line": "0", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "1", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "2", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "3", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "4", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "5", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "6", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "7", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "8", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "9", "checked": false, "code": "cost", "condition": "eq", "value": ""}, {"line": "10", "checked": false, "code": "cost", "condition": "eq", "value": ""}]\', \'0 4 * * *\');

');

if($_SERVER['HTTP_HOST']=="wyomind.com")
 $installer->run("UPDATE `{$this->getTable('datafeedmanager')}` SET datafeedmanager_categories ='[{\"line\": \"1/3\", \"checked\": false, \"mapping\": \"\"}, {\"line\": \"1/3/10\", \"checked\": false, \"mapping\": \"\"}, {\"line\": \"1/3/10/22\", \"checked\": false, \"mapping\": \"Furniture > Living Room Furniture\"}, {\"line\": \"1/3/10/23\", \"checked\": false, \"mapping\": \"Furniture > Bedroom Furniture\"}, {\"line\": \"1/3/13\", \"checked\": false, \"mapping\": \"\"}, {\"line\": \"1/3/13/12\", \"checked\": false, \"mapping\": \"Cameras & Optics\"}, {\"line\": \"1/3/13/12/25\", \"checked\": false, \"mapping\": \"Cameras & Optics > Camera & Optic Accessories\"}, {\"line\": \"1/3/13/12/26\", \"checked\": false, \"mapping\": \"Cameras & Optics > Cameras > Digital Cameras\"}, {\"line\": \"1/3/13/15\", \"checked\": false, \"mapping\": \"\"}, {\"line\": \"1/3/13/15/27\", \"checked\": false, \"mapping\": \"Electronics > Computers > Desktop Computers\"}, {\"line\": \"1/3/13/15/28\", \"checked\": false, \"mapping\": \"Electronics > Computers > Desktop Computers\"}, {\"line\": \"1/3/13/15/29\", \"checked\": false, \"mapping\": \"Electronics > Computers > Computer Accessorie\"}, {\"line\": \"1/3/13/15/30\", \"checked\": false, \"mapping\": \"Electronics > Computers > Computer Accessorie\"}, {\"line\": \"1/3/13/15/31\", \"checked\": false, \"mapping\": \"Electronics > Computers > Computer Accessorie\"}, {\"line\": \"1/3/13/15/32\", \"checked\": false, \"mapping\": \"Electronics > Computers > Computer Accessorie\"}, {\"line\": \"1/3/13/15/33\", \"checked\": false, \"mapping\": \"Electronics > Computers > Computer Accessorie\"}, {\"line\": \"1/3/13/15/34\", \"checked\": false, \"mapping\": \"Electronics > Computers > Computer Accessorie\"}, {\"line\": \"1/3/13/8\", \"checked\": false, \"mapping\": \"Electronics > Communications > Telephony > Mobile Phones\"}, {\"line\": \"1/3/18\", \"checked\": false, \"mapping\": \"\"}, {\"line\": \"1/3/18/19\", \"checked\": false, \"mapping\": \"Apparel & Accessories > Clothing > Activewear > Sweatshirts\"}, {\"line\": \"1/3/18/24\", \"checked\": false, \"mapping\": \"Apparel & Accessories > Clothing > Pants\"}, {\"line\": \"1/3/18/4\", \"checked\": false, \"mapping\": \"Apparel & Accessories > Clothing > Tops > Shirts\"}, {\"line\": \"1/3/18/5\", \"checked\": false, \"mapping\": \"Apparel & Accessories > Shoes\"}, {\"line\": \"1/3/18/5/16\", \"checked\": false, \"mapping\": \"Apparel & Accessories > Shoes\"}, {\"line\": \"1/3/18/5/17\", \"checked\": false, \"mapping\": \"Apparel & Accessories > Shoes\"}, {\"line\": \"1/3/20\", \"checked\": false, \"mapping\": \"\"}]'");


$installer->endSetup();