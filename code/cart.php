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

class OPTS extends OPTIONS
{
    public static function product_sizes($val)
    {
        global $database;
        $database->query("SELECT * FROM option WHERE productid='$val' AND quantity > 0 GROUP BY name ORDER BY name");
        return $database->fetchArray();
    }
}

if($_POST['function']=="cart"){
	$product=PRODUCTSS::getAttr("_productid",$_POST['product'],"INT",12);
	$option=OPTIONS::getAttr("_optionid",$_POST['option'],"INT",12);
	setlocale(LC_MONETARY,"en_NG");
	$price=$product->price * $_POST['quantity'];
	if($option->quantity < $_POST['quantity']){
		echo 300;
	}else{
	echo'<tr id="'.$product->_productid.'_'.$option->_optionid.'" valign="top" onclick="editcart('.$product->_productid.',\''.$product->_productid.'_'.$option->_optionid.'\');"><td><span>'.strtolower(String::string_short($product->name,20)).'</span><br>Size: '.$option->name.'<input type="hidden" class="mycartoption" id="cartoption_'.$product->_productid.'_'.$option->_optionid.'" value="'.$option->_optionid.'"  name="option[]"  /></td><td><span class="myquantity" total="'.$option->quantity.'">'.$_POST['quantity'].'</span><input type="hidden" class="cartquantity" id="cartquantity_'.$product->_productid.'_'.$option->_optionid.'" value="'.$_POST['quantity'].'"  name="quantity['.$option->_optionid.']"  /></td><td>'.$product->price.'<input type="hidden" class="cartunitprice" id="cartunitprice_'.$product->_productid.'_'.$option->_optionid.'" value="'.$product->price.'" /></td><td align="right"><strong class="cartpricetext">N'.number_format($price,2,".",",").'</strong><input type="hidden" class="cartprice" id="cartprice_'.$product->_productid.'_'.$option->_optionid.'" value="'.$price.'" /></td></tr>';
	}
}
if($_POST['function']=="editcart"){
	$product=PRODUCTSS::getAttr("_productid",$_POST['product'],"INT",12);
	$option=OPTIONS::getAttr("_optionid",$_POST['option'],"INT",12);
	setlocale(LC_MONETARY,"en_NG");
	$price=$product->price * $_POST['quantity'];
	if($option->quantity < $_POST['quantity']){
		echo 300;
	}else{
	echo'<td><span>'.strtolower(String::string_short($product->name,20)).'</span><br>Size: '.$option->name.'<input type="hidden" class="mycartoption" id="cartoption_'.$product->_productid.'_'.$option->_optionid.'" value="'.$option->_optionid.'"  name="option[]"  /></td><td><span class="myquantity" total="'.$option->quantity.'">'.$_POST['quantity'].'</span><input type="hidden" class="cartquantity" id="cartquantity_'.$product->_productid.'_'.$option->_optionid.'" name="quantity['.$option->_optionid.']" value="'.$_POST['quantity'].'" /></td><td>'.$product->price.'<input type="hidden" class="cartunitprice" id="cartunitprice_'.$product->_productid.'_'.$option->_optionid.'" value="'.$product->price.'" /></td><td align="right"><strong class="cartpricetext">N'.number_format($price,2,".",",").'</strong><input type="hidden" class="cartprice" id="cartprice_'.$product->_productid.'_'.$option->_optionid.'" value="'.$price.'" /></td>';
	}
}

if($_POST['function']=="pick"){
	$product=PRODUCTSS::getAttr("_productid",$_POST['product'],"INT",12);
	$options=OPTS::product_sizes($_POST['product']);
    $options=objectify($options);
	if(count($options)>1){
	$select='<option value="0">Select Size</option>';
	}
    if(count($options)<=0){
     $select='<option value="0">Out of Stock</option>';   
    }
	foreach($options as $option){
		$select.='<option value="'.$option->_optionid.'">'.$option->name.' ('.$option->quantity.')</option>';
	}
	//print_r($options);
	$js_array = json_encode($options);	
	echo'<script>
	var sizes = '.$js_array.';
	//console.log(sizes);
	</script>
	<h3>'.$product->name.'</h3>
	<div style="padding:10px;">
	<div class="miniprod fl" style="background-image:url(../photos/'.$product->photo.')"></div>
	<div class="miniprod_form fr">
	<div class="selecter"><select name="option" id="myselect" onChange="checksize()">'.$select.'</select></div>
	<input type="hidden" name="function" value="cart" />
	<div class="counter">
        <input type="hidden" name="product" id="prod_id" value="'.$product->_productid.'">
        <div class="minus" onclick="funcminus();">-</div><input type="text" name="quantity" id="prod_quantity" class="valu" value="1" onchange="checksize()"><div class="plus" onclick="funcplus();">+</div>
        </div>
	</div>
	</div>
	';
}

if($_POST['function']=="edit"){
	$product=PRODUCTSS::getAttr("_productid",$_POST['product'],"INT",12);
	$optionx=$_POST['option'];
	$quantity=$_POST['quantity'];
	$options=OPTS::product_sizes($_POST['product']);
    $options=objectify($options);
	if(count($options)>1){
	$select='<option value="0">Select Size</option>';
	}
	foreach($options as $option){
		if($option->_optionid==$optionx){ $current='selected="selected"'; }else{ $current='';}
		$select.='<option value="'.$option->_optionid.'" '.$current.'>'.$option->name.' ('.$option->quantity.')</option>';
	}
	//print_r($options);
	$js_array = json_encode($options);	
	echo'<script>
	var sizes = '.$js_array.';
	//console.log(sizes);
	</script>
	<h3>'.$product->name.'</h3>
	<div style="padding:10px;">
	<div class="miniprod fl" style="background-image:url(../photos/'.$product->photo.')"></div>
	<div class="miniprod_form fr">
	<div class="selecter"><select name="option" id="myselect" onChange="checksize()">'.$select.'</select></div>
	<input type="hidden" name="function" value="editcart" />
	<div class="counter">
        <input type="hidden" name="product" id="prod_id" value="'.$product->_productid.'">
        <div class="minus" onclick="funcminus();">-</div><input type="text" name="quantity" id="prod_quantity" class="valu" value="'.$quantity.'" onchange="checksize()"><div class="plus" onclick="funcplus();">+</div>
        </div>
	</div>
	</div>
	';
}

?>

