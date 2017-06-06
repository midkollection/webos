<?php
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



if($_POST['function']=="commit"){

$total=$_POST['total'];

$payment_type=$_POST['type'];
$discount=$_POST['discount'];
$carts=urldecode($_POST['cart']);
$carts=json_decode($carts);
$timer=time();
$totalprice=$total * ((100-($discount*1))/100);
$error=200;
    
    $datax["_transactionid"] = $timer;
    $datax["transactiondate"] = date("d:m:y",$timer);
    $datax["totalprice"] = $totalprice;
    $datax["price"] = $total;
    $datax["paymenttype"] = $payment_type;
    $datax["refer"] = "store_app";
    $datax["uploadstatus"] = '0';
    $datax["discount"] = $discount;
    
       
    $newData[":_transactionid"] = array("value"=>$timer);
            
    $new = TRANSACTIONSS::setUnique($newData);
    $newItem = new TRANSACTIONS($new->id);
    $newItem->data = $datax;
    $newItem->commitAll();
        

foreach($carts as $cart){
	if($cart->quantity > OPTIONS::getAttr("id",$cart->option,"INT",10)->quantity){
		$error=$error * 0;	
	}else{
	$cartunique=$timer."_".$cart->option."_".$cart->product;
    $cartproduct=PRODUCTSS::getAttr("id",$cart->product,"INT",12);
    $cartoption=OPTIONS::getAttr("id",$cart->option,"INT",12);
	$pricee=$cartproduct->price * $cart->quantity;
        
    $cartx["_transproductid"] = $cartunique;
    $cartx["transactionid"] = $timer;
    $cartx["productid"] = $cartproduct->_productid;
    $cartx["optionid"] = $cartoption->_optionid;
    $cartx["price"] = $pricee;
    $cartx["quantity"] = $cart->quantity;
            
    $newCartData[":_transproductid"] = array("value"=>$cartunique);
    $newCartx = CARTTRANSACTIONS::setUnique($newCartData);
    $newCartItem = new CARTTRANSACTION($newCartx->id);
    $newCartItem->data = $cartx;
    $newCartItem->commitAll();
	}
	}
    
    
if($error==0){	
	TRANSACTIONSS::deleteAttr("transactionid",$timer);
	CARTTRANSACTIONS::deleteAttr("transactionid",$timer);
	echo"Notice, there's a problem with one or more product quantity ordered";
    }else{
    echo $timer;
    $upcarts=CARTTRANSACTIONS::getAllAttr("transactionid",$timer,"INT",12);
    $upcarts=objectify($upcarts);
    foreach($upcarts as $upcart){
        $optionx=OPTIONS::getAttr("_optionid",$upcart->optionid,"INT",10);
        //var_dump($optionx);
        $newval=($optionx->quantity * 1) - ($upcart->quantity * 1);
        
        $newoption=new OPTION($optionx->id);
        $newoption->data['quantity'] =$newval;
        $newoption->commitAll();
    }
    }
}


function cprint(){
	$output = shell_exec('/path/to/your/program');
	echo $output;    
}
?>