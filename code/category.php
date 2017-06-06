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

if($_POST['function']=="category"){
	$categories=CATEGORYS::getAll();
    $categories=objectify($categories);
	foreach($categories as $category){
	echo'<li onClick="categorysearch('.$category->_categoryid.');">'.$category->name.'</li>';
	}
}

?>

