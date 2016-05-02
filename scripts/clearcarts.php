<?php
//$path = "/var/vhosts/katesomerville.jeff.alliance-global.com/";
$path = dirname(dirname(__FILE__));
$days = 10; //how many days to wait

$file = $path . "/app/etc/local.xml";
printf("file = " . $file . "\n");
$xml = simplexml_load_file($file);
$host = (string) $xml->global->resources->default_setup->connection->host;
$username = (string) $xml->global->resources->default_setup->connection->username;
$password = (string) $xml->global->resources->default_setup->connection->password;
$db = (string) $xml->global->resources->default_setup->connection->dbname;

$connect = mysql_connect($host, $username, $password);
mysql_select_db($db, $connect);


$query = "delete FROM sales_flat_quote where is_active = 1 and created_at < date_add(now(), interval - " . $days . " day)";
printf($query . "\n");
$results = mysql_query($query, $connect);

$num = mysql_affected_rows($connect);
printf("num rows = " . $num . "\n");
mysql_close($connect);

?>
