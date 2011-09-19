<?php

class SKUImages implements Process {

	private $_zipped;
	private $_files1;
	private $_source;
	private $_count;
	private $_counter;
	private $_t = 0;
	private $_r = 25;
	private $_number = '';
	private $_ftp;
	private $_dir;
	private $_log;
	private $_timeStamp;

	public function _construct(){
        $this->options = $_ENV['CONFIG'];
		$this->_ftp = new Ftp;
		$this->_dir = "processes/";//directory needs to be set
		$this->_log = $this->_dir.$this->options['log'];
		$Handle = fopen($this->_log, 'a+');
		$this->_timeStamp = date("Y-m-d H:i:s");
		$this->_data = $this->_timeStamp.": SKUImages started.\n";
		fwrite($Handle, $this->_data); 
		fclose($Handle);
        }

	private function _superD(){

		try {
		    $this->_ftp->connect($this->options['ftpSKU']['host']);
		    $this->_ftp->login($this->options['ftpSKU']['user'], $this->options['ftpSKU']['pass']);
		    $directory = '';
		    $a = $this->_ftp->nlist($directory);
		    foreach($a as $value){
		    $rest = substr($value, 0, -7);
			$date = date('Ymd');
//			$date = '20101112';
			if($rest == $date){
			$zip = $value;
//			echo 'This is ZIP: '.$zip."\n";
			$this->_zipped = $zip;
			$temp = $this->_dir.'/temp/'.$value;
			$this->_ftp->get($temp, $value, FTP_BINARY);
//			echo "SUCCESS!\n";
			}
		    }
			    $this->_ftp->close();
			   } catch (FtpException $e) {
 			   echo 'Error: ', $e->getMessage();
 			   
		$Handle = fopen($this->_log, 'a+');
		$this->_timeStamp = date("Y-m-d H:i:s");
		$this->_data = $this->_timeStamp.": SKUImages zip was not available.\n";
		fwrite($Handle, $this->_data); 
		fclose($Handle);
		
			   exit();
			}

	}
	
	private function _unpac(){
			$dir    = $this->_dir.'temp/img';
			$zip = $this->_dir.'temp/'.$this->_zipped;					
			exec("unzip -jo $zip -d $dir");
		
			$this->_files1 = scandir($dir);
			$this->_files1 = array_slice($this->_files1,2);
		
			$this->_count = count($this->_files1);
//			echo 'THIS IS THE COUNT OF IMAGES: '.$this->_count."\n";
	}
	
	private function _sendToCA(){
			$from = $this->_dir.'/temp/img/';
			$this->_counter = number_format($this->_count/25);
			$this->_counter = ceil($this->_counter);
		
//			echo 'THIS IS THE COUNTER: '.$this->_counter."\n";
		
			$this->_source = array();
		
			foreach($this->_files1 as $value){
			$rest = array($value,$from.$value);
			array_push($this->_source,$rest);
			}
		//print_r($this->_source);
			unset($this->_files1);
			
			for($e=0;$e<$this->_counter;$e++){
		
				for($i=$this->_t;$i<$this->_r;$i++){
					try {
    		
   					$this->_ftp->connect($this->options['ftpD']['host']);
    				$this->_ftp->login($this->options['ftpD']['user'], $this->options['ftpD']['pass']);
    				$this->_ftp->put($this->_source[$i][0], $this->_source[$i][1], FTP_BINARY);
//    				echo 'SUCCESS! FILE: '.$this->_source[$i][0]." DORK LOADED!\n";
    				$this->_ftp->close();
    				sleep(1);
//    				echo 'thanks for the rest!<br />';
    		
   					$this->_ftp->connect($this->options['ftpMMG']['host']);
    				$this->_ftp->login($this->options['ftpMMG']['user'], $this->options['ftpMMG']['pass']);
    				$this->_ftp->put($this->_source[$i][0], $this->_source[$i][1], FTP_BINARY);
//    				echo 'SUCCESS! FILE: '.$this->_source[$i][0]." MMG LOADED!\n";
    				$this->_ftp->close();
    				sleep(1);
//   				echo 'thanks for the rest!<br />';
    				} catch (FtpException $e) {
//    				echo 'Error: ', $e->getMessage();
					}
					
					if($this->_number != ''){
    				$new = $i+$this->_number;
//    				echo 'This is $i: '.$i." ".$new."\n";
    				} else {
//    				echo 'This is $i: '.$i."\n";
    				}
			
				}
					
			$this->_t = $i;
    		$this->_r = $this->_r+25;
    		
    		if($this->_count<$this->_r){
    		$this->_r = $this->_count-$this->_t;
    		$this->_r = $this->_r+1;
    		$this->_t = 0;
    		
    		if($this->_number != ''){
    		$this->_number = $i+$this->_number;
    		} else {
    		$this->_number = $i;
    		}

    		}
		
		//echo 'This is t: '.$this->_t." this is r: ".$this->_r."this is e: ".$e.' this is counter: '.$this->_counter."\n";
				
				
			}
			
		unlink($this->_dir.'/temp/'.$this->_zipped);
		for($e=0;$e<$count;$e++){
		unlink($this->_source[$i][1]);
		}
		unset($this->_source);
//		echo 'ALL DONE!';
		
		
		$Handle = fopen($this->_log, 'a+');
		$this->_timeStamp = date("Y-m-d H:i:s");
		$this->_data =  $this->_timeStamp.": SKUImages finished it's task.\n";
		fwrite($Handle, $this->_data); 
		fclose($Handle);
			
	}
	
	public function run(){
		$this->_superD();	
		$this->_unpac();
		$this->_sendToCA();
	}

}

?>