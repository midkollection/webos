<?php
include("class.php");
require_once("../model/class.php");
include("../model/option.php");
include("../model/category.php");
include("../model/products.php");
include("../model/options.php");
include("../model/categorys.php");
include("../model/productss.php");
include("config.php");

if(isset($_POST['option'])){
	$options=new ArrayObject;
	$options=$_POST['option'];
	$quantities=new ArrayObject;
	$quantities=$_POST['quantity'];
	//echo "<pre>";
	//print_r($options);
	$error=200;
	$total=0;
	$cart=new ArrayObject();
	
	foreach($options as $option){
		$quantity=$quantities[$option];
		$realoption=OPTIONS::getAttr("_optionid",$option,"INT",12);
		$realproduct=PRODUCTSS::getAttr("_productid",$realoption->productid,"INT",12);
		$price=$quantity * $realproduct->price;
		$total=$price+$total;
		$cart=array(
		"product"=>$realproduct->id,
		"option"=>$realoption->id,
		"quantity"=>$quantity
		);
		
		$carts[]=$cart;
		unset($cart);
		
		if($quantity > $realoption->quantity){
		  echo "Notice! Sorry the quantity requested for the product <strong>[".$realproduct->name."]</strong> option <strong>[".$realoption->name."]</strong> is not available!<br>";
		  //exit();	
		  $error=$error*0;
		}
	}
	
	if($error==200){
	echo'<div class="checkouttop"><strong>TOTAL AMOUNT: </strong><span id="pay_text_total">'.$total.'
		<form action="../code/transact.php" method="post" id="transact_form">
		<input type="hidden" name="function" value="commit" />
        <input type="hidden" name="total" value="'.$total.'" class="checkoutrealprice" id="pay_total" />
        <input type="hidden" name="type" value="" class="checkoutpaytype" id="pay_type_val" />
        <input type="hidden" name="discount" value="" class="checkoutpaydiscount" id="pay_type_discount" />
		<input type="hidden" name="cart" value=\''.urlencode(json_encode($carts)).'\' class="" id="pay_cart" />
		</form>
        </span></div>
      <div class="checkoutpay"> <strong>PAYABLE:</strong> <span class="checkoutprice">'.$total.'</span> </div>';	
	}
}

?>

