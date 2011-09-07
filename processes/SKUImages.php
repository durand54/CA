<?php

class SKUImages implements Process {

	private $__zipped;
	private $__files1;
	private $__source;
	private $__count;
	private $__counter;
	private $__t = 0;
	private $__r = 25;
	private $__number = '';
	private $__ftp;
	private $__dir;
	private $__log;
	private $__timeStamp;

	public function __construct(){
        $this->options = $_ENV['CONFIG'];
		$this->__ftp = new Ftp;
		$this->__dir = "processes/";//directory needs to be set
		$this->__log = $this->__dir.$this->options['log'];
		$Handle = fopen($this->__log, 'a+');
		$this->__timeStamp = date("Y-m-d H:i:s");
		$this->__data = $this->__timeStamp.": SKUImages started.\n";
		fwrite($Handle, $this->__data); 
		fclose($Handle);
        }

	private function __superD(){

		try {
		    $this->__ftp->connect($this->options['ftpSKU']['host']);
		    $this->__ftp->login($this->options['ftpSKU']['user'], $this->options['ftpSKU']['pass']);
		    $directory = '';
		    $a = $this->__ftp->nlist($directory);
		    foreach($a as $value){
		    $rest = substr($value, 0, -7);
			$date = date('Ymd');
//			$date = '20101112';
			if($rest == $date){
			$zip = $value;
//			echo 'This is ZIP: '.$zip."\n";
			$this->__zipped = $zip;
			$temp = $this->__dir.'/temp/'.$value;
			$this->__ftp->get($temp, $value, FTP_BINARY);
//			echo "SUCCESS!\n";
			}
		    }
			    $this->__ftp->close();
			   } catch (FtpException $e) {
 			   echo 'Error: ', $e->getMessage();
 			   
		$Handle = fopen($this->__log, 'a+');
		$this->__timeStamp = date("Y-m-d H:i:s");
		$this->__data = $this->__timeStamp.": SKUImages zip was not available.\n";
		fwrite($Handle, $this->__data); 
		fclose($Handle);
		
			   exit();
			}

	}
	
	private function __unpac(){
			$dir    = $this->__dir.'temp/img';
			$zip = $this->__dir.'temp/'.$this->__zipped;					
			exec("unzip -jo $zip -d $dir");
		
			$this->__files1 = scandir($dir);
			$this->__files1 = array_slice($this->__files1,2);
		
			$this->__count = count($this->__files1);
//			echo 'THIS IS THE COUNT OF IMAGES: '.$this->__count."\n";
	}
	
	private function __sendToCA(){
			$from = $this->__dir.'/temp/img/';
			$this->__counter = number_format($this->__count/25);
			$this->__counter = ceil($this->__counter);
		
//			echo 'THIS IS THE COUNTER: '.$this->__counter."\n";
		
			$this->__source = array();
		
			foreach($this->__files1 as $value){
			$rest = array($value,$from.$value);
			array_push($this->__source,$rest);
			}
		//print_r($this->__source);
			unset($this->__files1);
			
			for($e=0;$e<$this->__counter;$e++){
		
				for($i=$this->__t;$i<$this->__r;$i++){
					try {
    		
   					$this->__ftp->connect($this->options['ftpD']['host']);
    				$this->__ftp->login($this->options['ftpD']['user'], $this->options['ftpD']['pass']);
    				$this->__ftp->put($this->__source[$i][0], $this->__source[$i][1], FTP_BINARY);
//    				echo 'SUCCESS! FILE: '.$this->__source[$i][0]." DORK LOADED!\n";
    				$this->__ftp->close();
    				sleep(1);
//    				echo 'thanks for the rest!<br />';
    		
   					$this->__ftp->connect($this->options['ftpMMG']['host']);
    				$this->__ftp->login($this->options['ftpMMG']['user'], $this->options['ftpMMG']['pass']);
    				$this->__ftp->put($this->__source[$i][0], $this->__source[$i][1], FTP_BINARY);
//    				echo 'SUCCESS! FILE: '.$this->__source[$i][0]." MMG LOADED!\n";
    				$this->__ftp->close();
    				sleep(1);
//   				echo 'thanks for the rest!<br />';
    				} catch (FtpException $e) {
//    				echo 'Error: ', $e->getMessage();
					}
					
					if($this->__number != ''){
    				$new = $i+$this->__number;
//    				echo 'This is $i: '.$i." ".$new."\n";
    				} else {
//    				echo 'This is $i: '.$i."\n";
    				}
			
				}
					
			$this->__t = $i;
    		$this->__r = $this->__r+25;
    		
    		if($this->__count<$this->__r){
    		$this->__r = $this->__count-$this->__t;
    		$this->__r = $this->__r+1;
    		$this->__t = 0;
    		
    		if($this->__number != ''){
    		$this->__number = $i+$this->__number;
    		} else {
    		$this->__number = $i;
    		}

    		}
		
		//echo 'This is t: '.$this->__t." this is r: ".$this->__r."this is e: ".$e.' this is counter: '.$this->__counter."\n";
				
				
			}
			
		unlink($this->__dir.'/temp/'.$this->__zipped);
		for($e=0;$e<$count;$e++){
		unlink($this->__source[$i][1]);
		}
		unset($this->__source);
//		echo 'ALL DONE!';
		
		
		$Handle = fopen($this->__log, 'a+');
		$this->__timeStamp = date("Y-m-d H:i:s");
		$this->__data =  $this->__timeStamp.": SKUImages finished it's task.\n";
		fwrite($Handle, $this->__data); 
		fclose($Handle);
			
	}
	
	public function run(){
		$this->__superD();	
		$this->__unpac();
		$this->__sendToCA();
	}

}

?>