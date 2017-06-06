<?php

class SQLITE_SEQUENCE
{
    public $rowid=0;
    public $data=array(
        "name" => "",
        "seq" => "",
        "classPointer"=>0
    );
    public $temp=array(
        "name" => "",
        "seq" => "",
        "classPointer"=>0
    );
    
    
    public function __construct($objId)
    {
        $object=SQLITE_SEQUENCES::getParam($objId);
        foreach ($this->data as $key => $value) {
            unset($value);
            if ($key != "classPointer") {
                $this->data[$key] = $object->$key;
                $this->temp[$key] = $object->$key;
                if ($key=="id") {
                    $this->rowid= $object->$key;
                
                }
            }
        }
    }
    public function commitAll()
    {
        global $database;
        foreach ($this->data as $key => $value) {
            if ($value != $this->temp[$key] && $key != "classPointer") {
                $sql = 'UPDATE sqlite_sequence SET '.$key.'="'.$value.'" WHERE id = '.$this->rowid;
                $database->query($sql);
            }
           
        }
    }
    public function commit($name)
    {
        global $database;
        $sql = "UPDATE sqlite_sequence SET $name = '".$this->data[$name]."'  WHERE id = $this->rowid";
        $database->query($sql);
    }
    public function revert()
    {
        foreach ($this->data as $key => $value) {
            unset($value);
            $this->data[$key] = $this->temp[$key];
        }
    }
}
