<?php
$mainu="http://midkollections.com";
//$mainu="http://localhost/mid2015";
$dataurl=$mainu."/include/jsonrequest.php";
$uploadurl=$mainu."/include/recieve.php";
$pullurl=$mainu."/include/send.php";
$userurl=$mainu."/include/users.php";
$photourl=$mainu."/pearmobi/uploads/";
$logourl=$mainu."/images/mid7.png";
$connectionfile=$mainu."/include/connect.txt";
$sqlitefile="../config/pos.sqlite";
?>
<?php
if(is_file("config/settings.php")){
    include("config/settings.php");
}
?>
<?php
function curlarray($url){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$url" );
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$result = curl_exec($ch);
curl_close($ch);
echo "([".$result."])";
}
/*Curl GET call return object*/
function curlobject($url){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$url" );
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$result = curl_exec($ch);
curl_close($ch);
return $result;
}
function curloption($url,$val){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$url$val" );
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$result = curl_exec($ch);
curl_close($ch);
echo "([".$result."])";
}
/*Curl POST call return object*/
function curlpost($url,$data){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url );
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$result = curl_exec($ch);
curl_close($ch);
return $result;
}

class String{
public static function string_decode($string=""){
   $string=stripcslashes($string);
   return rawurldecode($string); 
}
public static function string_rips($string) { 
    $string = preg_replace ('/<[^>]*>/', ' ', $string); 
    $string = str_replace("\r", '', $string);
    $string = str_replace("\n", ' ', $string);
    $string = str_replace("\t", ' ', $string);
    $string = trim(preg_replace('/ {2,}/', ' ', $string));
    return $string; 
}
public static function string_short($string="",$limit=0){
	$above=($limit*1)+3;
	//$string=String::string_decode($string);
	$string=String::string_rips($string);
	$string=stripcslashes($string);
		 if(strlen($string)>=$above){
			$string=substr($string,0,$limit);
			$string="$string...";
	}
	return $string;
}
public static function no_font($string=""){
	$string = str_replace("face=\"'Segoe UI'\"","",$string);
	$string = str_replace("font-family: 'Segoe UI';","",$string);
	$string = strip_tags($string,'<p><a><bold><li><ul><table><td><tr><th><tbody><em><i><strong><b><br><br />');
	return $string;
}
}
function objectify($data){
    return json_decode(json_encode($data), FALSE);
}
?>