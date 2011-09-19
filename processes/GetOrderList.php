<?php
/*
 *unset and status have been turned off while testing
 */
class GetOrderList implements Process {
		
		private $_acountID;
		private $_account;
		private $_ftp;
		private $_developerKey;
		private $_pass;
		private $_client;
		private $_file;
		private $_file2;
		private $_accountLine;
		private $_headerLine;
		private $_result;
		private $_PO;
		private $_headers;
		private $_testTotal;
		private $_orderNum;
		private $_matches;
		private $_detailMIDs;
		private $_detailIDs;
		private $_DLine;
		private $_po;
		private $_clientOrderIndentifier;
		private $_pageSize;
		private $_string;
		private $_c;
		private $_testCount;
		private $_stringArray;
		
		
		//writeFile
		private $_data;
		private $_writeFile;
		private $_dir;
		private $_log;
		private $_timeStamp;
		
		//time
		private $_entrydate;
		private $_entryDateSuperD;
		private $_trackingdate;
		private $_modTime;
		private $_titleTime;
		
		//data tables and arrays
		private $_userTable = 'ca_user';
		private $_purchaseTable = 'ca_productPurchase';
		private $_userArray;
		private $_purchaseArray;
		
		
		
				public function _construct(){
        			$this->options = $_ENV['CONFIG'];
        			$this->db = new Database($this->options['db']);
					$this->_ftp = new Ftp;
        			$this->_developerKey = $this->options['getOrderList']['developerKey'];
        			$this->_pass = $this->options['getOrderList']['pass'];
        			$this->_client = $this->options['getOrderList']['client'];
        			$this->_entrydate = $this->options['getOrderList']['entrydate'];
        			$this->_entryDateSuperD = $this->options['getOrderList']['entryDateSuperD'];
        			$this->_trackingdate = $this->options['getOrderList']['trackingdate'];
        			$this->_modTime = $this->options['getOrderList']['modTime'];
        			$this->_titleTime = $this->options['getOrderList']['titleTime'];
        			$this->_writeFile = $this->options['getOrderList']['writeFile'];
        			$this->_log = $this->options['getOrderList']['log'];
        			$this->_po = array();
					$this->_c = 0;
					$this->_testCount = 20;
					$this->_stringArray = 0;
//LOG IN					
					
					$this->_dir = "processes/";//directory needed
					$this->_log = $this->_dir.$this->options['log'];
					$Handle = fopen($this->_log, 'a+');
					$this->_timeStamp = date("Y-m-d H:i:s");
					$this->_data = $this->_timeStamp.": GetOrderList started.\n";
					fwrite($Handle, $this->_data); 
					fclose($Handle);			
					
        		}
        		
        		public function newEntry($item,$table,$index){
					$track_list = $this->db->save($item,$table,$index);
				}
				
