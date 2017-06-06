<?php session_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('max_execution_time', 0);
include("class.php");
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
include("config.php");
$id=$_GET['id'];
$user=USERSS::getAttr("userid",$_SESSION['moderator_id'],"INT",10);
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>RECEIPT: <?php echo $id ?></title>
<script src="../app/jQueryAssets/jquery-1.8.3.min.js"></script>
<script>
   // window.print();
    //setTimeout(function () { window.close(); }, 1000);
	//setTimeout(function () {$('#myprinter', window.parent.document).remove(); }, 1000);
</script>
<style>
q {quotes: none}

q:before, q:after {content: ""}   

header h1, header nav, footer, img {display: none}

body {

	font: 10pt Trebuchet,sans-serif;

	line-height: 1.5;

	color: #555555;

}
strong{color:#000;}

h1 {font-size: 18pt;color:#000;}

h2 {font-size: 14pt; margin-top: 25px}

aside h2 {font-size: 18pt}

header .print {display: block}

img {border: 0}

header {margin-bottom: 40px}

header:after {display: none;}


blockquote {

	font-size: 13pt;

	font-style: italic;

}


p a {color: #444;}
p a:after {

	content: " (http://www.midkollections.com/" attr(href) ")";

	font-size: 80%;

	word-wrap: break-word;

}

p a[href^="http://"]:after, p a[href^="https://"]:after {

	content: " (" attr(href) ")";

}

q:after {content: " (" attr(cite) ")"}

aside {

	border-top: 1px solid #bbb;

	margin-top: 30px;

	display: block;

	page-break-before: always; 

}
    img{
        display:block !important;
    }
    hr{margin:15px 0;}
    .cart{width:100%; border-collapse: collapse;}
    .cart .headtable{border-bottom: 1px solid #ccc !important;}
    .cart td,.cart th{padding:8px 0;}
    .cart td{font-size:11px;font-family:monospace;}
    .discounttable{border-top:1px solid #888;background-color:#eee !important;}
    .totaltable{border-top:1px solid #555;border-bottom:2px solid #555;}
</style>
</head>

<body>

<h1>RECEIPT</h1>
<table width="100%">
<tr><td>
<strong>MIDKOLLECTIONS LIMITED</strong><br>

<small>Suite GF3, A.G.A Memorial Plaza,<br>
Area 11, Abuja.<br>
+234 (0)802 332 9239, +234 (0)803 111 3598<br>
sales@midkollections.com<br> </small>   
    </td><td></td><td align="right" valign="top"><img src="../photos/logo.png" height="40" /></td></tr>
</table>
    <hr />

    <table class="cart">
    <tr class="headtable"><th align="left" width="50">S/N</th><th align="left">ITEM</th><th align="left">VARIANT</th><th  align="right">COST</th><th align="center">QUANTITY</th><th  align="right">PRICE</th></tr>
     <?php
        $transaction=TRANSACTIONSS::getAttr("_transactionid",$id,"INT",12);
        echo "<pre>";
        $i=1;
        //print_r($transaction);
        //print_r(CARTTRANSACTION::find_attr("transactionid",$transaction->id));
        $carts=CARTTRANSACTIONS::getAllAttr("transactionid",$id,"INT",12);
        $carts=objectify($carts);
        foreach($carts as $cart){
            $product=PRODUCTSS::getAttr("_productid",$cart->productid,"INT",12);
            $option=OPTIONS::getAttr("_optionid",$cart->optionid,"INT",12);
            
            echo'<tr><td align="left">'.$i.'</td><td align="left">'.strtolower($product->name).'</td><td align="left">'.strtolower($option->name).'</td><td  align="right">'.$product->price.'</td><td align="center">'.$cart->quantity.'</td><td  align="right">'.number_format($cart->price,2,'.',',').'</td></tr>';
            $i++;
        }
     ?>   
    
        
    <tr class="discounttable"><td align="left"></td><td align="left">SUBTOTAL</td><td align="left"></td><td  align="right"></td><td align="center"></td><td  align="right"><?php echo number_format($transaction->price,2,'.',','); ?></td></tr>
    <tr class="discounttable"><td align="left"></td><td align="left">DISCOUNT</td><td align="left"></td><td  align="right"><?php echo $transaction->discount; ?></td><td align="center"></td><td  align="right"><?php echo number_format(($transaction->price * 1) - ($transaction->totalprice * 1),2,'.',',');; ?></td></tr>
    <tr class="totaltable"><td align="left"></td><td align="left"><strong>TOTAL</strong></td><td align="left"></td><td  align="right"></td><td align="center"></td><td  align="right"><strong><?php echo number_format($transaction->totalprice,2,'.',','); ?></strong></td></tr>
    </table>


<p><small>
<strong>Transaction Number:</strong> <?php echo $transaction->_transactionid; ?><br />
<strong>Date:</strong> <?php echo date("D, d M Y",$transaction->_transactionid); ?><br />

</small></p>
    
<hr />
    

</body>
</html>