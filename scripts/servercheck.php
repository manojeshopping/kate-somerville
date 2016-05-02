<?php
/*
Check the server status
If server is down returns 0
*/

$url = "http://www.katesomerville.com";
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url ); // Connect to your server
curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11");
curl_setopt($curl, CURLOPT_TIMEOUT, 15);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_NOBODY, true);
curl_exec($curl);
$info = curl_getinfo($curl);
      
// Check server's state
if ((!curl_error($curl)) && ($info['http_code'] != 0)) {
 echo 1;
} else {
 echo 0;
}
curl_close($curl);
?>