			private function _callSoap(){
				for($a=0;$a<4;$a++){
					echo $a."\n";
					$this->_accountID = $this->options['accountKeys'][$a];
					$this->_account = $a;
					echo $this->_accountID."\n";
					
					$this->_headers = '
<APICredentials xmlns="http://api.channeladvisor.com/webservices/">
	<DeveloperKey>'.$this->_developerKey.'</DeveloperKey>
	<Password>'.$this->_pass.'</Password>
</APICredentials>';
					
					$newpacket = '
<web:GetOrderList>
	<web:accountID>'.$this->_accountID.'</web:accountID>
<web:orderCriteria>
	<ord:StatusUpdateFilterBeginTimeGMT>2011-01-28T01:11:43</ord:StatusUpdateFilterBeginTimeGMT>
	<ord:DetailLevel>Medium</ord:DetailLevel>
	<ord:OrderStateFilter>Active</ord:OrderStateFilter>
	<ord:ExportState>NotExported</ord:ExportState>
	<ord:PageNumberFilter>1</ord:PageNumberFilter>
	<ord:PageSize>1</ord:PageSize>
</web:orderCriteria>
</web:GetOrderList>';
					
					$this->_result = $this->_client->call('GetOrderList',$newpacket,false,false,$this->_headers);
					if($this->_client->fault){
						echo 'Fault<pre>';
						print_r($this->_result);
						echo '</pre>';
						} else {
							$err = $this->_client->getError();
	
								if($err){
									echo 'Error: '.$err;
								  } else {
	
										echo 'Result<pre>';
										print_r($this->_result);
										echo '</pre>';
								  }
						}

					//
					$this->_testTotal = $this->_result['GetOrderListResult']['ResultData'];
					$this->_testTotal = number_format($this->_testTotal);
					
					if($this->_testTotal>0){
							$this->_orderNum = $this->_result['GetOrderListResult']['ResultData']['OrderResponseItem']['NumberOfMatches'];
							$this->_matches = $this->_result['GetOrderListResult']['ResultData']['OrderResponseItem']['NumberOfMatches'];
							$this->_matches = number_format($this->_matches);
							
					if($this->_matches != 1){ 
						$this->_detailMIDs = array();
						}
					
					$this->_clientOrderIdentifier = $this->_result['GetOrderListResult']['ResultData']['OrderResponseItem']['ClientOrderIdentifier'];
					$this->_file = $this->_clientOrderIdentifier.'_'.$this->_trackingdate.'_'.$this->_titleTime.'.txt';
					echo 'THIS IS THIS FILE: '.$this->_file;
					$this->_accountLine = 'A|ORDERS|2010B|'.$this->_entryDateSuperD.'|MUSICINFERNO|13620||';
					$this->_headerLine =   'H|'.$this->_entryDateSuperD.'|B|'.$this->_clientOrderIdentifier.'|||Music Inferno|5344 E. Greenmeadow Rd.||Long Beach|CA|90808|USA|Music Inferno|5344 E. Greenmeadow Rd.||Long Beach|CA|90808|USA|Will Call|2.99|david.delucca@gmail.com|';
					$this->_data = $this->_accountLine."\r".$this->_headerLine."\r";
					$this->_file2 = $this->_clientOrderIdentifier.'_'.$this->_trackingdate.'_'.$this->_titleTime.'.txt.done';		
					$this->_writeFile1();
					$this->_writeFile2();
					
					//call the complete set of the soap set
					$this->_callSoap2();	
					}
							
							
					}
			
			}
			
			private function _callSoap2(){
				if($this->_orderNum<20){
				$this->_pageSize = $this->_orderNum;
				echo 'THIS IS ORDER NUMBER UNDER 20 : '.$this->_orderNum."\n";
				} else {
				$this->_pageSize = 20;
				echo 'THIS IS ORDER NUMBER OVER 20: '.$this->_orderNum."\n";
				}
		
				$this->_orderNum = number_format($this->_orderNum/20,2, '.', '');
				$this->_orderNum = ceil($this->_orderNum);
				$this->_orderNum = $this->_orderNum+1;
				echo 'THIS IS ORDER NUMBER: '.$this->_orderNum."\n";
				
				for($i = 1;$i<$this->_orderNum;$i++){
				$newpacket = '
<web:GetOrderList>
	<web:accountID>'.$this->_accountID.'</web:accountID>
<web:orderCriteria>
	<ord:StatusUpdateFilterBeginTimeGMT>2011-01-28T01:11:43</ord:StatusUpdateFilterBeginTimeGMT>
	<ord:DetailLevel>High</ord:DetailLevel>
	<ord:OrderStateFilter>Active</ord:OrderStateFilter>
	<ord:ExportState>NotExported</ord:ExportState>
	<ord:PageNumberFilter>'.$i.'</ord:PageNumberFilter>
	<ord:PageSize>'.$this->_pageSize.'</ord:PageSize>
</web:orderCriteria>
</web:GetOrderList>';

				$this->_result = $this->_client->call('GetOrderList',$newpacket,false,false,$this->_headers);
				
				if($this->_client->fault){
						echo 'Fault<pre>';
						print_r($this->_result);
						echo '</pre>';
						} else {
							$err = $this->_client->getError();
	
								if($err){
									echo 'Error: '.$err;
								  } else {
	
										echo 'Result<pre>';
										print_r($this->_result);
										echo '</pre>';
										
										if($this->_matches == 1){
										echo "THIS IS ONE THIS IS MATCHES: ".$this->_matches."\n";
										$this->_oneOrder();
										} else {
										echo "THIS IS MORE: THIS IS MATCHES: ".$this->_matches." THIS IS TESTCOUNT: ".$this->_testCount."\n";
										

										if($this->_matches<$this->_testCount){
										$this->_testCount = $this->_matches;
										}
										$this->_moreOrder();
										}
								  }
						}
				
				//
				}
			$detailing = count($this->_detailMIDs);
			$detailing = number_format($detailing);

			if($detailing>0){
				$Dliners = '';
				for($r=0; $r<$detailing; $r++){
				$d = $r+1;
				$Dliners .= 'D|'.$d.'|'.$this->_detailMIDs[$r]."\r";
				}
				$this->_data = $Dliners;
			$this->_writeFile3();
			}
			
			
			$this->_destination();
			
				
			}
			
