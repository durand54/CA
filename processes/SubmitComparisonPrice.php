<?php

class SubmitComparisonPrice implements Process {

			private $_skuArray;
			private $_productArray;
			private $_productsArray;
			private $_quantityArray;
			private $_missingArray;
			private $_albumUpdateArray;
			private $_albumInstockArray;
			private $_albumZeroArray;
			private $_noTitleArray;
			private $_albumsTable = "albums";
			private $_channelTable = "channel2";
			private $_productsTable = "products";
			private $_skuCounter;
			private $_noTitleCounter;
			private $_productCounter;
			private $_productsCounter;
			private $_quantityCounter;
			private $_missingCounter;
			private $_albumUpdateCounter;
			private $_albumInstockCounter;
			private $_albumZeroCounter;
			private $_writeFile;
			private $_dir;
			private $_log;
			private $_timeStamp;
			private $_data;
			private $_sku;
			private $_quantity;
			private $_productID;
			private $_price;
			private $_cost;
			private $_destination;
			private $_filename;
			private $_dest;
			private $_printZeroCount;
			private $_printInstockCount;
			private $_printUpdateCount;
			private $_state;
			private $_titleArray;
			private $_outHeader;
			private $_instockHeader;
			private $_updateHeader;
			private $_outImploded;
			private $_instockImploded;
			private $_updateImploded;
			private $_name;
			private $_vType;
			private $_vTitle;
			private $_vArtist;
			private $_vWeight;
			private $_vCost;
			private $_rTitle;
			private $_rType;
			private $_rPrice;
			private $_rWeight;
			private $_rate;
			private $_sCost;
			private $_sAmount;
			private $_sWCMAmount;
			private $_sWWMAmount;
			private $_wcmPrice;
			private $_wwmPrice;
			private $_tabsOne;
			private $_tabsTwo;
			private $_tabsThree;
			private $_tabsFour;
			private $_pricing;

			
	public function _construct(){
        $this->options = $_ENV['CONFIG'];
        $this->db = new Database($this->options['db']);
        $this->_destination = $this->options['destination'];
        $this->_skuArray = array();
        $this->_productArray = array();
        $this->_productsArray = array();
        $this->_quantityArray = array();
        $this->_missingArray = array();
        $this->_albumUpdateArray = array();
        $this->_albumInstockArray = array();
        $this->_albumZeroArray = array();
        $this->_noTitleArray = array();
		$this->_outHeader = array();
		$this->_instockHeader = array();
		$this->_updateHeader = array();
		$this->_pricing = array();
		$this->_tabsOne = ' 	 	 	 	 	 	 	 	 ';
		$this->_tabsTwo = ' 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 ';
		$this->_tabsThree = ' ';
		$this->_tabsFour = ' 	 	 	 	 	 	 ';
		
    	$this->_outImploded = implode("\t", $this->_destination['channeladvisor']['headers']['out']);
    	echo $this->_outImploded;
		$this->_instockImploded = implode("\t", $this->_destination['channeladvisor']['headers']['instock']);
    	$this->_updateImploded = implode("\t", $this->_destination['channeladvisor']['headers']['updated']);
			
        $this->_state = array('instock','update','out');
        
        $this->_dir = "processes/";
		$this->_log = $this->_dir.$this->options['log'];
		$Handle = fopen($this->_log, 'a+');
		$this->_timeStamp = date("Y-m-d H:i:s");
		$this->_data = $this->_timeStamp.": SubmitComparison started.\n";
		fwrite($Handle, $this->_data); 
		fclose($Handle);
        }
        
    private function _getAll(){
    	$channel = $this->db->get_all($this->_channelTable);
    	if($channel){           
   		 	while (list($key, $value) = each($channel)) {
   		 	$sku = $value['sku'];
   		 	$sku = ltrim($sku,' '); 
   		 	$sku = "$sku";        
   		 	$pushing = array($sku,$value['quantity'],$value['title']);
   		 	if($value['title'] == "NEW  -  ()"){
   		 	echo $sku."\n".$value['title']."\n THIS IS A NON TITLE";
   		 	array_push($this->_noTitleArray,$pushing);
   		 	}
   		 	array_push($this->_skuArray,$pushing);
			}
			$this->_skuCounter = count($this->_skuArray);
			echo $this->_skuCounter."\n";
			$this->_noTitleCounter = count($this->_noTitleArray);
			echo "This is noTitle counter $this->_noTitleCounter\n";

   		 }
   		 $this->_compare();
    }
    
