<?php
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



function transSyncFile($data){
	$transactionQuery='DROP TABLE IF EXISTS "transactions"; 
    CREATE TABLE "transactions" ("id" INTEGER PRIMARY KEY AUTOINCREMENT,"_transactionid" INT UNIQUE, "transactiondate" VARCHAR,"uploadstatus" INT,"price" INT,"totalprice" INT,"paymenttype" VARCHAR,"timex" INT,"discount" INT,"status" INT,"refer" INT);';
    
    $cartQuery='DROP TABLE IF EXISTS "carttransaction";
     CREATE TABLE "carttransaction" ("id" INTEGER PRIMARY KEY AUTOINCREMENT,"_transproductid" VARCHAR UNIQUE, "transactionid" INT,"productid" INT,"optionid" INT,"uploadstatus" INT,"price" INT,"quantity" INT);';
    $prodQuery="";
    $transID=1;
    $cartID=1;
    $conter= count($data);
    $rem = $conter % 100;
    if($conter >= 100){
    $div = $conter/100;
    $loop = floor($div);
    } else {
        $loop = 0;
    }
    $i=1;
    $loo =1;
    if(is_array($data)){
    foreach($data as $newtransaction){
        $transactionQuery.='
        INSERT INTO "transactions" VALUES('.$transID.','.$newtransaction->timeOfTransaction.',"'.date("d:m:y",$newtransaction->timeOfTransaction).'",1,'.$newtransaction->totalprice.','.$newtransaction->totalprice.',NULL,NULL,NULL,NULL,"'.$newtransaction->method.'");';
        $transID++;
        
        require_once("class.php");
        if(is_array($newtransaction->cart)){
        foreach($newtransaction->cart as $newcart){
            //var_dump($newcart);
            if(isset($newcart->option)){
                $otpion=$newcart->option;
            }else{
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
       if($i == ($loo * 100) || $i == (($loop * 100) + $rem) ){
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
    echo 200;  
	
}
}

function dataSyncFile($data,$photourl,$logourl){
    $prodQuery='DROP TABLE IF EXISTS "products"; 
    CREATE TABLE "products" ("id" INTEGER PRIMARY KEY AUTOINCREMENT, "name" VARCHAR,"_productid" INT UNIQUE,"code" VARCHAR,"price" INT,"categoryid" INT, "status" INT, "update" INT,"photo" VARCHAR,"frequency" INT);';
    
    $catQuery='DROP TABLE IF EXISTS "category";
   CREATE TABLE "category" ("id" INTEGER PRIMARY KEY AUTOINCREMENT, "name" VARCHAR,"_categoryid" INT UNIQUE);';
    
   $optionQuery='DROP TABLE IF EXISTS "option";
   CREATE TABLE "option" ("id" INTEGER PRIMARY KEY AUTOINCREMENT, "name" VARCHAR,"_optionid" INT UNIQUE,"productid" INT,"quantity" INT,"type" VARCHAR); ';
    
    $userQuery='DROP TABLE IF EXISTS "users";
   CREATE TABLE "users" ("id" INTEGER PRIMARY KEY AUTOINCREMENT,"userid" VARCHAR,"username" VARCHAR, "password" VARCHAR, "role" VARCHAR);';
    
    $prodID=1;
    $catID=1;
    $optionID=1;
    $userID=1;
	if($data->status==200){
    $users=$data->users;
    if(is_array($users)){
    foreach($users as $user){
        
        $userQuery.='INSERT INTO "users" VALUES('.$userID.',"'.$user->userid.'","'.$user->username.'","'.$user->password.'","'.$user->role.'");';
        $userID++;
        
    }
    }
    //print_r(USERS::find_all());
	$cats=$data->category;
    
    if(!is_file('../photos/logo.png')){   
	$logo = file_get_contents($logourl);
	file_put_contents('../photos/logo.png', $logo);
    }
    if(is_array($cats)){    
	foreach($cats as $cat){
        $catQuery.='INSERT INTO "category" VALUES('.$catID.',"'.$cat->name.'",'.$cat->id.');';
        $catID++;
        
        if(is_array($cat->products)){
		foreach($cat->products as $prod){
            
        $prodQuery.='INSERT INTO "products" VALUES('.$prodID.',"'.$prod->name.'",'.$prod->id.',"'.$prod->code.'",'.$prod->price.','.$cat->id.',NULL,NULL,"'.$prod->photo.'",0);';
        $prodID++;
            
//                if(!is_file('../photos/'.$prod->photo)){
//                    if(strlen($prod->photo)>=5){
//                      $image = file_get_contents($photourl."".$prod->photo);
//                      file_put_contents('../photos/'.$prod->photo, $image);  
//                    }  
//                }
			
            if(is_array($prod->options)){
			foreach($prod->options as $opt){
                
            $optionQuery.='INSERT INTO "option" VALUES('.$optionID.',"'.$opt->name.'",'.$opt->id.','.$prod->id.','.$opt->quantity.',NULL);';
            $optionID++; 
                
			}
            }
		}
        }
	}
    }
    
    $content='';
    $content.=$userQuery;
    $content.=$catQuery;
    $content.=$prodQuery;
    $content.=$optionQuery;
    require_once("class.php");
    $db = new MyDB();
    $sql=$content;
    $ret = $db->exec($sql);
    $db->close();   
	echo 200;   
	}else{
	 echo 300;	
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
function checkupload(){
    return TRANS::counter()->counter;
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
        $result = curlpost($uploadurl, $datafile);
        $result=json_decode($result);
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


?>

