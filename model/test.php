<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("class.php");
include("users.php");
include("userss.php");

        $data["userid"] = "2";
        $data["username"] = "muyiwa";
        $data["password"] = "mistigue";
        $data["role"] = "role";
    
        //if(isset($user->userid)){
            $newData[":userid"] = array("value"=>"2");
            $newData[":username"] = array("value"=>"muyiwa");
            
         
                $new = USERSS::setUnique($newData);
                print_r($new);
           
        //}
?>