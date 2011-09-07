<?php
/*
 *unset and status have been turned off while testing
 */
class GetOrderList implements Process {
		
		private $__acountID;
		private $__account;
		private $__ftp;
		private $__developerKey;
		private $__pass;
		private $__client;
		private $__file;
		private $__file2;
		private $__accountLine;
		private $__headerLine;
		private $__result;
		private $__PO;
		private $__headers;
		private $__testTotal;
		private $__orderNum;
		private $__matches;
		private $__detailMIDs;
		private $__detailIDs;
		private $__DLine;
		private $__po;
		private $__clientOrderIndentifier;
		private $__pageSize;
		private $__string;
		private $__c;
		private $__testCount;
		private $__stringArray;
		
		
		//writeFile
		private $__data;
		private $__writeFile;
		private $__dir;
		private $__log;
		private $__timeStamp;
		
		//time
		private $__entrydate;
		private $__entryDateSuperD;
		private $__trackingdate;
		private $__modTime;
		private $__titleTime;
		
		//data tables and arrays
		private $__userTable = 'ca_user';
		private $__purchaseTable = 'ca_productPurchase';
		private $__userArray;
		private $__purchaseArray;
		
		
		
				public function __construct(){
        			$this->options = $_ENV['CONFIG'];
        			$this->db = new Database($this->options['db']);
					$this->__ftp = new Ftp;
        			$this->__developerKey = $this->options['getOrderList']['developerKey'];
        			$this->__pass = $this->options['getOrderList']['pass'];
        			$this->__client = $this->options['getOrderList']['client'];
        			$this->__entrydate = $this->options['getOrderList']['entrydate'];
        			$this->__entryDateSuperD = $this->options['getOrderList']['entryDateSuperD'];
        			$this->__trackingdate = $this->options['getOrderList']['trackingdate'];
        			$this->__modTime = $this->options['getOrderList']['modTime'];
        			$this->__titleTime = $this->options['getOrderList']['titleTime'];
        			$this->__writeFile = $this->options['getOrderList']['writeFile'];
        			$this->__log = $this->options['getOrderList']['log'];
        			$this->__po = array();
					$this->__c = 0;
					$this->__testCount = 20;
					$this->__stringArray = 0;
//LOG IN					
					
					$this->__dir = "processes/";//directory needed
					$this->__log = $this->__dir.$this->options['log'];
					$Handle = fopen($this->__log, 'a+');
					$this->__timeStamp = date("Y-m-d H:i:s");
					$this->__data = $this->__timeStamp.": GetOrderList started.\n";
					fwrite($Handle, $this->__data); 
					fclose($Handle);			
					
        		}
        		
        		public function newEntry($item,$table,$index){
					$track_list = $this->db->save($item,$table,$index);
				}
				
