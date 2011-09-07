<?php


$ini_array = parse_ini_file('credentials.ini');

$host = $ini_array['host'];
$dbPass = $ini_array['password'];
$dbUser = $ini_array['username'];
$dbName = $ini_array['database'];
$developerKey = $ini_array['developerKey'];
$pass = $ini_array['pass'];
$accountID = $ini_array['account'];
$accountID2 = $ini_array['account2'];
$accountID3 = $ini_array['account3'];
$accountID4 = $ini_array['account4'];
$hostSD = $ini_array['hostSD'];
$userSD = $ini_array['userSD'];
$passSD = $ini_array['passSD'];
$hostSKU = $ini_array['hostSKU'];
$userSKU = $ini_array['userSKU'];
$passSKU = $ini_array['passSKU'];
$hostCAD = $ini_array['hostCAD'];
$userCAD = $ini_array['userCAD'];
$passCAD = $ini_array['passCAD'];
$hostCAMMG = $ini_array['hostCAMMG'];
$userCAMMG = $ini_array['userCAMMG'];
$passCAMMG = $ini_array['passCAMMG'];
$hostW = $ini_array['hostW'];
$userW = $ini_array['userW'];
$passW = $ini_array['passW'];



$entrydate = date("Y-m-d");
$entryDateSuperD = date("m/d/Y");
$trackingdate = date("Ymd");
$modTime = date("H:i:s");
$titleTime = date("His");



//soapCall

$client = new nusoap_client('https://api.channeladvisor.com/ChannelAdvisorAPI/v1/OrderService.asmx?WSDL',true);

$confirm = new nusoap_client('https://api.channeladvisor.com/ChannelAdvisorAPI/v2/ShippingService.asmx?WSDL',true);

?>