    private function _compare(){
    	for($t = 0; $t<$this->_noTitleCounter; $t++){
    		$item = $this->_noTitleArray[$t][0];
    		$noTitle_list = $this->db->get("SELECT * FROM products WHERE product_id = '$item'");
    		if($noTitle_list){
    		$item = '';
    		while(list($key,$value) = each($noTitle_list)){
    		$this->_productID = $value['product_id'];
   		 	$this->_sku = $value['sku'];
   		 	$this->_quantity = $value['quantity'];
   		 	$this->_price = $value['price'];
   		 	$this->_cost = $value['cost'];
   		 	$push = array($this->_productID,$this->_quantity,$this->_price,$this->_cost,$this->_sku);
   			array_push($this->_quantityArray,$push);
   		 	echo "PUSHED INTO QUANTITY ARRAY\n";
   			$this->_sku = '';
   		 	
    		}
    		}
    	}
    	$c = count($this->_quantityArray);
    	echo "THIS IS THE QUANTITY ARRAY OF NO TITLES: $c\n\n\n";
    	sleep(1);
    	for($e = 0; $e<$this->_skuCounter; $e++){
    	      
    		$item = $this->_skuArray[$e][0];
    		$zero = $this->_skuArray[$e][0];
    		echo $item."\n";
    		$quantity = $this->_skuArray[$e][1];
    		$channelTitle = $this->_skuArray[$e][2];
    		$table = $this->_productsTable;
    		$productID = 'productID';
    	$sku_list = $this->db->get("SELECT *  FROM products WHERE product_id = '$item'"); 
		if($sku_list){ 
		$item = '';
   		 	while (list($key, $value) = each($sku_list)) {  
   		 	
   		 	$this->_productID = $value['product_id'];
   		 	$this->_sku = $value['sku'];
   		 	$this->_quantity = $value['quantity'];
   		 	$this->_price = $value['price'];
   		 	$this->_cost = $value['cost'];
   		 	
   		 	array_push($this->_productsArray,$this->_productID);
   		 	
   		 	echo "PUSHED INTO PRODUCTS ARRAY\n";
			}
   		if($this->_skuArray[$e][1] != $this->_quantity){
   			$push = array($this->_productID,$this->_quantity,$this->_price,$this->_cost,$this->_sku);
   			array_push($this->_quantityArray,$push);
   		 	echo "PUSHED INTO QUANTITY ARRAY\n";
   			$this->_sku = '';
   		}
   		if($this->_skuArray[$e][1] == $this->_quantity){
   			$push = array($this->_productID,$this->_quantity,$this->_price,$this->_cost,$this->_sku);
   			array_push($this->_productArray,$push);
   		 	echo "\nPUSHED INTO PRODUCT ARRAY\n\n";
   			$this->_sku = '';   			
   		}
   		}
   		if($item != ''){
   				$productID = $item;
 				$length = strlen($item);
 				$zero = substr("$item", 0, -1);
   			$zero = "$zero";
   			$pushing = array($zero,$channelTitle,$productID);
   			array_push($this->_missingArray,$pushing);
   		 	echo "PUSHED INTO MISSING ARRAY\n";
   		}
   		
    	}
    	
    	$this->_productsCounter = count($this->_productsArray);
    	$this->_quantityCounter = count($this->_quantityArray);
    	$this->_productCounter = count($this->_productArray);
    	$this->_missingCounter = count($this->_missingArray);
    	echo "THIS IS ALL PRODUCTS THAT MATCH CHANNEL: ".$this->_productsCounter."\n";
    	echo "THIS IS QUANTITIES THAT DON'T MATCH: ".$this->_quantityCounter."\n";
    	echo "THIS IS QUANTITIES THAT DO MATCH: ".$this->_productCounter."\n";
    	echo "THIS IS CHANNEL MISSING FROM PRODUCTS: ".$this->_missingCounter."\n";

    	$this->_writeOut();
    
    }
    
