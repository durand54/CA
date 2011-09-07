<?php

/**
 * soap
 */
require('processes/nusoap-0/lib/good/nusoap.php');

/**
 * database key
 */
require('lib/configOS.php');

/**
 * soap
 */
$client->soap_defencoding = 'UTF-8';
$client->decode_utf8 = false;
$err = $client->getError();
if($err){
echo 'Constructor error: '.$err.'';
}


$_ENV['CONFIG'] = array(
'shipping_rates' => array(
        1 => 1.48,  
        2 => 1.48, 
        3 => 1.48, 
        4 => 1.63, 
        5 => 1.78, 
        6 => 1.95, 
        7 => 2.13,
	8 => 2.29,
	9 => 2.45,
	10 => 2.57,
	11 => 2.57,
	12 => 2.57,
	13 => 2.57,
	14 => 2.57,
	15 => 2.57,
	16 => 2.57,
	17 => 2.96,
	18 => 2.96,
	19 => 2.96,
	20 => 2.96,
	21 => 2.96,
	22 => 2.96,
	23 => 2.96,
	24 => 2.96,
	25 => 2.96,
	26 => 2.96,
	27 => 2.96,
	28 => 2.96,
	29 => 2.96,
	30 => 2.96,
	31 => 2.96,
	32 => 2.96,
	33 => 3.35,
	34 => 3.35,
	35 => 3.35,
	36 => 3.35,
	37 => 3.35,
	38 => 3.35,
	39 => 3.35,
	40 => 3.35,
	41 => 3.35,
	42 => 3.35,
	43 => 3.35,
	44 => 3.35,
	45 => 3.35,
	46 => 3.35,
	47 => 3.35,
	48 => 3.35,
	49 => 3.74,
	50 => 3.74,
	51 => 3.74,
	52 => 3.74,
	53 => 3.74,
	54 => 3.74,
	55 => 3.74,
	56 => 3.74,
	57 => 3.74,
	58 => 3.74,
	59 => 3.74,
	60 => 3.74,
	61 => 3.74,
	62 => 3.74,
	63 => 3.74,
	64 => 3.74,
	65 => 4.13,
	66 => 4.13,
	67 => 4.13,
	68 => 4.13,
	69 => 4.13,
	70 => 4.13,
	71 => 4.13,
	72 => 4.13,
	73 => 4.13,
	74 => 4.13,
	74 => 4.13,
	75 => 4.13,
	76 => 4.13,
	77 => 4.13,
	78 => 4.13,
	79 => 4.13

    ), 
    'file_length' => 10000,
    'tmpdir' => 'tmp/',
    'shipping_fee' => 0.14,
    'log' => 'crontab.log',
    'db' => array(
        'host' => $host, 
        'user' => $dbUser, 
        'password' => $dbPass, 
        'name' => $dbName
    ),
    'ftpSKU' =>array(
    	'host' => $hostSKU,
    	'user' => $userSKU,
    	'pass' => $passSKU
    ),
    'ftpD' =>array(
    	'host' => $hostCAD,
    	'user' => $userCAD,
    	'pass' => $passCAD
    ),
    'ftpMMG' =>array(
    	'host' => $hostCAMMG,
    	'user' => $userCAMMG,
    	'pass' => $passCAMMG
    ),
	'ftpSD' =>array(
    	'host' => $hostSD,
    	'user' => $userSD,
    	'pass' => $passSD
	),
    'accountKeys' =>array(
    	$accountID,
    	$accountID2,
    	$accountID3,
    	$accountID4
    ),
	'getOrderList' => array(
		'developerKey' => $developerKey,
		'pass' => $pass,
		'entrydate' => $entrydate,
		'entryDateSuperD' => $entryDateSuperD,
		'trackingdate' => $trackingdate,
		'modTime' => $modTime,
		'titleTime' => $titleTime,
		'client' => $client,
		'confirm' => $confirm,
		'writeFile' => 'temp/',
		'log' => 'temp/run.log'
	),
	'destination' => array(
				'channeladvisor' => array( 
                'output_prefix' => "CAAddAndUpdate", 
                'output_extension' => '.txt', 
                'path' => '/Inventory', 
                'dest'=>'channeladvisor',
                'headers' => array(
                'instock' => array("Auction Title", "Inventory Number", 
                        "Quantity Update Type", "Quantity", "Starting Bid", "Reserve", 
                        "Weight", "ISBN", "UPC", "EAN", "ASIN", "MPN", "Short Description", 
                        "Description", "Seller Cost", "Buy It Now Price", "Retail Price", 
                        "Second Chance Offer Price", "Picture URLs", "TaxProductCode", 
                        "Supplier Code", "Warehouse Location", "Inventory Subtitle", 
                        "Relationship Name", "Variation Parent SKU", "Ad Template Name", 
                        "Posting Template Name", "Schedule Name", "eBay Category List", 
                        "eBay Store Category Name", "Labels", "DC Code", "Do Not Consolidate", 
                        "ChannelAdvisor Store Title", "ChannelAdvisor Store Description", 
                        "Store Meta Description", "ChannelAdvisor Store Price", 
                        "ChannelAdvisor Store Category ID", "Classification", 
                        "Attribute1Name", "Attribute1Value", "Attribute2Name", 
                        "Attribute2Value", "Attribute3Name", "Attribute3Value", 
                        "Attribute4Name", "Attribute4Value", "Attribute5Name", 
                        "Attribute5Value", "Attribute6Name", "Attribute6Value", 
                        "Attribute7Name", "Attribute7Value", "Attribute8Name", 
                        "Attribute8Value", "Attribute9Name", "Attribute9Value", 
                        "Attribute10Name", "Attribute10Value", "Ship Zone Name", 
                        "Ship Carrier Code", "Ship Class Code", "Ship Rate First Item", 
                        "Ship Handling First Item", "Ship Rate Additional Item", 
                        "Ship Handling Additional Item"), 
               'updated' => array("Auction Title", "Inventory Number", 
                        "Quantity Update Type", "Quantity", "Starting Bid", "Reserve", 
                        "Weight", "ISBN", "UPC", "EAN", "ASIN", "MPN", "Short Description", 
                        "Description", "Seller Cost", "Buy It Now Price", "Retail Price", 
                        "Second Chance Offer Price", "Picture URLs", "TaxProductCode", 
                        "Supplier Code", "Warehouse Location", "Inventory Subtitle", 
                        "Relationship Name", "Variation Parent SKU", "Ad Template Name", 
                        "Posting Template Name", "Schedule Name", "eBay Category List", 
                        "eBay Store Category Name", "Labels", "DC Code", "Do Not Consolidate", 
                        "ChannelAdvisor Store Title", "ChannelAdvisor Store Description", 
                        "Store Meta Description", "ChannelAdvisor Store Price", 
                        "ChannelAdvisor Store Category ID", "Classification", 
                        "Attribute1Name", "Attribute1Value", "Attribute2Name", 
                        "Attribute2Value", "Attribute3Name", "Attribute3Value", 
                        "Attribute4Name", "Attribute4Value", "Attribute5Name", 
                        "Attribute5Value", "Attribute6Name", "Attribute6Value", 
                        "Attribute7Name", "Attribute7Value", "Attribute8Name", 
                        "Attribute8Value", "Attribute9Name", "Attribute9Value", 
                        "Attribute10Name", "Attribute10Value", "Ship Zone Name", 
                        "Ship Carrier Code", "Ship Class Code", "Ship Rate First Item", 
                        "Ship Handling First Item", "Ship Rate Additional Item", 
                        "Ship Handling Additional Item"), 
               'out' => array("Auction Title", "Inventory Number", 
                        "Quantity Update Type", "Quantity", "Starting Bid", "Reserve", 
                        "Weight", "ISBN", "UPC", "EAN", "ASIN", "MPN", "Short Description", 
                        "Description", "Seller Cost", "Buy It Now Price", "Retail Price", 
                        "Second Chance Offer Price", "Picture URLs", "TaxProductCode", 
                        "Supplier Code", "Warehouse Location", "Inventory Subtitle", 
                        "Relationship Name", "Variation Parent SKU", "Ad Template Name", 
                        "Posting Template Name", "Schedule Name", "eBay Category List", 
                        "eBay Store Category Name", "Labels", "DC Code", "Do Not Consolidate", 
                        "ChannelAdvisor Store Title", "ChannelAdvisor Store Description", 
                        "Store Meta Description", "ChannelAdvisor Store Price", 
                        "ChannelAdvisor Store Category ID", "Classification", 
                        "Attribute1Name", "Attribute1Value", "Attribute2Name", 
                        "Attribute2Value", "Attribute3Name", "Attribute3Value", 
                        "Attribute4Name", "Attribute4Value", "Attribute5Name", 
                        "Attribute5Value", "Attribute6Name", "Attribute6Value", 
                        "Attribute7Name", "Attribute7Value", "Attribute8Name", 
                        "Attribute8Value", "Attribute9Name", "Attribute9Value", 
                        "Attribute10Name", "Attribute10Value", "Ship Zone Name", 
                        "Ship Carrier Code", "Ship Class Code", "Ship Rate First Item", 
                        "Ship Handling First Item", "Ship Rate Additional Item", 
                        "Ship Handling Additional Item")
                		)
                	)
                )
	
);
define('DEBUG', TRUE);
require('lib/Database.php');
require('lib/ftp.class.php');



?>