			private function __callSoap(){
				for($a=0;$a<4;$a++){
					echo $a."\n";
					$this->__accountID = $this->options['accountKeys'][$a];
					$this->__account = $a;
					echo $this->__accountID."\n";
					
					$this->__headers = '
<APICredentials xmlns="http://api.channeladvisor.com/webservices/">
	<DeveloperKey>'.$this->__developerKey.'</DeveloperKey>
	<Password>'.$this->__pass.'</Password>
</APICredentials>';
					
					$newpacket = '
<web:GetOrderList>
	<web:accountID>'.$this->__accountID.'</web:accountID>
<web:orderCriteria>
	<ord:StatusUpdateFilterBeginTimeGMT>2011-01-28T01:11:43</ord:StatusUpdateFilterBeginTimeGMT>
	<ord:DetailLevel>Medium</ord:DetailLevel>
	<ord:OrderStateFilter>Active</ord:OrderStateFilter>
	<ord:ExportState>NotExported</ord:ExportState>
	<ord:PageNumberFilter>1</ord:PageNumberFilter>
	<ord:PageSize>1</ord:PageSize>
</web:orderCriteria>
</web:GetOrderList>';
					
					$this->__result = $this->__client->call('GetOrderList',$newpacket,false,false,$this->__headers);
					if($this->__client->fault){
						echo 'Fault<pre>';
						print_r($this->__result);
						echo '</pre>';
						} else {
							$err = $this->__client->getError();
	
								if($err){
									echo 'Error: '.$err;
								  } else {
	
										echo 'Result<pre>';
										print_r($this->__result);
										echo '</pre>';
								  }
						}

					//
					$this->__testTotal = $this->__result['GetOrderListResult']['ResultData'];
					$this->__testTotal = number_format($this->__testTotal);
					
					if($this->__testTotal>0){
							$this->__orderNum = $this->__result['GetOrderListResult']['ResultData']['OrderResponseItem']['NumberOfMatches'];
							$this->__matches = $this->__result['GetOrderListResult']['ResultData']['OrderResponseItem']['NumberOfMatches'];
							$this->__matches = number_format($this->__matches);
							
					if($this->__matches != 1){ 
						$this->__detailMIDs = array();
						}
					
					$this->__clientOrderIdentifier = $this->__result['GetOrderListResult']['ResultData']['OrderResponseItem']['ClientOrderIdentifier'];
					$this->__file = $this->__clientOrderIdentifier.'_'.$this->__trackingdate.'_'.$this->__titleTime.'.txt';
					echo 'THIS IS THIS FILE: '.$this->__file;
					$this->__accountLine = 'A|ORDERS|2010B|'.$this->__entryDateSuperD.'|MUSICINFERNO|13620||';
					$this->__headerLine =   'H|'.$this->__entryDateSuperD.'|B|'.$this->__clientOrderIdentifier.'|||Music Inferno|5344 E. Greenmeadow Rd.||Long Beach|CA|90808|USA|Music Inferno|5344 E. Greenmeadow Rd.||Long Beach|CA|90808|USA|Will Call|2.99|david.delucca@gmail.com|';
					$this->__data = $this->__accountLine."\r".$this->__headerLine."\r";
					$this->__file2 = $this->__clientOrderIdentifier.'_'.$this->__trackingdate.'_'.$this->__titleTime.'.txt.done';		
					$this->__writeFile1();
					$this->__writeFile2();
					
					//call the complete set of the soap set
					$this->__callSoap2();	
					}
							
							
					}
			
			}
			
			private function __callSoap2(){
				if($this->__orderNum<20){
				$this->__pageSize = $this->__orderNum;
				echo 'THIS IS ORDER NUMBER UNDER 20 : '.$this->__orderNum."\n";
				} else {
				$this->__pageSize = 20;
				echo 'THIS IS ORDER NUMBER OVER 20: '.$this->__orderNum."\n";
				}
		
				$this->__orderNum = number_format($this->__orderNum/20,2, '.', '');
				$this->__orderNum = ceil($this->__orderNum);
				$this->__orderNum = $this->__orderNum+1;
				echo 'THIS IS ORDER NUMBER: '.$this->__orderNum."\n";
				
				for($i = 1;$i<$this->__orderNum;$i++){
				$newpacket = '
<web:GetOrderList>
	<web:accountID>'.$this->__accountID.'</web:accountID>
<web:orderCriteria>
	<ord:StatusUpdateFilterBeginTimeGMT>2011-01-28T01:11:43</ord:StatusUpdateFilterBeginTimeGMT>
	<ord:DetailLevel>High</ord:DetailLevel>
	<ord:OrderStateFilter>Active</ord:OrderStateFilter>
	<ord:ExportState>NotExported</ord:ExportState>
	<ord:PageNumberFilter>'.$i.'</ord:PageNumberFilter>
	<ord:PageSize>'.$this->__pageSize.'</ord:PageSize>
</web:orderCriteria>
</web:GetOrderList>';

				$this->__result = $this->__client->call('GetOrderList',$newpacket,false,false,$this->__headers);
				
				if($this->__client->fault){
						echo 'Fault<pre>';
						print_r($this->__result);
						echo '</pre>';
						} else {
							$err = $this->__client->getError();
	
								if($err){
									echo 'Error: '.$err;
								  } else {
	
										echo 'Result<pre>';
										print_r($this->__result);
										echo '</pre>';
										
										if($this->__matches == 1){
										echo "THIS IS ONE THIS IS MATCHES: ".$this->__matches."\n";
										$this->__oneOrder();
										} else {
										echo "THIS IS MORE: THIS IS MATCHES: ".$this->__matches." THIS IS TESTCOUNT: ".$this->__testCount."\n";
										

										if($this->__matches<$this->__testCount){
										$this->__testCount = $this->__matches;
										}
										$this->__moreOrder();
										}
								  }
						}
				
				//
				}
			$detailing = count($this->__detailMIDs);
			$detailing = number_format($detailing);

			if($detailing>0){
				$Dliners = '';
				for($r=0; $r<$detailing; $r++){
				$d = $r+1;
				$Dliners .= 'D|'.$d.'|'.$this->__detailMIDs[$r]."\r";
				}
				$this->__data = $Dliners;
			$this->__writeFile3();
			}
			
			
			$this->__destination();
			
				
			}
			
