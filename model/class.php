<?php
ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
error_reporting(E_ALL);
class Db
{
    public $connection;
    public $results;
    private $lastQuery;
    private $magicQuotesActive;
    private $realEscapeStringExists;
    public $dbError;


    public function __construct()
    {
        
        $this->openConnection();
        $this->magicQuotesActive = get_magic_quotes_gpc();
        $this->realEscapeStringExists = function_exists("escapeString");
    }

    private function openConnection()
    {
        try {
            //$dbx=new PDO("mysql:dbname=A980309_TNA;host=76.163.252.239", "A980309_felixson", "Miklow123");
            $dbx=new PDO('sqlite:../config/pos.sqlite');
            $dbx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection = $dbx;
            $this->connection;
        } catch (PDOException $e) {
            $this->dbError = $e->getMessage();
        }
    }


    private function closeConnection()
    {
        if (isset($this->connection)) {
            $this->connection=null;
            unset($this->connection);
        }
    }

    public function query($sql)
    {
        $this->lastQuery = $sql;
        try {
            $results = $this->connection->query($sql);
            $this->results=$results;
            return $results;
        } catch (Exception $e) {
            $this->dbError = $e->getMessage();
            //die();
            //return $this->dbError;
        }
    }
    
    public function prepareBind($sql, $param)
    {
        try {
            $results = $this->connection->prepare($sql);
            
            foreach ($param as $key => &$val) {
                if (isset($val["param"])) {
                    if ($val["param"]=="STR") {
                        $results->bindParam($key, $val["value"], PDO::PARAM_STR, $val["length"]);
                    } elseif ($val["param"]=="INT") {
                        $results->bindParam($key, $val["value"], PDO::PARAM_INT);
                    }
                } else {
                    $results->bindParam($key,$val["value"]);
                }
            }
            $results->execute();
            $this->results=$results;
            return $results;
        } catch (Exception $e) {
            $this->dbError = $e->getMessage();
        }
    }
    

    public function fetchArray()
    {
        if (isset($this->dbError)) {
            echo $this->dbError;
            die();
        } else {
            $results = $this->results;
            return $results->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    public function fetchSingle()
    {
        if (isset($this->dbError)) {
            echo $this->dbError;
            die();
        } else {
            $results = $this->results;
            return $results->fetchObject();
        }
    }
    public function numRow()
    {
         $results = $this->results;
         return $results->rowCount();
    }
    
    public function busy()
    {
        return $this->connection->inTransaction();
    }
    
    public function rollBack()
    {
        $this->connection->rollBack();
    }

    public function insertId()
    {
         return $this->connection->lastInsertId();
    }

    public function affectedRow()
    {
         return sqlite3_affected_rows();
    }
    
    public function __destruct()
    {
        $this->closeConnection();
    }
}

$database   =   new Db();
?>
