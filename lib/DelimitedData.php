<?php

class DelimitedData {
    
    private $options = array(
        'delimiter' => "\t", 
        'ending' => "\n"
    );
    public $columns;
    public $rows;
    
    public function __construct($options=array()){
        $this->options = array_merge($this->options, $options);
        $this->columns = array();
        $this->rows = array();
    }
    
    public function parse($data){
        $lines = explode($this->options['ending'], $data);
        $this->columns = $this->parse_row(array_shift($lines));
        
        foreach($lines as $row){
            $cells = array();
            foreach($this->parse_row($row) as $col => $cell){
                $cells[$this->columns[$col]] = $cell;
                unset($cell);
            }
            array_push($this->rows, $cells);
            unset($cells);
        }
        
        unset($data);
    }
    
    public function flatten($options=array()){
        $options = array_merge($this->options, $options);
        $rows = $this->rows;
        $output = implode($this->get_columns(), $options['delimiter']);
        $output .= $options['ending'];
        
        foreach($rows as $row){
            $output .= implode($row, $options['delimiter']);
            $output .= $options['ending'];
            unset($row);
        }
        
        return $output;
    }
    
    private function parse_row($row){
        return array_map('trim', explode(
            $this->options['delimiter'], 
            ' ' . $row . ' '));
    }
    
    private function get_columns(){
        return (count($this->columns) > 0)?
            $this->columns : array_keys($this->rows[0]);
    }
}

?>