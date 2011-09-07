<?php

class FTP {
    
    private $options;
    private $connection;
    
    public function __construct($user, $password, $host, $options=array()){
        $this->options = array_merge(array(
            'port' => 21, 
            'timeout' => 90, 
            'use_ssl' => FALSE, 
            'mode' => FTP_BINARY, 
            'pasv' => FALSE
        ), $options);
        
        $this->connect($host);
        $this->login($user, $password);
    }
    
    public static function from_array($options){
        return new self($options['user'], $options['password'], 
            $options['host'], $options['options']);
    }
    
    public function __destruct(){
        return ftp_close($this->connection);
    }
    
    private function connect($host){
        $connect = ($this->options['use_ssl'])? 'ftp_ssl_connect' : 'ftp_connect';
        $this->connection = $connect(
            $host, $this->options['port'], $this->options['timeout']);
    }
    
    private function login($username, $password){
        if(!ftp_login($this->connection, $username, $password)){
            throw new FTP_Exception("Login failed.");
        }
        elseif($this->options['pasv']){
            ftp_pasv($this->connection, TRUE);
        }
    }
    
    public function nlist($directory='.'){
        return ftp_nlist($this->connection, $directory);
    }
    
    public function put_data($data, $destination_filename){
        $temp = fopen('php://temp', 'r+');
        fwrite($temp, $data);
        rewind($temp);
        
        $result = $this->put_file($temp, $destination_filename);
        fclose($temp);
        return $result;
    }
    
    public function put_file($file, $destination_filename){
        $result = ftp_fput(
            $this->connection, 
            $destination_filename, 
            $file, 
            $this->options['mode']);
        
        if(!$result)
            throw new FTP_Exception("Failed to upload file data.");
        
        return $result;
    }
    
    public function chdir($directory){
    	$this->connection->ftp_chdir($directory);
    echo "Current directory is now: " . ftp_pwd($this->connection) . "\n";
    }
    
    public function put($filename, $destination_filename,$binary){
        $result = ftp_put(
            $this->connection, 
            $filename, 
            $destination_filename, 
            $binary);
        
        if(!$result)
            throw new FTP_Exception("Failed to upload file: \"$filename\".");
        
        return $result;
    }
    
    public function get_data($filename){
        $temp = fopen('php://temp', 'r+');
        $this->get_file($filename, $temp);
        rewind($temp);
        $data = stream_get_contents($temp);
        fclose($temp);
        
        return $data;
    }
    
    public function get_file($filename, $file){
        $result = ftp_fget(
            $this->connection, 
            $file, 
            $filename, 
            $this->options['mode']);
        
        if(!$result)
            throw new FTP_Exception("Failed to download file: \"$filename\".");
        
        return $result;
    }
    
    public function get($filename, $destination_filename){
        $result = ftp_get(
            $this->connection, 
            $destination_filename, 
            $filename, 
            $this->options['mode']);
        
        if(!$result)
            throw new FTP_Exception("Failed to download file: \"$filename\".");
        
        return $result;
    }
    
    public function delete($filename){
        if(!$result = ftp_delete($this->connection, $filename))
            throw new FTP_Exception("Failed to delete file: \"$filename\".");
        
        return $result;
    }
    
    public function move($filename, $destination_filename){
        $data = $this->get_data($filename);
        if($this->put_data($data, $destination_filename)){
            $this->delete($filename);
            return TRUE;
        }
        
        return FALSE;
    }
}

class FTP_Exception extends Exception {}

?>