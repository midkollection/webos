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

if($_REQUEST['function']=="optioncheck"){
	$option=OPTIONS::getAttr("_optionid",$_REQUEST['option'],"INT",12);
	echo $option->quantity;
}
?>

