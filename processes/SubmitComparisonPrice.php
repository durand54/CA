<?php

class SubmitComparisonPrice implements Process {

			private $__skuArray;
			private $__productArray;
			private $__productsArray;
			private $__quantityArray;
			private $__missingArray;
			private $__albumUpdateArray;
			private $__albumInstockArray;
			private $__albumZeroArray;
			private $__noTitleArray;
			private $__albumsTable = "albums";
			private $__channelTable = "channel2";
			private $__productsTable = "products";
			private $__skuCounter;
			private $__noTitleCounter;
			private $__productCounter;
			private $__productsCounter;
			private $__quantityCounter;
			private $__missingCounter;
			private $__albumUpdateCounter;
			private $__albumInstockCounter;
			private $__albumZeroCounter;
			private $__writeFile;
			private $__dir;
			private $__log;
			private $__timeStamp;
			private $__data;
			private $__sku;
			private $__quantity;
			private $__productID;
			private $__price;
			private $__cost;
			private $__destination;
			private $__filename;
			private $__dest;
			private $__printZeroCount;
			private $__printInstockCount;
			private $__printUpdateCount;
			private $__state;
			private $__titleArray;
			private $__outHeader;
			private $__instockHeader;
			private $__updateHeader;
			private $__outImploded;
			private $__instockImploded;
			private $__updateImploded;
			private $__name;
			private $__vType;
			private $__vTitle;
			private $__vArtist;
			private $__vWeight;
			private $__vCost;
			private $__rTitle;
			private $__rType;
			private $__rPrice;
			private $__rWeight;
			private $__rate;
			private $__sCost;
			private $__sAmount;
			private $__sWCMAmount;
			private $__sWWMAmount;
			private $__wcmPrice;
			private $__wwmPrice;
			private $__tabsOne;
			private $__tabsTwo;
			private $__tabsThree;
			private $__tabsFour;
			private $__pricing;

			
	public function __construct(){
        $this->options = $_ENV['CONFIG'];
        $this->db = new Database($this->options['db']);
        $this->__destination = $this->options['destination'];
        $this->__skuArray = array();
        $this->__productArray = array();
        $this->__productsArray = array();
        $this->__quantityArray = array();
        $this->__missingArray = array();
        $this->__albumUpdateArray = array();
        $this->__albumInstockArray = array();
        $this->__albumZeroArray = array();
        $this->__noTitleArray = array();
		$this->__outHeader = array();
		$this->__instockHeader = array();
		$this->__updateHeader = array();
		$this->__pricing = array();
		$this->__tabsOne = ' 	 	 	 	 	 	 	 	 ';
		$this->__tabsTwo = ' 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 ';
		$this->__tabsThree = ' ';
		$this->__tabsFour = ' 	 	 	 	 	 	 ';
		
    	$this->__outImploded = implode("\t", $this->__destination['channeladvisor']['headers']['out']);
    	echo $this->__outImploded;
		$this->__instockImploded = implode("\t", $this->__destination['channeladvisor']['headers']['instock']);
    	$this->__updateImploded = implode("\t", $this->__destination['channeladvisor']['headers']['updated']);
			
        $this->__state = array('instock','update','out');
        
        $this->__dir = "processes/";
		$this->__log = $this->__dir.$this->options['log'];
		$Handle = fopen($this->__log, 'a+');
		$this->__timeStamp = date("Y-m-d H:i:s");
		$this->__data = $this->__timeStamp.": SubmitComparison started.\n";
		fwrite($Handle, $this->__data); 
		fclose($Handle);
        }
        
    private function __getAll(){
    	$channel = $this->db->get_all($this->__channelTable);
    	if($channel){           
   		 	while (list($key, $value) = each($channel)) {
   		 	$sku = $value['sku'];
   		 	$sku = ltrim($sku,' '); 
   		 	$sku = "$sku";        
   		 	$pushing = array($sku,$value['quantity'],$value['title']);
   		 	if($value['title'] == "NEW  -  ()"){
   		 	echo $sku."\n".$value['title']."\n THIS IS A NON TITLE";
   		 	array_push($this->__noTitleArray,$pushing);
   		 	}
   		 	array_push($this->__skuArray,$pushing);
			}
			$this->__skuCounter = count($this->__skuArray);
			echo $this->__skuCounter."\n";
			$this->__noTitleCounter = count($this->__noTitleArray);
			echo "This is noTitle counter $this->__noTitleCounter\n";

   		 }
   		 $this->__compare();
    }
    