    private function _writeOut(){
    	for($k=0; $k<$this->_quantityCounter; $k++){
    			$sku = $this->_quantityArray[$k][4];
    			$quantity = $this->_quantityArray[$k][1];
    			$cost = $this->_quantityArray[$k][3];
    			$price = $this->_quantityArray[$k][2];
    			$productID = $this->_quantityArray[$k][0];
    			echo "THIS IS SEARCH SKU: ".$sku."\n";
    			$album_list = $this->db->get("SELECT *  FROM albums WHERE sku = '$sku'");
    			
    			if($album_list){           
			$sku = '';
   		 	while (list($key, $value) = each($album_list)) {
   		 	
   		 	
   		 	$this->_vType = $value['cfgltr'];
			$this->_vTitle = $value['title'];
			$this->_vArtist = $value['artist'];
			$this->_vWeight = $value['weight'];
			$this->_vCost = $cost;
			$this->_rType = $this->_format_channeladvisor_item_type();
			$this->_rTitle = sprintf("NEW %s (%s)", $this->_format_channeladvisor_item_title(), $this->_rType);
			
			$this->_format_item_price();
			$this->_rPrice = $this->_pricing[0][0];
			$this->_wcmPrice = $this->_pricing[0][1];
			$this->_wwmPrice = $this->_pricing[0][2];
			unset($this->_pricing);
			
			
			$pushing = array($this->_rTitle,$productID,"UNSHIPPED",$quantity,$this->_rPrice,$this->_tabsOne,$this->_vCost,$this->_rPrice,$this->_tabsTwo,$this->_wcmPrice,$this->_tabsThree,$this->_wwmPrice,$this->_tabsFour);
			
   		 	array_push($this->_albumUpdateArray,$pushing);
   		 	$this->_vType = '';
			$this->_vTitle = '';
			$this->_vArtist = '';
			$this->_vWeight = '';
			$this->_vCost = '';
			$this->_rType = '';
			$this->_rTitle = '';
			$this->_rPrice = '';
			$this->_wcmPrice = '';
			$this->_wwmPrice = '';
    			}
    	}
    			
    			
    	}
    	$this->_albumUpdateCounter = count($this->_albumUpdateArray);
    	echo 'THIS IS ALBUM COUNTER: '.$this->_albumUpdateCounter."\n";
    	
    	unset($this->_quantityArray);
    	for($k=0; $k<$this->_productCounter; $k++){
    			$sku = $this->_productArray[$k][4];
    			$quantity = $this->_productArray[$k][1];
    			$cost = $this->_productArray[$k][3];
    			$price = $this->_productArray[$k][2];
    			$productID = $this->_productArray[$k][0];
    			echo "THIS IS SEARCH SKU: ".$sku."\n";
    			$album_list = $this->db->get("SELECT *  FROM albums WHERE sku = '$sku'");
    			
    			if($album_list){           
			$sku = '';
   		 	while (list($key, $value) = each($album_list)) {
   		 	if($quantity == 0){
   		 	
   		 	
   		 	$this->_vType = $value['cfgltr'];
			$this->_vTitle = $value['title'];
			$this->_vArtist = $value['artist'];
			$this->_vWeight = $value['weight'];
			$this->_vCost = $cost;
			$this->_rType = $this->_format_channeladvisor_item_type();
			$this->_rTitle = sprintf("NEW %s (%s)", $this->_format_channeladvisor_item_title(), $this->_rType);
			echo $this->_rTitle;
			
			$this->_format_item_price();
			$this->_rPrice = $this->_pricing[0][0];
			$this->_wcmPrice = $this->_pricing[0][1];
			$this->_wwmPrice = $this->_pricing[0][2];
			unset($this->_pricing);
			
			$pushingZero = array($this->_rTitle,$productID,"UNSHIPPED",$quantity,$this->_rPrice,$this->_tabsOne,$this->_vCost,$this->_rPrice,$this->_tabsTwo,$this->_wcmPrice,$this->_tabsThree,$this->_wwmPrice,$this->_tabsFour);

			
   		 	array_push($this->_albumZeroArray,$pushingZero);
   		 	
   		 	$this->_vType = '';
			$this->_vTitle = '';
			$this->_vArtist = '';
			$this->_vWeight = '';
			$this->_vCost = '';
			$this->_rType = '';
			$this->_rTitle = '';
			$this->_rPrice = '';
			$this->_wcmPrice = '';
			$this->_wwmPrice = '';
   		 	}else{
   		 	
   		 	
   		 	$this->_vType = $value['cfgltr'];
			$this->_vTitle = $value['title'];
			$this->_vArtist = $value['artist'];
			$this->_vWeight = $value['weight'];
			$this->_vCost = $cost;
			$this->_rType = $this->_format_channeladvisor_item_type();
			$this->_rTitle = sprintf("NEW %s (%s)", $this->_format_channeladvisor_item_title(), $this->_rType);
			echo $this->_rTitle;
			
			$this->_format_item_price();
			$this->_rPrice = $this->_pricing[0][0];
			$this->_wcmPrice = $this->_pricing[0][1];
			$this->_wwmPrice = $this->_pricing[0][2];
			unset($this->_pricing);
			
			$pushing = array($this->_rTitle,$productID,"UNSHIPPED",$quantity,$this->_rPrice,$this->_tabsOne,$this->_vCost,$this->_rPrice,$this->_tabsTwo,$this->_wcmPrice,$this->_tabsThree,$this->_wwmPrice,$this->_tabsFour);

			
   		 	array_push($this->_albumInstockArray,$pushing);
   		 	
   		 	$this->_vType = '';
			$this->_vTitle = '';
			$this->_vArtist = '';
			$this->_vWeight = '';
			$this->_vCost = '';
			$this->_rType = '';
			$this->_rTitle = '';
			$this->_rPrice = '';
			$this->_wcmPrice = '';
			$this->_wwmPrice = '';
   		 	}
    			}
    	}
    			
    			
    	}
    	$this->_albumInstockCounter = count($this->_albumInstockArray);
    	echo 'THIS IS INSTOCK ALBUM COUNTER: '.$this->_albumInstockCounter."\n";
    	$this->_albumZeroCounter = count($this->_albumZeroArray);
    	echo 'THIS IS ZERO ALBUM COUNTER: '.$this->_albumZeroCounter."\n";
		unset($this->_productArray);
		
    	for($k=0; $k<$this->_missingCounter; $k++){
    	echo "\n";
    	print_r($this->_missingArray[$k]);
    	echo "\n\n";
    			$sku = $this->_missingArray[$k][0];
    			$title = $this->_missingArray[$k][1];
    			$productID = $this->_missingArray[$k][2];
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
   		 	
   		 	
   		 	$this->_vType = $value['cfgltr'];
			$this->_vTitle = $value['title'];
			$this->_vArtist = $value['artist'];
			$this->_vWeight = $value['weight'];
			$this->_vCost = $cost;
			$this->_rType = $this->_format_channeladvisor_item_type();
			$this->_rTitle = sprintf("NEW %s (%s)", $this->_format_channeladvisor_item_title(), $this->_rType);
			echo $this->_rTitle;
			$this->_format_item_price();
			$this->_rPrice = $this->_pricing[0][0];
			$this->_wcmPrice = $this->_pricing[0][1];
			$this->_wwmPrice = $this->_pricing[0][2];
			unset($this->_pricing);
			
			$pushingZero = array($this->_rTitle,$productID,"UNSHIPPED",$quantity,$this->_rPrice,$this->_tabsOne,$this->_vCost,$this->_rPrice,$this->_tabsTwo,$this->_wcmPrice,$this->_tabsThree,$this->_wwmPrice,$this->_tabsFour);

   		 	array_push($this->_albumZeroArray,$pushingZero);
   		 	
   		 	$this->_vType = '';
			$this->_vTitle = '';
			$this->_vArtist = '';
			$this->_vWeight = '';
			$this->_vCost = '';
			$this->_rType = '';
			$this->_rTitle = '';
			$this->_rPrice = '';
			$this->_wcmPrice = '';
			$this->_wwmPrice = '';
   		 	}else{

   		 	
   		 	$this->_vType = $value['cfgltr'];
			$this->_vTitle = $value['title'];
			$this->_vArtist = $value['artist'];
			$this->_vWeight = $value['weight'];
			$this->_vCost = $cost;
			$this->_rType = $this->_format_channeladvisor_item_type();
			$this->_rTitle = sprintf("NEW %s (%s)", $this->_format_channeladvisor_item_title(), $this->_rType);
			echo $this->_rTitle;
			
			$this->_format_item_price();
			$this->_rPrice = $this->_pricing[0][0];
			$this->_wcmPrice = $this->_pricing[0][1];
			$this->_wwmPrice = $this->_pricing[0][2];
			unset($this->_pricing);
			
			
			$pushing = array($this->_rTitle,$productID,"UNSHIPPED",$quantity,$this->_rPrice,$this->_tabsOne,$this->_vCost,$this->_rPrice,$this->_tabsTwo,$this->_wcmPrice,$this->_tabsThree,$this->_wwmPrice,$this->_tabsFour);

			
   		 	array_push($this->_albumInstockArray,$pushing);
   		 	
   		 	$this->_vType = '';
			$this->_vTitle = '';
			$this->_vArtist = '';
			$this->_vWeight = '';
			$this->_vCost = '';
			$this->_rType = '';
			$this->_rTitle = '';
			$this->_rPrice = '';
			$this->_wcmPrice = '';
			$this->_wwmPrice = '';
   		 	}
    			}
    	}
    			
    			
    	}
    	$this->_albumInstockCounter = count($this->_albumInstockArray);
    	echo 'THIS IS ALBUM COUNTER: '.$this->_albumInstockCounter."\n";
    	$this->_albumZeroCounter = count($this->_albumZeroArray);
    	echo 'THIS IS ALBUM COUNTER: '.$this->_albumZeroCounter."\n";
		
		unset($this->_missingArray);

    }
    /*
    ** begin from importStock.php
    */
    private function _format_channeladvisor_item_title(){
        $artist = ucwords(strtolower($this->_vArtist));
        $title = ucwords(strtolower($this->_vTitle));
        
        // Show only title on DVDs.
        if($this->_format_channeladvisor_item_type() == 'DVD')
            return $title;
        
        if(strpos($artist, ',') !== FALSE){
            $name = explode(',', $artist);
            $artist = sprintf("%s %s", ucwords($name[1]), ucwords($name[0]));
        }
        
        return sprintf("%s - %s", $artist, $title);
    }
    
    private function _format_channeladvisor_item_type(){
        switch(strtoupper($this->_vType)){
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
    
    private function _format_item_WCM(){
    
    switch($this->_dest){
    	case 'channeladvisor':
    		//$this->_sCost = $this->_format_item_shipping_cost();
    		$this->_sAmount = number_format(((1.10 * $this->_vCost) + .80 - 2.98 + $this->_sCost) / .85, 2);
    		break;
    	}
    	
    	return $this->_sAmount;
    }
    
    private function _format_item_WWM(){
    	switch($this->_dest){
    		case 'channeladvisor':          
    		$this->_sAmount =  number_format((((1.10* $this->_vCost)-2.99 +.99+1.77)/.85), 2);
    		break;
    		} 
    		
     		return $this->_sAmount;
     }
     
     
     private function _format_item_weight(){
		
        if(!$this->_vWeight){
        echo "no weight";
        }
        $this->_vWeight = ceil($this->_vWeight)? $this->_vWeight : 4;
        
        
        return $this->_vWeight;
    }
    
    private function _format_item_shipping_cost(){
        $rates = $this->options['shipping_rates'];
        $this->_rWeight = $this->_format_item_weight();
        if(array_key_exists($this->_rWeight, $rates)){
            $rate = $rates[$this->_rWeight];
        } else {
            $rate = array_shift($rates);
        }
        $this->_rate = number_format($rate + $this->options['shipping_fee'], 2);
        
        return $this->_rate;
    }
    
    private function _format_item_price(){
        	
        $this->_pricing = array();
        
        switch($this->_dest){
        case 'channeladvisor':
        		
        		$this->_sCost = $this->_format_item_shipping_cost();
        		
                /**
                *ebay pricing changed to company 01082011
                *
                $this->_sAmount = number_format((1.00 * 1.1 - (2.99 - $this->_sCost - $this->_vCost - 0.05 - 0.30 - (0.029 * 2.99))) / ((1 - 0.15 * .80) - 0.029), 2);
                */
                
                $this->_sAmount = number_format(((1.10 * $this->_vCost) + .80 - 2.98 + $this->_sCost) / .85, 2);
                
                $this->_sWCMAmount = number_format(((1.10 * $this->_vCost) + .80 - 2.98 + $this->_sCost) / .85, 2);
               	
               	$this->_sWWMAmount =  number_format((((1.10* $this->_vCost)-2.99 +.99+ $this->_sCost)/.85), 2);
               	
               	$pushing = array($this->_sAmount,$this->_sWCMAmount,$this->_sWWMAmount);
               	
               	array_push($this->_pricing,$pushing);
               	
                break;
                }
                $this->_sCost = '';
                $this->_sAmount = '';
                $this->_sWCMAmount = '';
                $this->_sWWMAmount = '';

                }
    /*
    ** end from importStock.php
    */
    

    /*
    **begin writing file
    */
    
    private function _generate_output_filename($count){
    
    $this->_titleArray = array();
    echo $name." ".$count."\n";
    for($e=0;$e<$count;$e++){
    	$parts = array(
            $this->_destination['channeladvisor']['output_prefix'], 
            $this->_dest, 
            $this->_name, 
            $e, 
            date('Y-m-d-h-i-s'));
        
        $namer = sprintf("%s.txt",implode('_', array_filter($parts, 'strlen')));
         echo $namer."\n";   
        array_push($this->_titleArray,$namer);
        
            }
    }
    
    private function _open_output_file($state){
    		switch($state){
    		case 'update':
    		$count = $this->_printUpdateCount;
    		$array = $this->_albumUpdateArray;
    		$headers = $this->_updateImploded;
    		$this->_name = 'updated';
    		break;
    		case 'out':
    		$count = $this->_printZeroCount;
    		$array = $this->_albumZeroArray;
    		$headers = $this->_outImploded;
    		$this->_name = 'out';
    		break;
    		case 'instock':
    		$count = $this->_printInstockCount;
    		$array = $this->_albumInstockArray;
    		$headers = $this->_instockImploded;
    		$this->_name = 'updated';
    		break;
    		}
    		
    	    $countArray = count($array);
			$this->_generate_output_filename($count);
			for($f=0;$f<count($this->_titleArray);$f++){
    	    $handle = fopen(WORKING_DIR . $this->options['tmpdir'] . $this->_titleArray[$f], 'w+');
        	fwrite($handle, $headers. "\n");
        	fclose($handle);
        	}
        	for($k=0;$k<$count;$k++){
        	
    	    $handle = fopen(WORKING_DIR . $this->options['tmpdir'] . $this->_titleArray[$k], 'a+');
    	    
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
        	unset($this->_titleArray);
        	
    }
    
	
	private function _export(){
	
	
		echo "this is Zero Counter: $this->_albumZeroCounter\n";
		echo "this is Update Counter: $this->_albumUpdateCounter\n";
		echo "this is Instock Counter: $this->_albumInstockCounter\n";
        $file_lines = $updated_lines = 0;
//        exit();

        
        $this->_printInstockCount = number_format($this->_albumInstockCounter/10000);
        $this->_printInstockCount = ceil($this->_printInstockCount)+1;
		echo "this is print instock counter: $this->_printInstockCount\n";
        $this->_open_output_file("instock");

        $this->_printUpdateCount = number_format($this->_albumUpdateCounter/10000);
        $this->_printUpdateCount = ceil($this->_printUpdateCount)+1;
		echo "this is print update counter: $this->_printUpdateCount\n";
        $this->_open_output_file("update");
        
        
        $this->_printZeroCount = number_format($this->_albumZeroCounter/10000);
        $this->_printZeroCount = ceil($this->_printZeroCount)+1;
		echo "this is this is print zero counter: $this->_printZeroCount\n";
        $this->_open_output_file("out");

		
        
        
        $Handle = fopen($this->_log, 'a+');
		$this->_timeStamp = date("Y-m-d H:i:s");
		$this->_data = $this->_timeStamp.": SubmitComparison FINISHED.\n";
		fwrite($Handle, $this->_data); 
		fclose($Handle);
        exit();
		
	}
	    
    public function run(){
   
    
		$this->_dest = $this->_destination['channeladvisor']['dest'];
        print "\nExporting items for $this->_dest.\n";

    	$this->_getAll();
    	
    	$this->_export();
    }

}

?>