			private function __oneOrder(){
			$this->__detailIDs = array();
			
			$PurchaseOrder = $this->__result['GetOrderListResult']['ResultData']['OrderResponseItem']['ClientOrderIdentifier'];
			array_push($this->__po,$PurchaseOrder);
			
			$CAOrderID = $this->__result['GetOrderListResult']['ResultData']['OrderResponseItem']['OrderID'];
			$COI = $this->__result['GetOrderListResult']['ResultData']['OrderResponseItem']['ClientOrderIdentifier'];
			$TotalOrderAmount = $this->__result['GetOrderListResult']['ResultData']['OrderResponseItem']['TotalOrderAmount'];
			$OrderState = $this->__result['GetOrderListResult']['ResultData']['OrderResponseItem']['OrderState'];
			$Email = $this->__result['GetOrderListResult']['ResultData']['OrderResponseItem']['BuyerEmailAddress'];
			$EmailOptIn = $this->__result['GetOrderListResult']['ResultData']['OrderResponseItem']['EmailOptIn'];
			
			$ship = count($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem']['ShippingInfo']);
			echo 'THIS IS SHIPPED: '.$ship."\n";
			if($ship>0){
				foreach($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem']['ShippingInfo'] as $key=>$val){
					$shipping[$key]=$val;
						}
						}
						
			$pay = count($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem']['PaymentInfo']);
			if($pay>0){			
				foreach($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem']['PaymentInfo'] as $key=>$val){
					$payment[$key]=$val;
						}
						}
			
			$bill = count($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem']['BillingInfo']);
			if($bill>0){
				foreach($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem']['BillingInfo'] as $key=>$val){
					$billing[$key]=$val;
						}
						}
			
			$ord = count($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem']['ShoppingCart']['LineItemSKUList']['OrderLineItemItem']);
			if($ord>0){
				foreach($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem']['ShoppingCart']['LineItemSKUList']['OrderLineItemItem']as $key=>$val){
					$order[$key]=$val;
						}
						}
						
			$dCount = count($detailIDs);
			$dCount = number_format($dCount);
			$dCounter = $dCount+1;
			$dCounter = '-'.$dCounter;
			echo "Email this: ".$Email."\n";
			
			/*
			**new Tracking No for __userArray
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
			
			$this->__userArray = array(
			'userEmail' => $Email,
			'accountID' => $this->__account,
			'poNumber' => $this->__clientOrderIdentifier.$dCounter,
			'cartID' => $PurchaseOrder,
			'trackingNo' => $trackingNum,
			'fname' => $shipping['FirstName'],
			'lname' => $shipping['LastName'],
			'dateEntry' => $this->__entrydate,
			'dateModified' => $this->__entrydate,
			'timeModified' => $this->__modTime,
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
				
				$this->newEntry($this->__userArray,$this->__userTable,'poNumber');
				
				$Detail = count($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem']['ShoppingCart']['LineItemSKUList']['OrderLineItemItem']);
				$Detail = number_format($Detail);
				
				
				
				if(isset($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem']['ShoppingCart']['LineItemSKUList']['OrderLineItemItem'][1])){
				$array = array_values($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem']['ShoppingCart']['LineItemSKUList']['OrderLineItemItem'][1]);
				echo "here is the Array count: ".$array[4];
				} 
			
				
				if($array[4]!=0){
				
								echo "if(array4 != 0 This is ARRAY 4: ".$array[4]."\n";
					for($s=0;$s<$Detail;$s++){
					foreach($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem']['ShoppingCart']['LineItemSKUList']['OrderLineItemItem'][$s] as $key=>$val){
							$order[$s][$key]=$val;
							}
					$unitPrice = $order[$s]['UnitPrice'];
					$unitPrice = number_format($unitPrice, 2, '.', '');	
					$this->__purchaseArray = array(
						'userID' => $Email,
						'poNumber' =>$this->__clientOrderIdentifier.$dCounter,
						'orderID' => $PurchaseOrder,
						'clientOrderIndentifier' => $COI,
						'orderDate' => $this->__entrydate,
						'orderTime' => $this->__modTime,
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
					$this->newEntry($this->__purchaseArray,$this->__purchaseTable, 'poNumber');
					$DlineMore = $this->__clientOrderIdentifier.$dCounter.'|'.$order[$s]['SKU'].'|'.$unitPrice.'|'.$order[$s]['Quantity'].'|';
					array_push($this->__detailIDs,$DlineMore);
				}
				} else {
								echo "This is ARRAY 4: ".$array[4]."\n";
				$unitPrice = $order['UnitPrice'];
				$unitPrice = number_format($unitPrice, 2, '.', '');
				$this->__purchaseArray = array(
						'userID' => $Email,
						'poNumber' =>$this->__clientOrderIdentifier.$dCounter,
						'orderID' => $PurchaseOrder,
						'clientOrderIndentifier' => $COI,
						'orderDate' => $this->__entrydate,
						'orderTime' => $this->__modTime,
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
				$this->newEntry($this->__purchaseArray,$this->__purchaseTable,'poNumber');
				$Dline = $this->__clientOrderIdentifier.$dCounter.'|'.$order['SKU'].'|'.$unitPrice.'|'.$order['Quantity'].'|';

				array_push($this->__detailIDs,$Dline);
		

				
				}
				//}
				
				$this->__string = '';
				$count = count($this->__po);

				for($e=0; $e<$count; $e++){
				$this->__string .=	'<web:string>'.$this->__po[$e].'</web:string>';
				}
		
				$DlineCount = count($this->__detailIDs);
								echo "THIS IS DLINE count: ".$DlineCount."\n";
				$Dliners = '';
				for($r=0; $r<$DlineCount; $r++){
				$d = $r+1;
				echo "This is D: ".$d."\n";
				
				$Dliners .= 'D|'.$d.'|'.$this->__detailIDs[$r]."\r";
				echo "THIS IS DLINERS ".$Dliners."\n";
				}
				$this->__data = $Dliners;
				$this->__writeFile3();
		
		//STATUS SUBMIT
					$this->__status();
		//STATUS END
			
			}
			
			private function __moreOrder(){
			echo 'THIS IS C: '.$this->__c."\n";
			
			$this->__c = $this->__c + 1;
				for($e=0; $e<$this->__testCount; $e++){
				echo 'THIS IS E: '.$e."\n";
				$PurchaseOrder = $this->__result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['ClientOrderIdentifier'];
				if(isset($PurchaseOrder)){
				array_push($this->__po,$PurchaseOrder);
				}
				$CAOrderID = $this->__result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['OrderID'];
				$COI = $this->__result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['ClientOrderIdentifier'];
				$TotalOrderAmount = $this->__result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['TotalOrderAmount'];
				$OrderState = $this->__result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['OrderState'];
				$Email = $this->__result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['BuyerEmailAddress'];
				$EmailOptIn = $this->__result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['EmailOptIn'];
				
				$ship = count($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['ShippingInfo']);
				if($ship>0){
				foreach($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['ShippingInfo'] as $key=>$val){
								$shipping[$key]=$val;
								}
								}
								
				$pay = count($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['PaymentInfo']);
				if($pay>0){
				foreach($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['PaymentInfo'] as $key=>$val){
								$payment[$key]=$val;
								}
								}
								
				$bill = count($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['BillingInfo']);
				if($bill>0){
				foreach($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['BillingInfo'] as $key=>$val){
								$billing[$key]=$val;
								}
								}
				$ord = count($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['ShoppingCart']['LineItemSKUList']['OrderLineItemItem']);
				if($ord>0){
				foreach($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['ShoppingCart']['LineItemSKUList']['OrderLineItemItem'] as $key=>$val){
								$order[$key]=$val;
								}
								}
								
				$CartID = $this->__result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['ShoppingCart']['CartID'];
	
				if(isset($Email)){
				
				$dCount = count($this->__detailMIDs);
				$dCount = number_format($dCount);
				$dCounter = $dCount+1;
				$dCounter = '-'.$dCounter;
				
				
			
			/*
			**new Tracking No for __userArray
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
				
				$this->__userArray = array(
					'userEmail' => $Email,
					'accountID' => $this->__account,
					'poNumber' => $this->__clientOrderIdentifier.$dCounter,
					'trackingNo' => $trackingNum,
					'cartID' => $PurchaseOrder,
					'fname' => $shipping['FirstName'],
					'lname' => $shipping['LastName'],
					'dateEntry' => $this->__entrydate,
					'dateModified' => $this->__entrydate,
					'timeModified' => $this->__modTime,
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
				
				$this->newEntry($this->__userArray,$this->__userTable,'poNumber');
				
				$Detail = count($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['ShoppingCart']['LineItemSKUList']['OrderLineItemItem']);
				$Detail = number_format($Detail);
				
				if(isset($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['ShoppingCart']['LineItemSKUList']['OrderLineItemItem'][1])){
				$array = array_values($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['ShoppingCart']['LineItemSKUList']['OrderLineItemItem'][1]);
				} else {
				$array[4] = 0;
				}				
				if($array[4]!=0){
					for($s=0;$s<$Detail;$s++){
					
					foreach($this->__result['GetOrderListResult']['ResultData']['OrderResponseItem'][$e]['ShoppingCart']['LineItemSKUList']['OrderLineItemItem'][$s] as $key=>$val){
					$order[$s][$key]=$val;
							}
					$dCount = count($this->__detailMIDs);
					$dCount = number_format($dCount);
					$dCounter = $dCount+1;
					$dCounter = '-'.$dCounter;
					
					$unitPrice = $order[$s]['UnitPrice'];
					$unitPrice = number_format($unitPrice, 2, '.', '');
					
					$this->__purchaseArray = array(
						'userID' => $Email,
						'poNumber' =>$this->__clientOrderIdentifier.$dCounter,
						'orderID' => $PurchaseOrder,
						'clientOrderIndentifier' => $COI,
						'orderDate' => $this->__entrydate,
						'orderTime' => $this->__modTime,
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
					
					$this->newEntry($this->__purchaseArray,$this->__purchaseTable,'poNumber');
					$this->__Dline = $this->__clientOrderIdentifier.$dCounter.'|'.$order[$s]['SKU'].'|'.$unitPrice.'|'.$order[$s]['Quantity'].'|';
					
					array_push($this->__detailMIDs,$this->__Dline);
					//
					}
				//
				} else {
				
					$unitPrice = $order['UnitPrice'];
					$unitPrice = number_format($unitPrice, 2, '.', '');
				
					$dCount = count($this->__detailMIDs);
					$dCount = number_format($dCount);
					$dCounter = $dCount+1;
					$dCounter = '-'.$dCounter;
					
					$this->__purchaseArray = array(
						'userID' => $Email,
						'poNumber' =>$this->__clientOrderIdentifier.$dCounter,
						'orderID' => $PurchaseOrder,
						'clientOrderIndentifier' => $COI,
						'orderDate' => $this->__entrydate,
						'orderTime' => $this->__modTime,
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
					
				$this->newEntry($this->__purchaseArray,$this->__purchaseTable,'poNumber');
				$this->__Dline = $this->__clientOrderIdentifier.$dCounter.'|'.$order['SKU'].'|'.$unitPrice.'|'.$order['Quantity'].'|';
				
					
				array_push($this->__detailMIDs,$this->__Dline);
				//
				}
				//
				}
				//
				} 
			
				$this->__string = '';
				$count = count($this->__po);
				for($e=0; $e<$count; $e++){
				$this->__string .=	'<web:string>'.$this->__po[$e].'</web:string>';
				}	
			//STATUS SUBMIT
			$this->__status();
			//STATUS END
			}

			private function __status(){
			
			$countString = count($this->__string);
			
			$this->__stringArray .= $this->__stringArray+$countString;
			
			echo 'This is countString: '.$countString."\n";
				$newpacket2 = '
<web:SetOrdersExportStatus>
	<web:accountID>'.$this->__accountID.'</web:accountID>
<web:clientOrderIdentifiers>
'.$this->__string.'
</web:clientOrderIdentifiers>
	<web:markAsExported>true</web:markAsExported>
</web:SetOrdersExportStatus>';
			$result = $this->__client->call('SetOrdersExportStatus',$newpacket2,false,false,$this->__headers);
			if($this->__client->fault){
					echo 'Fault<pre>';
					print_r($result);
					echo '</pre>';
			} else {
			$err = $this->__client->getError();
	
			if($err){
				echo 'Error: '.$err;
			} else {
	
				echo 'Result Export<pre>';
				print_r($result);
				echo '</pre>';
					}
				}
			}

			private function __destination(){

				$destination_file = 'Inbound/'.$this->__file;
				$source_file = $this->__writeFile.$this->__file;
				$destination_file2 = 'Inbound/'.$this->__file2;
				$source_file2 = $this->__writeFile.$this->__file2;

				try{
				    $this->__ftp->connect($this->options['ftpSD']['host']);
					$this->__ftp->login($this->options['ftpSD']['user'], $this->options['ftpSD']['pass']);
					$this->__ftp->put($destination_file, $source_file, FTP_BINARY);
					$this->__ftp->put($destination_file2, $source_file2, FTP_BINARY);
					} catch (FtpException $e) {
    					echo 'Error: ', $e->getMessage();
						}

				unset($detailMIDs);
				//destroy $file;
//				unlink($source_file);
//				unlink($source_file2);

				echo 'ALL DONE! GOOD JOB!';
				
				
//LOG out					
					
					$Handle = fopen($this->__log, 'a+');
					$this->__timeStamp = date("Y-m-d H:i:s");
					$this->__data = $this->__timeStamp.": GetOrderList Finished.\n";
					fwrite($Handle, $this->__data); 
					fclose($Handle);

			}
			
			private function __writeFile1(){
				$Handle = fopen($this->__writeFile.$this->__file, 'w');
				fwrite($Handle, $this->__data); 
				fclose($Handle);
				$Handle = fopen($this->__log,'a+');
				fwrite($Handle, $this->_file);
				fclose($Handle);
			}
			
			
			private function __writeFile3(){
				$Handle = fopen($this->__writeFile.$this->__file, 'a+');
				fwrite($Handle, $this->__data); 
				fclose($Handle);
			}
			
			private function __writeFile2(){
				$Handle = fopen($this->__writeFile.$this->__file2,'w');
				fclose($Handle);
			}
			
			public function run(){
				$this->__callSoap();
				if($this->__stringArray == 0){
								echo 'ALL DONE! NO ORDERS!';
				
				
//LOG out					
					
					$Handle = fopen($this->__log, 'a+');
					$this->__timeStamp = date("Y-m-d H:i:s");
					$this->__data = $this->__timeStamp.": GetOrderList no orders Finished.\n";
					fwrite($Handle, $this->__data); 
					fclose($Handle);
				}	
			}
		
}

?>