    private function __compare(){
    	for($t = 0; $t<$this->__noTitleCounter; $t++){
    		$item = $this->__noTitleArray[$t][0];
    		$noTitle_list = $this->db->get("SELECT * FROM products WHERE product_id = '$item'");
    		if($noTitle_list){
    		$item = '';
    		while(list($key,$value) = each($noTitle_list)){
    		$this->__productID = $value['product_id'];
   		 	$this->__sku = $value['sku'];
   		 	$this->__quantity = $value['quantity'];
   		 	$this->__price = $value['price'];
   		 	$this->__cost = $value['cost'];
   		 	$push = array($this->__productID,$this->__quantity,$this->__price,$this->__cost,$this->__sku);
   			array_push($this->__quantityArray,$push);
   		 	echo "PUSHED INTO QUANTITY ARRAY\n";
   			$this->__sku = '';
   		 	
    		}
    		}
    	}
    	$c = count($this->__quantityArray);
    	echo "THIS IS THE QUANTITY ARRAY OF NO TITLES: $c\n\n\n";
    	sleep(1);
    	for($e = 0; $e<$this->__skuCounter; $e++){
    	      
    		$item = $this->__skuArray[$e][0];
    		$zero = $this->__skuArray[$e][0];
    		echo $item."\n";
    		$quantity = $this->__skuArray[$e][1];
    		$channelTitle = $this->__skuArray[$e][2];
    		$table = $this->__productsTable;
    		$productID = 'productID';
    	$sku_list = $this->db->get("SELECT *  FROM products WHERE product_id = '$item'"); 
		if($sku_list){ 
		$item = '';
   		 	while (list($key, $value) = each($sku_list)) {  
   		 	
   		 	$this->__productID = $value['product_id'];
   		 	$this->__sku = $value['sku'];
   		 	$this->__quantity = $value['quantity'];
   		 	$this->__price = $value['price'];
   		 	$this->__cost = $value['cost'];
   		 	
   		 	array_push($this->__productsArray,$this->__productID);
   		 	
   		 	echo "PUSHED INTO PRODUCTS ARRAY\n";
			}
   		if($this->__skuArray[$e][1] != $this->__quantity){
   			$push = array($this->__productID,$this->__quantity,$this->__price,$this->__cost,$this->__sku);
   			array_push($this->__quantityArray,$push);
   		 	echo "PUSHED INTO QUANTITY ARRAY\n";
   			$this->__sku = '';
   		}
   		if($this->__skuArray[$e][1] == $this->__quantity){
   			$push = array($this->__productID,$this->__quantity,$this->__price,$this->__cost,$this->__sku);
   			array_push($this->__productArray,$push);
   		 	echo "\nPUSHED INTO PRODUCT ARRAY\n\n";
   			$this->__sku = '';   			
   		}
   		}
   		if($item != ''){
   				$productID = $item;
 				$length = strlen($item);
 				$zero = substr("$item", 0, -1);
   			$zero = "$zero";
   			$pushing = array($zero,$channelTitle,$productID);
   			array_push($this->__missingArray,$pushing);
   		 	echo "PUSHED INTO MISSING ARRAY\n";
   		}
   		
    	}
    	
    	$this->__productsCounter = count($this->__productsArray);
    	$this->__quantityCounter = count($this->__quantityArray);
    	$this->__productCounter = count($this->__productArray);
    	$this->__missingCounter = count($this->__missingArray);
    	echo "THIS IS ALL PRODUCTS THAT MATCH CHANNEL: ".$this->__productsCounter."\n";
    	echo "THIS IS QUANTITIES THAT DON'T MATCH: ".$this->__quantityCounter."\n";
    	echo "THIS IS QUANTITIES THAT DO MATCH: ".$this->__productCounter."\n";
    	echo "THIS IS CHANNEL MISSING FROM PRODUCTS: ".$this->__missingCounter."\n";

    	$this->__writeOut();
    
    }
    
