<?php
class MyDB extends SQLite3
   {
      function __construct()
      {
         $this->open('../config/pos.sqlite');
      }
 }
 class MysqlDb{
 public $connection;
 private $last_query;
 private $magic_quotes_active;
 private $real_escape_string_exists;


 public function __construct() {	
	$db = new MyDB();
	$this->connection = $db;
	$this->open_connection();
	$this->magic_quotes_active = get_magic_quotes_gpc();
	$this->real_escape_string_exists = function_exists( "escapeString" );
 }

public function open_connection() {
	 //$this->connection = new MysqlDb();
   		if(!$this->connection){
			echo "Opened database Caput!!\n";
      echo $this->connection->lastErrorMsg();
   		} else {
      
   		}
}


 public function close_connection(){
     if(isset($this->connection)){
         $this->connection->close();
         unset($this->connection);
     }
 }

 public function query($sql){
     $this->last_query   =   $sql;
     $results            = $this->connection->query($sql);
     return $results;
 }

 public function fetch_array($results){
     return sqlite_fetch_array($results);         
 }

 public function num_row($results){
     return sqlite_num_rows($results);
 }

 public function insert_id(){
     return sqlite_last_insert_rowid($this->connection);
 }

 public function affected_row(){
     return sqlite3_affected_rows();
 }
}
$database2   =   new MysqlDb();


?>