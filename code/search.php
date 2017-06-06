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

class PRODS extends PRODUCTSS
{
    public static function search($val)
    {
        global $database;
        $database->query("SELECT * FROM products WHERE name LIKE '%$val%' OR code LIKE '%$val%'");
        return $database->fetchArray();
    }
}

class OPTS extends OPTIONS
{
    public static function product_sizes($val)
    {
        global $database;
        $database->query("SELECT * FROM option WHERE productid='$val' AND quantity > 0 GROUP BY name ORDER BY name");
        return $database->fetchArray();
    }
}
if($_POST['function']=="search"){
	$products=PRODS::search($_POST['search']);
	$products=objectify($products);
	foreach($products as $product){
	$options=OPTS::product_sizes($product->_productid);
    $options=objectify($options);
	if(count($options)<1){ 
	$disable='style="background-image:url(../photos/'.$product->photo.');opacity:0.5;cursor:not-allowed;"';
	$text='<span style="display:block;padding:10px;margin:0;overflow:hidden;text-align:center;color:#fff;font-size:63px;text-shadow:1px 1px 1px #000;font-weight:lighter;"><i class="fa fa-eye-slash"></i></span>'; 
	}else{ 
	$disable=' style="background-image:url(../photos/'.$product->photo.')" onclick="pickcart('.$product->_productid.');"';
	$text='<div class="prod_name">'.String::string_short($product->name,16).'</div>';
	}
		echo'<div class="productbox" '.$disable.'>'.$text.'</div>';
	}
}


if($_POST['function']=="category"){
	$products=PRODS::getAllAttr("categoryid",$_POST['category'],"INT",12);
    $products=objectify($products);
    //var_dump($products);
	foreach($products as $product){
	$options=OPTS::product_sizes($product->_productid);
    $options=objectify($options);
    //var_dump($options);
	if(count($options)<1){ 
	$disable='style="background-image:url(../photos/'.$product->photo.');opacity:0.5;cursor:not-allowed;"';
	$text='<span style="display:block;padding:10px;margin:0;overflow:hidden;text-align:center;color:#fff;font-size:63px;text-shadow:1px 1px 1px #000;font-weight:lighter;"><i class="fa fa-eye-slash"></i></span>'; 
	}else{ 
	$disable=' style="background-image:url(../photos/'.$product->photo.')" onclick="pickcart('.$product->_productid.');"';
	$text='<div class="prod_name">'.String::string_short($product->name,14).'</div>';
	}
		echo'<div class="productbox" '.$disable.'>'.$text.'</div>';
	}
}

?>