    private function __writeOut(){
    	for($k=0; $k<$this->__quantityCounter; $k++){
    			$sku = $this->__quantityArray[$k][4];
    			$quantity = $this->__quantityArray[$k][1];
    			$cost = $this->__quantityArray[$k][3];
    			$price = $this->__quantityArray[$k][2];
    			$productID = $this->__quantityArray[$k][0];
    			echo "THIS IS SEARCH SKU: ".$sku."\n";
    			$album_list = $this->db->get("SELECT *  FROM albums WHERE sku = '$sku'");
    			
    			if($album_list){           
			$sku = '';
   		 	while (list($key, $value) = each($album_list)) {
   		 	
   		 	
   		 	$this->__vType = $value['cfgltr'];
			$this->__vTitle = $value['title'];
			$this->__vArtist = $value['artist'];
			$this->__vWeight = $value['weight'];
			$this->__vCost = $cost;
			$this->__rType = $this->__format_channeladvisor_item_type();
			$this->__rTitle = sprintf("NEW %s (%s)", $this->__format_channeladvisor_item_title(), $this->__rType);
			
			$this->__format_item_price();
			$this->__rPrice = $this->__pricing[0][0];
			$this->__wcmPrice = $this->__pricing[0][1];
			$this->__wwmPrice = $this->__pricing[0][2];
			unset($this->__pricing);
			
			
			$pushing = array($this->__rTitle,$productID,"UNSHIPPED",$quantity,$this->__rPrice,$this->__tabsOne,$this->__vCost,$this->__rPrice,$this->__tabsTwo,$this->__wcmPrice,$this->__tabsThree,$this->__wwmPrice,$this->__tabsFour);
			
   		 	array_push($this->__albumUpdateArray,$pushing);
   		 	$this->__vType = '';
			$this->__vTitle = '';
			$this->__vArtist = '';
			$this->__vWeight = '';
			$this->__vCost = '';
			$this->__rType = '';
			$this->__rTitle = '';
			$this->__rPrice = '';
			$this->__wcmPrice = '';
			$this->__wwmPrice = '';
    			}
    	}
    			
    			
    	}
    	$this->__albumUpdateCounter = count($this->__albumUpdateArray);
    	echo 'THIS IS ALBUM COUNTER: '.$this->__albumUpdateCounter."\n";
    	
    	unset($this->__quantityArray);
    	for($k=0; $k<$this->__productCounter; $k++){
    			$sku = $this->__productArray[$k][4];
    			$quantity = $this->__productArray[$k][1];
    			$cost = $this->__productArray[$k][3];
    			$price = $this->__productArray[$k][2];
    			$productID = $this->__productArray[$k][0];
    			echo "THIS IS SEARCH SKU: ".$sku."\n";
    			$album_list = $this->db->get("SELECT *  FROM albums WHERE sku = '$sku'");
    			
    			if($album_list){           
			$sku = '';
   		 	while (list($key, $value) = each($album_list)) {
   		 	if($quantity == 0){
   		 	
   		 	
   		 	$this->__vType = $value['cfgltr'];
			$this->__vTitle = $value['title'];
			$this->__vArtist = $value['artist'];
			$this->__vWeight = $value['weight'];
			$this->__vCost = $cost;
			$this->__rType = $this->__format_channeladvisor_item_type();
			$this->__rTitle = sprintf("NEW %s (%s)", $this->__format_channeladvisor_item_title(), $this->__rType);
			echo $this->__rTitle;
			
			$this->__format_item_price();
			$this->__rPrice = $this->__pricing[0][0];
			$this->__wcmPrice = $this->__pricing[0][1];
			$this->__wwmPrice = $this->__pricing[0][2];
			unset($this->__pricing);
			
			$pushingZero = array($this->__rTitle,$productID,"UNSHIPPED",$quantity,$this->__rPrice,$this->__tabsOne,$this->__vCost,$this->__rPrice,$this->__tabsTwo,$this->__wcmPrice,$this->__tabsThree,$this->__wwmPrice,$this->__tabsFour);

			
   		 	array_push($this->__albumZeroArray,$pushingZero);
   		 	
   		 	$this->__vType = '';
			$this->__vTitle = '';
			$this->__vArtist = '';
			$this->__vWeight = '';
			$this->__vCost = '';
			$this->__rType = '';
			$this->__rTitle = '';
			$this->__rPrice = '';
			$this->__wcmPrice = '';
			$this->__wwmPrice = '';
   		 	}else{
   		 	
   		 	
   		 	$this->__vType = $value['cfgltr'];
			$this->__vTitle = $value['title'];
			$this->__vArtist = $value['artist'];
			$this->__vWeight = $value['weight'];
			$this->__vCost = $cost;
			$this->__rType = $this->__format_channeladvisor_item_type();
			$this->__rTitle = sprintf("NEW %s (%s)", $this->__format_channeladvisor_item_title(), $this->__rType);
			echo $this->__rTitle;
			
			$this->__format_item_price();
			$this->__rPrice = $this->__pricing[0][0];
			$this->__wcmPrice = $this->__pricing[0][1];
			$this->__wwmPrice = $this->__pricing[0][2];
			unset($this->__pricing);
			
			$pushing = array($this->__rTitle,$productID,"UNSHIPPED",$quantity,$this->__rPrice,$this->__tabsOne,$this->__vCost,$this->__rPrice,$this->__tabsTwo,$this->__wcmPrice,$this->__tabsThree,$this->__wwmPrice,$this->__tabsFour);

			
   		 	array_push($this->__albumInstockArray,$pushing);
   		 	
   		 	$this->__vType = '';
			$this->__vTitle = '';
			$this->__vArtist = '';
			$this->__vWeight = '';
			$this->__vCost = '';
			$this->__rType = '';
			$this->__rTitle = '';
			$this->__rPrice = '';
			$this->__wcmPrice = '';
			$this->__wwmPrice = '';
   		 	}
    			}
    	}
    			
    			
    	}
    	$this->__albumInstockCounter = count($this->__albumInstockArray);
    	echo 'THIS IS INSTOCK ALBUM COUNTER: '.$this->__albumInstockCounter."\n";
    	$this->__albumZeroCounter = count($this->__albumZeroArray);
    	echo 'THIS IS ZERO ALBUM COUNTER: '.$this->__albumZeroCounter."\n";
		unset($this->__productArray);
		
    	for($k=0; $k<$this->__missingCounter; $k++){
    	echo "\n";
    	print_r($this->__missingArray[$k]);
    	echo "\n\n";
    			$sku = $this->__missingArray[$k][0];
    			$title = $this->__missingArray[$k][1];
    			$productID = $this->__missingArray[$k][2];
    			echo "THIS IS ZERO SEARCH SKU: $sku \n";
    			$album_list = $this->db->get("SELECT *  FROM albums WHERE sku = '$sku'");
    			
    			if($album_list){           
			$sku = '';
   		 	while (list($key, $value) = each($album_list)) {
			if($value['title'] != ''){
			} else {
			echo "this is the channel title: ".$title."\n";
			}
			$quantity = 0;
   		 	if($quantity == 0){
   		 	
   		 	
   		 	$this->__vType = $value['cfgltr'];
			$this->__vTitle = $value['title'];
			$this->__vArtist = $value['artist'];
			$this->__vWeight = $value['weight'];
			$this->__vCost = $cost;
			$this->__rType = $this->__format_channeladvisor_item_type();
			$this->__rTitle = sprintf("NEW %s (%s)", $this->__format_channeladvisor_item_title(), $this->__rType);
			echo $this->__rTitle;
			$this->__format_item_price();
			$this->__rPrice = $this->__pricing[0][0];
			$this->__wcmPrice = $this->__pricing[0][1];
			$this->__wwmPrice = $this->__pricing[0][2];
			unset($this->__pricing);
			
			$pushingZero = array($this->__rTitle,$productID,"UNSHIPPED",$quantity,$this->__rPrice,$this->__tabsOne,$this->__vCost,$this->__rPrice,$this->__tabsTwo,$this->__wcmPrice,$this->__tabsThree,$this->__wwmPrice,$this->__tabsFour);

   		 	array_push($this->__albumZeroArray,$pushingZero);
   		 	
   		 	$this->__vType = '';
			$this->__vTitle = '';
			$this->__vArtist = '';
			$this->__vWeight = '';
			$this->__vCost = '';
			$this->__rType = '';
			$this->__rTitle = '';
			$this->__rPrice = '';
			$this->__wcmPrice = '';
			$this->__wwmPrice = '';
   		 	}else{

   		 	
   		 	$this->__vType = $value['cfgltr'];
			$this->__vTitle = $value['title'];
			$this->__vArtist = $value['artist'];
			$this->__vWeight = $value['weight'];
			$this->__vCost = $cost;
			$this->__rType = $this->__format_channeladvisor_item_type();
			$this->__rTitle = sprintf("NEW %s (%s)", $this->__format_channeladvisor_item_title(), $this->__rType);
			echo $this->__rTitle;
			
			$this->__format_item_price();
			$this->__rPrice = $this->__pricing[0][0];
			$this->__wcmPrice = $this->__pricing[0][1];
			$this->__wwmPrice = $this->__pricing[0][2];
			unset($this->__pricing);
			
			
			$pushing = array($this->__rTitle,$productID,"UNSHIPPED",$quantity,$this->__rPrice,$this->__tabsOne,$this->__vCost,$this->__rPrice,$this->__tabsTwo,$this->__wcmPrice,$this->__tabsThree,$this->__wwmPrice,$this->__tabsFour);

			
   		 	array_push($this->__albumInstockArray,$pushing);
   		 	
   		 	$this->__vType = '';
			$this->__vTitle = '';
			$this->__vArtist = '';
			$this->__vWeight = '';
			$this->__vCost = '';
			$this->__rType = '';
			$this->__rTitle = '';
			$this->__rPrice = '';
			$this->__wcmPrice = '';
			$this->__wwmPrice = '';
   		 	}
    			}
    	}
    			
    			
    	}
    	$this->__albumInstockCounter = count($this->__albumInstockArray);
    	echo 'THIS IS ALBUM COUNTER: '.$this->__albumInstockCounter."\n";
    	$this->__albumZeroCounter = count($this->__albumZeroArray);
    	echo 'THIS IS ALBUM COUNTER: '.$this->__albumZeroCounter."\n";
		
		unset($this->__missingArray);

    }
    /*
    ** begin from importStock.php
    */
    private function __format_channeladvisor_item_title(){
        $artist = ucwords(strtolower($this->__vArtist));
        $title = ucwords(strtolower($this->__vTitle));
        
        // Show only title on DVDs.
        if($this->__format_channeladvisor_item_type() == 'DVD')
            return $title;
        
        if(strpos($artist, ',') !== FALSE){
            $name = explode(',', $artist);
            $artist = sprintf("%s %s", ucwords($name[1]), ucwords($name[0]));
        }
        
        return sprintf("%s - %s", $artist, $title);
    }
    
