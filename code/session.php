<?php session_start();
include("class.php");
require_once("../model/class.php");
include("../model/users.php");
include("../model/userss.php");
include("config.php");

if($_REQUEST['function']=="auth"){
   echo authuser($_REQUEST['xname'],$_REQUEST['xpass']);
}
function authuser($name,$pass){
    $newData[":username"] = array("value"=>$name);
    $newData[":password"] = array("value"=>$pass);
    $authuser=USERSS::getFullAttr($newData);
    //print_r($authuser);
    if($authuser->id >= 1){   
        $_SESSION['active']="current";
        $_SESSION['moderator_id']=$authuser->userid;
        $_SESSION['moderator_role']=$authuser->role;
        return 200; 
    }else{   
        unset($_SESSION['active']);
        unset($_SESSION['moderator_id']);
        unset($_SESSION['moderator_role']);
        return 300;
    }
}

?>