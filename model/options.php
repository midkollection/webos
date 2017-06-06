<?php
class OPTIONS
{

//    Get all data rows under table users......INPUT: none......OUTPUT: Result Array//
    public static function getAll()
    {
        global $database;
        $database->query("SELECT * FROM option");
        return $database->fetchArray();
    }
    
//    Get single row under table users......INPUT: id......OUTPUT: Result Object//    
    public static function get($rowId)
    {
        global $database;
        $database->query("SELECT * FROM option where id = $rowId");
        return $database->fetchSingle();
    }
    
//    Get single row under table users......INPUT: id......OUTPUT: Result Object//
    public static function getParam($rowId)
    {
        global $database;
        $param=array(
        ":id"=>array(
            "value"=>$rowId,
            "param"=>"INT"
            )
        );
        
        $database->prepareBind("SELECT * FROM option where id = :id", $param);
        return $database->fetchSingle();
    }
//    Get single row under table users......INPUT: array......OUTPUT: Result Object//
    public static function getFullAttr($data)
    {
        global $database;
        $que;
        foreach($data as $key => &$val){
            $line=ltrim($key, ":");
            $que[]="$line = $key";
        }
        $full=implode(" AND ", $que);
        $database->prepareBind("SELECT * FROM option where $full", $data);
        return $database->fetchSingle();
    } 
    //    Get Array under table users......INPUT: Column Name, Value......OUTPUT: Result Array//
    public static function getAllAttr($name, $value, $type, $length)
    {
        global $database;
        $param=array(
            ":$name"=>array(
            "value"=>$value,
            "param"=>$type,
            "length"=>$length
            )
        );
        $database->prepareBind("SELECT * FROM option where $name = :$name", $param);
        return $database->fetchArray();
    }  
//    Get single row under table users....INPUT: Column Name, Value....OUTPUT: Result Object//
    public static function getAttr($name, $value, $type, $length)
    {
        global $database;
        $param=array(
            ":$name"=>array(
            "value"=>$value,
            "param"=>$type,
            "length"=>$length
            )
        );
        $database->prepareBind("SELECT * FROM option where $name = :$name", $param);
        return $database->fetchSingle();
    }
    
//    Delete single row from table users......INPUT: id......OUTPUT: null//
    public static function delete($rowId)
    {
        global $database;
        $sql = "DELETE FROM option WHERE id = $rowId";
        $database->query($sql);
    }
    
//    Delete single row from table users......INPUT: Column Name, Value......OUTPUT: null//
    public static function deleteAttr($name, $value)
    {
        global $database;
        $sql = "DELETE FROM option WHERE $name = '$value'";
        $database->query($sql);
    }
    
//    Get row count for stated attribute under table users...INPUT: Column Name, Value...OUTPUT: Result Object->count//
    public static function countAttr($name, $value)
    {
        global $database;
        $sql = "SELECT count(*) as count FROM option WHERE $name = '$value'";
        $database->query($sql);
        return $database->fetchSingle();
    }
    
 //    Insert single row into column name under table users......INPUT: Value......OUTPUT: new Object//
    public static function set($name = "")
    {
        global $database;
        $database->query("INSERT INTO option (`name`) VALUES ('$name')");
        return self::get($database->insertId());
    }
    
    //    Insert single row into any column under table users......INPUT: Value......OUTPUT: new Object//
    public static function setAttr($name = "",$value = "")
    {
        global $database;
        $database->query("INSERT INTO option (`$name`) VALUES ('$value')");
        return self::get($database->insertId());
    }
    
 //    Insert a unique single row into column name under table users......INPUT: Value......OUTPUT: Object//
    public static function setUnique($data)
    {
        if (!is_object(self::getFullAttr($data))) {
            $que;
            foreach($data as $key => &$val){
                $line=ltrim($key, ":");
                $que[$line]=$val["value"];
            }
            $new=self::setParam($que);
        } else {
            $new=self::getFullAttr($data);
        }
        return $new;
    }
    
 //    Insert single row into column name under table users......INPUT: Array......OUTPUT: new Object//
    public static function setParam($param)
    {
        global $database;
        foreach ($param as $key => &$val) {
            $keys[]=$key;
            $vals[]="'".addslashes($val)."'";
        }
        $database->query("INSERT INTO option (".implode(', ', $keys).") VALUES (".implode(', ', $vals).")");
        return self::get($database->insertId());
    }
}