    private function __format_channeladvisor_item_type(){
        switch(strtoupper($this->__vType)){
            case 'G':
            case 'H':
            	return 'DVD';
            
            case 'O':
                return 'Blu-ray';
            
            case 'U':
                return 'UMD';
            
            case 'X':
            case 'D':
            case 'I':
                return 'CD';
            
            case 'R':       
            case 'S':
                return 'Vinyl';
            
            default:
                return '';
        }
    }
    
    private function __format_item_WCM(){
    
    switch($this->__dest){
    	case 'channeladvisor':
    		//$this->__sCost = $this->__format_item_shipping_cost();
    		$this->__sAmount = number_format(((1.10 * $this->__vCost) + .80 - 2.98 + $this->__sCost) / .85, 2);
    		break;
    	}
    	
    	return $this->__sAmount;
    }
    
    private function __format_item_WWM(){
    	switch($this->__dest){
    		case 'channeladvisor':          
    		$this->__sAmount =  number_format((((1.10* $this->__vCost)-2.99 +.99+1.77)/.85), 2);
    		break;
    		} 
    		
     		return $this->__sAmount;
     }
     
     
     private function __format_item_weight(){
		
        if(!$this->__vWeight){
        echo "no weight";
        }
        $this->__vWeight = ceil($this->__vWeight)? $this->__vWeight : 4;
        
        
        return $this->__vWeight;
    }
    
