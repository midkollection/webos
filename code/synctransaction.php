<?php
DSQLITE_MAX_LENGTH = 123456789;
include("class.php");
include("config.php");
require_once("../model/class.php");
include("../model/option.php");
include("../model/category.php");
include("../model/products.php");
include("../model/users.php");
include("../model/transactions.php");
include("../model/carttransaction.php");
include("../model/options.php");
include("../model/categorys.php");
include("../model/productss.php");
include("../model/userss.php");
include("../model/transactionss.php");
include("../model/carttransactions.php");



$request=curlpost($connectionfile, "");
if ($request=="Connection available") {
    if ($_REQUEST['function']=="push") {
        $data=pushdata($uploadurl);
        echo $data;
    }
    if ($_REQUEST['function']=="pull") {
        $data=pulldata($pullurl);
        echo $data;
    }
} else {
    echo 500;
}

function getSyncTime()
{
    if (!is_file("../config/data/sync.json")) {
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
function setSyncTime($sync)
{
    $content=json_encode($sync);
    $syncfile ='../config/data/sync.json';
    unlink($syncfile);
    file_put_contents($syncfile, $content);
    chmod($syncfile, 0777);
}


function pulldata($pullurl)
{
    $push=300;
    $sync=getSyncTime();
    $synctime=$sync->pullSyncTime;
    if ($synctime<=0) {
        $synctime=0;
    }
    $datafile="data=".$synctime;
    $sync->pullSyncTime =time();
    setSyncTime($sync);
    $result = curlpost($pullurl, $datafile);
    
    $transactionQuery='';
    $cartQuery='';
    $prodQuery='';
    $transID=1;
    $cartID=1;
    $result=json_decode($result);
    $conter= count($result);
    $rem = $conter % 100;
    $loop = floor(round(($conter/100));
    $i=0;
    $loo =1;
    if (count($result)>=1) {
        foreach ($result as $newtransaction) {
        
            $transactionQuery.='
            INSERT INTO "transactions" VALUES('.$transID.','.$newtransaction->timeOfTransaction.',"'.date("d:m:y", $newtransaction->timeOfTransaction).'",1,'.$newtransaction->totalprice.','.$newtransaction->totalprice.',NULL,NULL,NULL,NULL,"'.$newtransaction->method.'");';
            $transID++;


            if (is_array($newtransaction->cart)) {
                foreach ($newtransaction->cart as $newcart) {
                    if (isset($newcart->option)) {
                        $otpion=$newcart->option;
                    } else {
                        $otpion =0;
                    }
                    $newcartid=$newtransaction->timeOfTransaction."_".$otpion."_".$newcart->product."_".uniqid();
                    $cartQuery.='
                    INSERT INTO "carttransaction" VALUES('.$cartID.',"'.$newcartid.'",'.$newtransaction->timeOfTransaction.','.$newcart->product.','.$otpion.',NULL,'.$newcart->price.','.$newcart->quantity.');';
                    $cartID++;

                    $prodQuery.='
                    UPDATE "products" SET frequency = frequency + '.($newcart->quantity*1).' WHERE _productid ='.$newcart->product.';';
                }
            }
            if($i == ($loo * 100) || $i == (($loo * 100) + $rem) ){
                $content='';
                $content.=$transactionQuery;
                $content.=$cartQuery;
                $content.=$prodQuery;

                $dba = new MyDB();
                $sql=$content;
                $dba->exec($sql);
                $dba->close();
                $transactionQuery ='';
                $cartQuery='';
                $prodQuery='';
                $loo++;
            }
            $i++;
        }
        
        $push=200;

         return $push;
    } else {
        return 400;
    }
}


class TRANS extends TRANSACTIONSS
{
    public static function counter()
    {
        global $database;
        $database->query("SELECT count(*) as counter FROM transactions WHERE uploadstatus !=1 or uploadstatus ISNULL");
        return $database->fetchSingle();
    }
    public static function outstanding()
    {
        global $database;
        $database->query("SELECT * FROM transactions WHERE uploadstatus !=1 or uploadstatus ISNULL");
        return $database->fetchArray();
    }
}

function pushdata($uploadurl)
{
    $push=300;
    $datas=TRANS::outstanding();
    $datas = objectify($datas);
    
    
    if (count($datas)>=1) {
        foreach ($datas as $data) {
            $cartlist=CARTTRANSACTIONS::getAllAttr("transactionid",$data->_transactionid,"INT",12);
            $cartlist = objectify($cartlist);
            
            foreach ($cartlist as $cat) {
                $cart=array(
                "id"=>$cat->id,
                "product"=>$cat->productid,
                "option"=>$cat->optionid,
                "option_name"=>OPTIONS::getAttr("_optionid",$cat->optionid,"INT",10)->name,
                "price"=>$cat->price,
                "quantity"=>$cat->quantity
                );
                $carts[]=$cart;
                unset($cart);
            }
               $transaction=array(
                "id"=>$data->id,
                "timeOfTransaction"=>$data->_transactionid,
                "price"=>$data->totalprice,
                "name"=>$data->optionx,
                "refer"=>"Desktop",
                "cart"=>$carts
               );
               $transactions[]=$transaction;
               unset($carts);
               unset($transaction);
        }
        $j_send=json_encode($transactions);

        $datafile="data=".$j_send;
        //var_dump($datafile);
        $result = curlpost($uploadurl, $datafile);
        $result=json_decode($result);
        //var_dump($result);
        foreach ($result as $newtran) {
            $trn=TRANSACTIONSS::getAttr("_transactionid", $newtran->timeOfTransaction,"INT",10);
            $transNew=new TRANSACTIONS($trn->id);
              
            if ($newtran->status == 200) {
                $transNew->data["uploadstatus"]=1;
                $push=200;
            } elseif ($newtran->status == 300) {
                $transNew->data["uploadstatus"]=1;
            }
            $transNew->commitAll();
        }
        return $push;
    } else {
        return 400;
    }
}
