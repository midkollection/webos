<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('max_execution_time', 0);

include("config.php");
if($_REQUEST['function']=="firsttime"){ 
  if(is_file("config/settings.php")>=1){
        echo 200;
    }else{
        echo 500;
    }  
}

if($_REQUEST['function']=="exist"){ 
    if(is_file($sqlitefile)>=1){
        echo 200;
    }else{
        echo 500;
    }
}

if($_REQUEST['function']=="connection"){ 
    $request=curlpost($connectionfile,"");
        if ($request=="Connection available")
        {
          echo 200;  
        }else{
          echo 500;
        }
}

if($_REQUEST['function']=="createdatabase"){
   include("class.php");
   @unlink('../config/data/sync.json');
   $db = new MyDB();
   if(!$db){
      $rez=400;
   } else {
      $rez= 300;
   }
   
   $sql ='DROP TABLE IF EXISTS `products`;
   CREATE TABLE "products" ("id" INTEGER PRIMARY KEY AUTOINCREMENT, "name" VARCHAR,"_productid" INT UNIQUE,"code" VARCHAR,"price" INT,"categoryid" INT, "status" INT, "update" INT,"photo" VARCHAR,"frequency" INT);
   DROP TABLE IF EXISTS `category`;
   CREATE TABLE "category" ("id" INTEGER PRIMARY KEY AUTOINCREMENT, "name" VARCHAR,"_categoryid" INT UNIQUE);
   DROP TABLE IF EXISTS `option`;
   CREATE TABLE "option" ("id" INTEGER PRIMARY KEY AUTOINCREMENT, "name" VARCHAR,"_optionid" INT UNIQUE,"productid" INT,"quantity" INT,"type" VARCHAR);   
   DROP TABLE IF EXISTS `transactions`;
   CREATE TABLE "transactions" ("id" INTEGER PRIMARY KEY AUTOINCREMENT,"_transactionid" INT UNIQUE, "transactiondate" VARCHAR,"uploadstatus" INT,"price" INT,"totalprice" INT,"paymenttype" VARCHAR,"timex" INT,"discount" INT,"status" INT,"refer" INT);  
   DROP TABLE IF EXISTS `carttransaction`;
   CREATE TABLE "carttransaction" ("id" INTEGER PRIMARY KEY AUTOINCREMENT,"_transproductid" VARCHAR UNIQUE, "transactionid" INT,"productid" INT,"optionid" INT,"uploadstatus" INT,"price" INT,"quantity" INT);
   DROP TABLE IF EXISTS `syncdata`;
   CREATE TABLE "syncdata" ("id" INTEGER PRIMARY KEY AUTOINCREMENT,"action" VARCHAR, "synctime" VARCHAR);
   DROP TABLE IF EXISTS `users`;
   CREATE TABLE "users" ("id" INTEGER PRIMARY KEY AUTOINCREMENT,"userid" VARCHAR,"username" VARCHAR, "password" VARCHAR, "role" VARCHAR);
   ';

   $ret = $db->exec($sql);
   if(!$ret){
      echo 500;
   } else {
      $rez= 200;
   }
   $db->close();
    echo $rez;
}

if($_REQUEST['function']=="checkupload"){
    include("startupsync.php");
    //var_dump(checkupload());
    $rez=checkupload();
    if($rez >= 1){
        echo 200;
    }else{
        echo 300;
    }
}
function getSyncTime(){
    if(!is_file("../config/data/sync.json")){
        $file ='../config/data/sync.json';
        $content='{"pullSyncTime":"000","pushSyncTime":"000"}';
        file_put_contents($file, $content);
        chmod($file, 0777);  
    }
    $syncfile ='../config/data/sync.json';
    $sata = file_get_contents($syncfile);
    $sync = json_decode($sata);
    return $sync;
}
function setSyncTime($sync){
    $content=json_encode($sync);
    $syncfile ='../config/data/sync.json';
    unlink($syncfile);
    file_put_contents($syncfile, $content);
    chmod($syncfile, 0777);
}
if($_REQUEST['function']=="checkdownload"){
    $sync=getSyncTime();
    $request=curlpost($dataurl,$sync->pullSyncTime);
    $sync->pullSyncTime = time();
    if(strlen($request)>=10){
    $Datafile ='../config/data/products/products_'.time().'.json';
    $content=$request;
    file_put_contents($Datafile, $content);
    chmod($Datafile, 0777);
        
    echo 200;
    }else{
    echo 300;
    }
}

if($_REQUEST['function']=="checktransaction"){
    $sync=getSyncTime();
    $request=curlobject($pullurl."?data=".$sync->pullSyncTime);
    $sync->pullSyncTime = time();
    if(strlen($request)>=10){
    $Datafile ='../config/data/transactions/transactions_'.time().'.json';
    $content=$request;
    file_put_contents($Datafile, $content);
    chmod($Datafile, 0777);
    setSyncTime($sync);   
    echo 200;
    }else{
    echo 300;
    }
}


if($_REQUEST['function']=="loadProducts"){
    include("startupsync.php");
    $folder = scandir('../config/data/products',1);
    $file=$folder[0];
    $sata = file_get_contents('../config/data/products/'.$file);
    $sync = json_decode($sata);
    dataSyncFile($sync,$photourl,$logourl);   
}


if($_REQUEST['function']=="loadTransactions"){
    include("startupsync.php");
    $folder = scandir('../config/data/transactions',1);
    $file=$folder[0];
    $sata = file_get_contents('../config/data/transactions/'.$file);
    $sync = json_decode($sata);
    transSyncFile($sync);
}

if($_REQUEST['function']=="uploadtransactions"){
    include("startupsync.php");
    echo pushdata($uploadurl);
}


if($_REQUEST['function']=="dbrewrite"){
    if($_REQUEST['data']=="accesscode"){
        $file ='../config/pos.sqlite';
        @unlink($file);
        @unlink('../config/data/sync.json');
        echo 200;   
    }else{
        echo 300;  
    }
}

if($_REQUEST['function']=="startupOption"){
    $file ='config/settings.php';
$content='<?php';
$content.='
$mainu = "'.$_REQUEST["mainu"].'";';
    foreach($_REQUEST as $name => &$value){
        if($name!="function" && $name!="mainu"){
$content.='
$'.$name.' = $mainu."'.$value.'";';
    }
    }
$content.='
?>';
    file_put_contents($file, $content);
    chmod($file, 0777);
    @unlink('../config/pos.sqlite');
    @unlink('../config/data/sync.json');
    echo 200;
}

?>