    private function __format_item_shipping_cost(){
        $rates = $this->options['shipping_rates'];
        $this->__rWeight = $this->__format_item_weight();
        if(array_key_exists($this->__rWeight, $rates)){
            $rate = $rates[$this->__rWeight];
        } else {
            $rate = array_shift($rates);
        }
        $this->__rate = number_format($rate + $this->options['shipping_fee'], 2);
        
        return $this->__rate;
    }
    
    private function __format_item_price(){
        	
        $this->__pricing = array();
        
        switch($this->__dest){
        case 'channeladvisor':
        		
        		$this->__sCost = $this->__format_item_shipping_cost();
        		
                /**
                *ebay pricing changed to white crow media 01082011
                *
                $this->__sAmount = number_format((1.00 * 1.1 - (2.99 - $this->__sCost - $this->__vCost - 0.05 - 0.30 - (0.029 * 2.99))) / ((1 - 0.15 * .80) - 0.029), 2);
                */
                
                $this->__sAmount = number_format(((1.10 * $this->__vCost) + .80 - 2.98 + $this->__sCost) / .85, 2);
                
                $this->__sWCMAmount = number_format(((1.10 * $this->__vCost) + .80 - 2.98 + $this->__sCost) / .85, 2);
               	
               	$this->__sWWMAmount =  number_format((((1.10* $this->__vCost)-2.99 +.99+ $this->__sCost)/.85), 2);
               	
               	$pushing = array($this->__sAmount,$this->__sWCMAmount,$this->__sWWMAmount);
               	
               	array_push($this->__pricing,$pushing);
               	
                break;
                }
                $this->__sCost = '';
                $this->__sAmount = '';
                $this->__sWCMAmount = '';
                $this->__sWWMAmount = '';

                }
    /*
    ** end from importStock.php
    */
    