			private function _oneOrder(){
			$this->_detailIDs = array();
			
			$PurchaseOrder = $this->_result['GetOrderListResult']['ResultData']['OrderResponseItem']['ClientOrderIdentifier'];
			array_push($this->_po,$PurchaseOrder);
			
			$CAOrderID = $this->_result['GetOrderListResult']['ResultData']['OrderResponseItem']['OrderID'];
			$COI = $this->_result['GetOrderListResult']['ResultData']['OrderResponseItem']['ClientOrderIdentifier'];
			$TotalOrderAmount = $this->_result['GetOrderListResult']['ResultData']['OrderResponseItem']['TotalOrderAmount'];
			$OrderState = $this->_result['GetOrderListResult']['ResultData']['OrderResponseItem']['OrderState'];
			$Email = $this->_result['GetOrderListResult']['ResultData']['OrderResponseItem']['BuyerEmailAddress'];
			$EmailOptIn = $this->_result['GetOrderListResult']['ResultData']['OrderResponseItem']['EmailOptIn'];
			
			$ship = count($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem']['ShippingInfo']);
			echo 'THIS IS SHIPPED: '.$ship."\n";
			if($ship>0){
				foreach($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem']['ShippingInfo'] as $key=>$val){
					$shipping[$key]=$val;
						}
						}
						
			$pay = count($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem']['PaymentInfo']);
			if($pay>0){			
				foreach($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem']['PaymentInfo'] as $key=>$val){
					$payment[$key]=$val;
						}
						}
			
			$bill = count($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem']['BillingInfo']);
			if($bill>0){
				foreach($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem']['BillingInfo'] as $key=>$val){
					$billing[$key]=$val;
						}
						}
			
			$ord = count($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem']['ShoppingCart']['LineItemSKUList']['OrderLineItemItem']);
			if($ord>0){
				foreach($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem']['ShoppingCart']['LineItemSKUList']['OrderLineItemItem']as $key=>$val){
					$order[$key]=$val;
						}
						}
						
			$dCount = count($detailIDs);
			$dCount = number_format($dCount);
			$dCounter = $dCount+1;
			$dCounter = '-'.$dCounter;
			echo "Email this: ".$Email."\n";
			
			/*
			**new Tracking No for _userArray
			*/
			$pipe = '-';
    		$comma = '';
    		$var = str_replace($pipe,$comma,$PurchaseOrder);
    		$number = "000000000000";
    		$var2 = $var.$number;
    		$shipTag = substr($var2,0,15);
    		$zip = str_replace($pipe,$comma,$shipping['PostalCode']);
    		$zipCode = substr($zip,0,5);
    		$trackingNum = $shipTag.$zipCode;
			//end
			
			$this->_userArray = array(
			'userEmail' => $Email,
			'accountID' => $this->_account,
			'poNumber' => $this->_clientOrderIdentifier.$dCounter,
			'cartID' => $PurchaseOrder,
			'trackingNo' => $trackingNum,
			'fname' => $shipping['FirstName'],
			'lname' => $shipping['LastName'],
			'dateEntry' => $this->_entrydate,
			'dateModified' => $this->_entrydate,
			'timeModified' => $this->_modTime,
			'trackingNumber' => '',
			'postDate' => '',
			'transactionDateTime' => '',
			'status' => '',
			'emailOptin' => $EmailOptIn,
			'ship_addressOne' => $shipping['AddressLine1'],
			'ship_addressTwo' => $shipping['AddressLine2'],
			'ship_city' => $shipping['City'],
			'ship_region' => $shipping['Region'],
			'ship_postalcode' => $shipping['PostalCode'],
			'ship_countrycode' => $shipping['CountryCode'],
			'ship_companyname' => $shipping['CompanyName'],
			'ship_jobtitle' => $shipping['JobTitle'],
			'ship_title' => $shipping['Title'],
			'ship_suffix' => $shipping['Suffix'],
			'ship_phonenumberday' => $shipping['PhoneNumberDay'],
			'ship_phonenumberevening' => $shipping['PhoneNumberEvening'],
			'bill_addressOne' => $billing['AddressLine1'],
			'bill_addressTwo' =>$billing['AddressLine2'],
			'bill_city' =>$billing['City'],
			'bill_region' =>$billing['Region'],
			'bill_postalcode' =>$billing['PostalCode'],
			'bill_countrycode' =>$billing['CountryCode'],
			'bill_companyname' =>$billing['CompanyName'],
			'bill_title' =>$billing['Title'],
			'bill_fname' =>$billing['FirstName'],
			'bill_lname' =>$billing['LastName'],
			'bill_suffix' =>$billing['Suffix'],
			'bill_phonenumberday' =>$billing['PhoneNumberDay'],
			'bill_phonenumberevening' =>$billing['PhoneNumberEvening'],
			'CAOrderID' => $CAOrderID
				);
				
				$this->newEntry($this->_userArray,$this->_userTable,'poNumber');
				
				$Detail = count($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem']['ShoppingCart']['LineItemSKUList']['OrderLineItemItem']);
				$Detail = number_format($Detail);
				
				
				
				if(isset($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem']['ShoppingCart']['LineItemSKUList']['OrderLineItemItem'][1])){
				$array = array_values($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem']['ShoppingCart']['LineItemSKUList']['OrderLineItemItem'][1]);
				echo "here is the Array count: ".$array[4];
				} 
			
				
				if($array[4]!=0){
				
								echo "if(array4 != 0 This is ARRAY 4: ".$array[4]."\n";
					for($s=0;$s<$Detail;$s++){
					foreach($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem']['ShoppingCart']['LineItemSKUList']['OrderLineItemItem'][$s] as $key=>$val){
							$order[$s][$key]=$val;
							}
					$unitPrice = $order[$s]['UnitPrice'];
					$unitPrice = number_format($unitPrice, 2, '.', '');	
					$this->_purchaseArray = array(
						'userID' => $Email,
						'poNumber' =>$this->_clientOrderIdentifier.$dCounter,
						'orderID' => $PurchaseOrder,
						'clientOrderIndentifier' => $COI,
						'orderDate' => $this->_entrydate,
						'orderTime' => $this->_modTime,
						'totalOrderAmount' => $TotalOrderAmount,
						'orderState' => $OrderState,
						'paymentType' => $payment['PaymentType'],
						'ccLast4' => $payment['CreditCardLast4'],
						'PayPalID' => $payment['PayPalID'],
						'MerchantReferenceNumber' => $payment['MerchantReferenceNumber'],
						'PaymentTransactionID' => $payment['PaymentTransactionID'],
						'cartID' => $PurchaseOrder,
						'SKU' => $order[$s]['SKU'],
						'unitPrice' => $unitPrice,
						'quantity' => $order[$s]['Quantity'],
						'title' => $order[$s]['Title'],
						'saleSourceId' => $order[$s]['SalesSourceID'],
						'trackingNo' => $PurchaseOrder,
						'CAOrderID' => $CAOrderID
					);
					$this->newEntry($this->_purchaseArray,$this->_purchaseTable, 'poNumber');
					$DlineMore = $this->_clientOrderIdentifier.$dCounter.'|'.$order[$s]['SKU'].'|'.$unitPrice.'|'.$order[$s]['Quantity'].'|';
					array_push($this->_detailIDs,$DlineMore);
				}
				} else {
								echo "This is ARRAY 4: ".$array[4]."\n";
				$unitPrice = $order['UnitPrice'];
				$unitPrice = number_format($unitPrice, 2, '.', '');
				$this->_purchaseArray = array(
						'userID' => $Email,
						'poNumber' =>$this->_clientOrderIdentifier.$dCounter,
						'orderID' => $PurchaseOrder,
						'clientOrderIndentifier' => $COI,
						'orderDate' => $this->_entrydate,
						'orderTime' => $this->_modTime,
						'totalOrderAmount' => $TotalOrderAmount,
						'orderState' => $OrderState,
						'paymentType' => $payment['PaymentType'],
						'ccLast4' => $payment['CreditCardLast4'],
						'PayPalID' => $payment['PayPalID'],
						'MerchantReferenceNumber' => $payment['MerchantReferenceNumber'],
						'PaymentTransactionID' => $payment['PaymentTransactionID'],
						'cartID' => $PurchaseOrder,
						'SKU' => $order['SKU'],
						'unitPrice' => $unitPrice,
						'quantity' => $order['Quantity'],
						'title' => $order['Title'],
						'saleSourceId' => $order['SalesSourceID'],
						'trackingNo' => $PurchaseOrder,
						'CAOrderID' => $CAOrderID
						);
				$this->newEntry($this->_purchaseArray,$this->_purchaseTable,'poNumber');
				$Dline = $this->_clientOrderIdentifier.$dCounter.'|'.$order['SKU'].'|'.$unitPrice.'|'.$order['Quantity'].'|';

				array_push($this->_detailIDs,$Dline);
		

				
				}
				//}
				
				$this->_string = '';
				$count = count($this->_po);

				for($e=0; $e<$count; $e++){
				$this->_string .=	'<web:string>'.$this->_po[$e].'</web:string>';
				}
		
				$DlineCount = count($this->_detailIDs);
								echo "THIS IS DLINE count: ".$DlineCount."\n";
				$Dliners = '';
				for($r=0; $r<$DlineCount; $r++){
				$d = $r+1;
				echo "This is D: ".$d."\n";
				
				$Dliners .= 'D|'.$d.'|'.$this->_detailIDs[$r]."\r";
				echo "THIS IS DLINERS ".$Dliners."\n";
				}
				$this->_data = $Dliners;
				$this->_writeFile3();
		
		//STATUS SUBMIT
					$this->_status();
		//STATUS END
			
			}
			
			private function _moreOrder(){
			echo 'THIS IS C: '.$this->_c."\n";
			
			$this->_c = $this->_c + 1;
				for($e=0; $e<$this->_testCount; $e++){
				echo 'THIS IS E: '.$e."\n";
				$PurchaseOrder = $this->_result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['ClientOrderIdentifier'];
				if(isset($PurchaseOrder)){
				array_push($this->_po,$PurchaseOrder);
				}
				$CAOrderID = $this->_result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['OrderID'];
				$COI = $this->_result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['ClientOrderIdentifier'];
				$TotalOrderAmount = $this->_result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['TotalOrderAmount'];
				$OrderState = $this->_result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['OrderState'];
				$Email = $this->_result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['BuyerEmailAddress'];
				$EmailOptIn = $this->_result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['EmailOptIn'];
				
				$ship = count($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['ShippingInfo']);
				if($ship>0){
				foreach($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['ShippingInfo'] as $key=>$val){
								$shipping[$key]=$val;
								}
								}
								
				$pay = count($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['PaymentInfo']);
				if($pay>0){
				foreach($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['PaymentInfo'] as $key=>$val){
								$payment[$key]=$val;
								}
								}
								
				$bill = count($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['BillingInfo']);
				if($bill>0){
				foreach($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['BillingInfo'] as $key=>$val){
								$billing[$key]=$val;
								}
								}
				$ord = count($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['ShoppingCart']['LineItemSKUList']['OrderLineItemItem']);
				if($ord>0){
				foreach($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['ShoppingCart']['LineItemSKUList']['OrderLineItemItem'] as $key=>$val){
								$order[$key]=$val;
								}
								}
								
				$CartID = $this->_result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['ShoppingCart']['CartID'];
	
				if(isset($Email)){
				
				$dCount = count($this->_detailMIDs);
				$dCount = number_format($dCount);
				$dCounter = $dCount+1;
				$dCounter = '-'.$dCounter;
				
				
			
			/*
			**new Tracking No for _userArray
			*/
			$pipe = '-';
    		$comma = '';
    		$var = str_replace($pipe,$comma,$PurchaseOrder);
    		$number = "000000000000";
    		$var2 = $var.$number;
    		$shipTag = substr($var2,0,15);
    		$zip = str_replace($pipe,$comma,$shipping['PostalCode']);
    		$zipCode = substr($zip,0,5);
    		$trackingNum = $shipTag.$zipCode;
			//end
				
				$this->_userArray = array(
					'userEmail' => $Email,
					'accountID' => $this->_account,
					'poNumber' => $this->_clientOrderIdentifier.$dCounter,
					'trackingNo' => $trackingNum,
					'cartID' => $PurchaseOrder,
					'fname' => $shipping['FirstName'],
					'lname' => $shipping['LastName'],
					'dateEntry' => $this->_entrydate,
					'dateModified' => $this->_entrydate,
					'timeModified' => $this->_modTime,
					'trackingNumber' => '',
					'postDate' => '',
					'transactionDateTime' => '',
					'status' => '',
					'emailOptin' => $EmailOptIn,
					'ship_addressOne' => $shipping['AddressLine1'],
					'ship_addressTwo' => $shipping['AddressLine2'],
					'ship_city' => $shipping['City'],
					'ship_region' => $shipping['Region'],
					'ship_postalcode' => $shipping['PostalCode'],
					'ship_countrycode' => $shipping['CountryCode'],
					'ship_companyname' => $shipping['CompanyName'],
					'ship_jobtitle' => $shipping['JobTitle'],
					'ship_title' => $shipping['Title'],
					'ship_suffix' => $shipping['Suffix'],
					'ship_phonenumberday' => $shipping['PhoneNumberDay'],
					'ship_phonenumberevening' => $shipping['PhoneNumberEvening'],
					'bill_addressOne' => $billing['AddressLine1'],
					'bill_addressTwo' =>$billing['AddressLine2'],
					'bill_city' =>$billing['City'],
					'bill_region' =>$billing['Region'],
					'bill_postalcode' =>$billing['PostalCode'],
					'bill_countrycode' =>$billing['CountryCode'],
					'bill_companyname' =>$billing['CompanyName'],
					'bill_title' =>$billing['Title'],
					'bill_fname' =>$billing['FirstName'],
					'bill_lname' =>$billing['LastName'],
					'bill_suffix' =>$billing['Suffix'],
					'bill_phonenumberday' =>$billing['PhoneNumberDay'],
					'bill_phonenumberevening' =>$billing['PhoneNumberEvening'],
					'CAOrderID' => $CAOrderID
				);
				
				$this->newEntry($this->_userArray,$this->_userTable,'poNumber');
				
				$Detail = count($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['ShoppingCart']['LineItemSKUList']['OrderLineItemItem']);
				$Detail = number_format($Detail);
				
				if(isset($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['ShoppingCart']['LineItemSKUList']['OrderLineItemItem'][1])){
				$array = array_values($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['ShoppingCart']['LineItemSKUList']['OrderLineItemItem'][1]);
				} else {
				$array[4] = 0;
				}				
				if($array[4]!=0){
					for($s=0;$s<$Detail;$s++){
					
					foreach($this->_result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['ShoppingCart']['LineItemSKUList']['OrderLineItemItem'][$s] as $key=>$val){
					$order[$s][$key]=$val;
							}
					$dCount = count($this->_detailMIDs);
					$dCount = number_format($dCount);
					$dCounter = $dCount+1;
					$dCounter = '-'.$dCounter;
					
					$unitPrice = $order[$s]['UnitPrice'];
					$unitPrice = number_format($unitPrice, 2, '.', '');
					
					$this->_purchaseArray = array(
						'userID' => $Email,
						'poNumber' =>$this->_clientOrderIdentifier.$dCounter,
						'orderID' => $PurchaseOrder,
						'clientOrderIndentifier' => $COI,
						'orderDate' => $this->_entrydate,
						'orderTime' => $this->_modTime,
						'totalOrderAmount' => $TotalOrderAmount,
						'orderState' => $OrderState,
						'paymentType' => $payment['PaymentType'],
						'ccLast4' => $payment['CreditCardLast4'],
						'PayPalID' => $payment['PayPalID'],
						'MerchantReferenceNumber' => $payment['MerchantReferenceNumber'],
						'PaymentTransactionID' => $payment['PaymentTransactionID'],
						'cartID' => $PurchaseOrder,
						'SKU' => $order[$s]['SKU'],
						'unitPrice' => $unitPrice,
						'quantity' => $order[$s]['Quantity'],
						'title' => $order[$s]['Title'],
						'saleSourceId' => $order[$s]['SalesSourceID'],
						'trackingNo' => $PurchaseOrder,
						'CAOrderID' => $CAOrderID
					);
					
					$this->newEntry($this->_purchaseArray,$this->_purchaseTable,'poNumber');
					$this->_Dline = $this->_clientOrderIdentifier.$dCounter.'|'.$order[$s]['SKU'].'|'.$unitPrice.'|'.$order[$s]['Quantity'].'|';
					
					array_push($this->_detailMIDs,$this->_Dline);
					//
					}
				//
				} else {
				
					$unitPrice = $order['UnitPrice'];
					$unitPrice = number_format($unitPrice, 2, '.', '');
				
					$dCount = count($this->_detailMIDs);
					$dCount = number_format($dCount);
					$dCounter = $dCount+1;
					$dCounter = '-'.$dCounter;
					
					$this->_purchaseArray = array(
						'userID' => $Email,
						'poNumber' =>$this->_clientOrderIdentifier.$dCounter,
						'orderID' => $PurchaseOrder,
						'clientOrderIndentifier' => $COI,
						'orderDate' => $this->_entrydate,
						'orderTime' => $this->_modTime,
						'totalOrderAmount' => $TotalOrderAmount,
						'orderState' => $OrderState,
						'paymentType' => $payment['PaymentType'],
						'ccLast4' => $payment['CreditCardLast4'],
						'PayPalID' => $payment['PayPalID'],
						'MerchantReferenceNumber' => $payment['MerchantReferenceNumber'],
						'PaymentTransactionID' => $payment['PaymentTransactionID'],
						'cartID' => $PurchaseOrder,
						'SKU' => $order['SKU'],
						'unitPrice' => $unitPrice,
						'quantity' => $order['Quantity'],
						'title' => $order['Title'],
						'saleSourceId' => $order['SalesSourceID'],
						'trackingNo' => $PurchaseOrder,
						'CAOrderID' => $CAOrderID
						);
					
				$this->newEntry($this->_purchaseArray,$this->_purchaseTable,'poNumber');
				$this->_Dline = $this->_clientOrderIdentifier.$dCounter.'|'.$order['SKU'].'|'.$unitPrice.'|'.$order['Quantity'].'|';
				
					
				array_push($this->_detailMIDs,$this->_Dline);
				//
				}
				//
				}
				//
				} 
			
				$this->_string = '';
				$count = count($this->_po);
				for($e=0; $e<$count; $e++){
				$this->_string .=	'<web:string>'.$this->_po[$e].'</web:string>';
				}	
			//STATUS SUBMIT
			$this->_status();
			//STATUS END
			}

			private function _status(){
			
			$countString = count($this->_string);
			
			$this->_stringArray .= $this->_stringArray+$countString;
			
			echo 'This is countString: '.$countString."\n";
				$newpacket2 = '
<web:SetOrdersExportStatus>
	<web:accountID>'.$this->_accountID.'</web:accountID>
<web:clientOrderIdentifiers>
'.$this->_string.'
</web:clientOrderIdentifiers>
	<web:markAsExported>true</web:markAsExported>
</web:SetOrdersExportStatus>';
			$result = $this->_client->call('SetOrdersExportStatus',$newpacket2,false,false,$this->_headers);
			if($this->_client->fault){
					echo 'Fault<pre>';
					print_r($result);
					echo '</pre>';
			} else {
			$err = $this->_client->getError();
	
			if($err){
				echo 'Error: '.$err;
			} else {
	
				echo 'Result Export<pre>';
				print_r($result);
				echo '</pre>';
					}
				}
			}

			private function _destination(){

				$destination_file = 'Inbound/'.$this->_file;
				$source_file = $this->_writeFile.$this->_file;
				$destination_file2 = 'Inbound/'.$this->_file2;
				$source_file2 = $this->_writeFile.$this->_file2;

				try{
				    $this->_ftp->connect($this->options['ftpSD']['host']);
					$this->_ftp->login($this->options['ftpSD']['user'], $this->options['ftpSD']['pass']);
					$this->_ftp->put($destination_file, $source_file, FTP_BINARY);
					$this->_ftp->put($destination_file2, $source_file2, FTP_BINARY);
					} catch (FtpException $e) {
    					echo 'Error: ', $e->getMessage();
						}

				unset($detailMIDs);
				//destroy $file;
//				unlink($source_file);
//				unlink($source_file2);

				echo 'ALL DONE! GOOD JOB!';
				
				
//LOG out					
					
					$Handle = fopen($this->_log, 'a+');
					$this->_timeStamp = date("Y-m-d H:i:s");
					$this->_data = $this->_timeStamp.": GetOrderList Finished.\n";
					fwrite($Handle, $this->_data); 
					fclose($Handle);

			}
			
			private function _writeFile1(){
				$Handle = fopen($this->_writeFile.$this->_file, 'w');
				fwrite($Handle, $this->_data); 
				fclose($Handle);
				$Handle = fopen($this->_log,'a+');
				fwrite($Handle, $this->_file);
				fclose($Handle);
			}
			
			
			private function _writeFile3(){
				$Handle = fopen($this->_writeFile.$this->_file, 'a+');
				fwrite($Handle, $this->_data); 
				fclose($Handle);
			}
			
			private function _writeFile2(){
				$Handle = fopen($this->_writeFile.$this->_file2,'w');
				fclose($Handle);
			}
			
			public function run(){
				$this->_callSoap();
				if($this->_stringArray == 0){
								echo 'ALL DONE! NO ORDERS!';
				
				
//LOG out					
					
					$Handle = fopen($this->_log, 'a+');
					$this->_timeStamp = date("Y-m-d H:i:s");
					$this->_data = $this->_timeStamp.": GetOrderList no orders Finished.\n";
					fwrite($Handle, $this->_data); 
					fclose($Handle);
				}	
			}
		
}

?>