<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('max_execution_time', 0);

include("class.php");
require_once("../model/class.php");
include("../model/option.php");
include("../model/category.php");
include("../model/products.php");
include("../model/options.php");
include("../model/categorys.php");
include("../model/productss.php");
include("config.php");

//$photourl='http://midkollections.com/pearmobi/uploads/';
if($_REQUEST['function']=="sync"){	
		$prod=PRODUCTSS::getAttr("_productid",$_REQUEST['id'],"INT",12);			
			if(!is_file('../photos/'.$prod->photo)){
			$image = file_get_contents($photourl."".$prod->photo);
			file_put_contents('../photos/'.$prod->photo, $image);
            echo 200;
			}else{
                echo 200;
            }
			
}
if($_REQUEST['function']=="pull"){	
		$prod=PRODUCTSS::getAttr("_productid",$_REQUEST['id'],"INT",12);		
			unlink('../photos/'.$prod->photo);
			$image = file_get_contents($photourl."".$prod->photo);
			file_put_contents('../photos/'.$prod->photo, $image);
            echo 200;		
}

?>