    /*
    **begin writing file
    */
    
    private function __generate_output_filename($count){
    
    $this->__titleArray = array();
    echo $name." ".$count."\n";
    for($e=0;$e<$count;$e++){
    	$parts = array(
            $this->__destination['channeladvisor']['output_prefix'], 
            $this->__dest, 
            $this->__name, 
            $e, 
            date('Y-m-d-h-i-s'));
        
        $namer = sprintf("%s.txt",implode('_', array_filter($parts, 'strlen')));
         echo $namer."\n";   
        array_push($this->__titleArray,$namer);
        
            }
    }
    
    private function __open_output_file($state){
    		switch($state){
    		case 'update':
    		$count = $this->__printUpdateCount;
    		$array = $this->__albumUpdateArray;
    		$headers = $this->__updateImploded;
    		$this->__name = 'updated';
    		break;
    		case 'out':
    		$count = $this->__printZeroCount;
    		$array = $this->__albumZeroArray;
    		$headers = $this->__outImploded;
    		$this->__name = 'out';
    		break;
    		case 'instock':
    		$count = $this->__printInstockCount;
    		$array = $this->__albumInstockArray;
    		$headers = $this->__instockImploded;
    		$this->__name = 'updated';
    		break;
    		}
    		
    	    $countArray = count($array);
			$this->__generate_output_filename($count);
			for($f=0;$f<count($this->__titleArray);$f++){
    	    $handle = fopen(WORKING_DIR . $this->options['tmpdir'] . $this->__titleArray[$f], 'w+');
        	fwrite($handle, $headers. "\n");
        	fclose($handle);
        	}
        	for($k=0;$k<$count;$k++){
        	
    	    $handle = fopen(WORKING_DIR . $this->options['tmpdir'] . $this->__titleArray[$k], 'a+');
    	    
    	    if($countArray>10000){
    	    $arrayCount = 10000;
    	    } else {
    	    $arrayCount = $countArray;
    	    }
    	    
    	    for($j=0;$j<$arrayCount;$j++){
    	    $line = implode("\t", $array[$j]);
    	    $line = $line."\n";
    	    echo $line;
    	    fwrite($handle, $line);
    	    $write = array_shift($array);
    	    echo "THIS IS LINE: ".$j."\n";
    	    echo 'THIS IS ARRAY COUNT: '.count($array)."\n";
    	    
    	    $countArray = count($array);
    	    
    	    }
    	    
        	}
        	fclose($handle);
        	unset($this->__titleArray);
        	
    }
    
	
	private function __export(){
	
	
		echo "this is Zero Counter: $this->__albumZeroCounter\n";
		echo "this is Update Counter: $this->__albumUpdateCounter\n";
		echo "this is Instock Counter: $this->__albumInstockCounter\n";
        $file_lines = $updated_lines = 0;
//        exit();

        
        $this->__printInstockCount = number_format($this->__albumInstockCounter/10000);
        $this->__printInstockCount = ceil($this->__printInstockCount)+1;
		echo "this is print instock counter: $this->__printInstockCount\n";
        $this->__open_output_file("instock");

        $this->__printUpdateCount = number_format($this->__albumUpdateCounter/10000);
        $this->__printUpdateCount = ceil($this->__printUpdateCount)+1;
		echo "this is print update counter: $this->__printUpdateCount\n";
        $this->__open_output_file("update");
        
        
        $this->__printZeroCount = number_format($this->__albumZeroCounter/10000);
        $this->__printZeroCount = ceil($this->__printZeroCount)+1;
		echo "this is this is print zero counter: $this->__printZeroCount\n";
        $this->__open_output_file("out");

		
        
        
        $Handle = fopen($this->__log, 'a+');
		$this->__timeStamp = date("Y-m-d H:i:s");
		$this->__data = $this->__timeStamp.": SubmitComparison FINISHED.\n";
		fwrite($Handle, $this->__data); 
		fclose($Handle);
        exit();
		
	}
	    
    public function run(){
   
    
		$this->__dest = $this->__destination['channeladvisor']['dest'];
        print "\nExporting items for $this->__dest.\n";

    	$this->__getAll();
    	
    	$this->__export();
    }

}

?>