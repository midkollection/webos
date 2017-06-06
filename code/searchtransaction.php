<?php
include("class.php");
include("config.php");
require_once("../model/class.php");
include("../model/transactions.php");
include("../model/transactionss.php");

    
      
class TRANSE extends TRANSACTIONSS
{
    
    public static function search($start,$end)
    {
        global $database;
        $database->query("select transactiondate,SUM(totalprice) as total from transactions where _transactionid > '$start' and _transactionid < '$end' GROUP BY transactiondate ORDER BY transactiondate DESC");
        return $database->fetchArray();
    }
    public static function getDate($date)
    {
        global $database;
        $database->query("select * from transactions where transactiondate = '$date' ORDER BY _transactionid DESC");
        return $database->fetchArray();
    }
}
?>
<table class="trandata">
    <tr><th width="10">S/N</th><th width="130">Transaction Date</th>
        <th width="130">Time</th><th>Transaction ID</th><th>Price</th><th>Discount</th><th>Amount</th><th>Channel</th><th>View</th></tr>
    <?php
    $m=$_REQUEST["month"];
    $y=$_REQUEST["year"];
    $start = strtotime("1-".$m."-".$y);
    $end = strtotime(date("t",$start)."-".$m."-".$y);
    //echo $test."-".$end." ".date("h:ia D, d M Y",$test)."-".date("h:ia D, d M Y",$end);
        $i=1;
   
    $transdates=objectify(TRANSE::search($start,$end));
    echo "<pre>";
    //var_dump($transdates);
    foreach($transdates as $transdate){
        //var_dump(TRANSE::getDate($transdate->transactiondate));
        $dday=explode(":",$transdate->transactiondate);
        echo"<tbody><tr style='background:#bbb;color:#222;border-bottom:1px solid #888;'><th colspan='8'>".date("D,d M Y",strtotime($dday[2]."-".$dday[1]."-".$dday[0]))."</th><th>".number_format($transdate->total)."</th></tr></tbody>
        <tbody id='$transdate->transactiondate' style=''>";
       $trans=objectify(TRANSE::getDate($transdate->transactiondate));
       foreach($trans as $tran){ 
        if($tran->refer != "store_app"){
            $refer="Web Sale($tran->refer)";
        }else{
            $refer="Store Sale";
        }
        echo '<tr><td>'.$i.'</td><td>'.date("d/M/Y",$tran->_transactionid).'</td><td>'.date("h:i a",$tran->_transactionid).'</td><td>'.$tran->_transactionid.'</td><td>'.$tran->price.'</td><td>'.$tran->discount.'</td><td>'.$tran->totalprice.'</td><td>'.$refer.'</td><td><a onclick="view('.$tran->_transactionid.')" class="viewbut">view</a></td></tr>';
        $i++;
    }
        echo"</tbody>";
    }      
    ?>
</table>