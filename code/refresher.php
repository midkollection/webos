<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>POS</title>
<link rel="stylesheet" type="text/css" href="../css/awesome/css/font-awesome.min.css">
<script src="../jQueryAssets/jquery-1.8.3.min.js"></script>
<script src="../js/fx.js"></script>
<script>
    var i = setInterval(function(){
    $("#alert").attr("class","fa fa-refresh fa-spin fa-stack-1x");
        $("#alertbox").css("color","#fff");
    //$("#alert").attr("class","fa fa-eye-slash");
    flashlong('box');
        datapull();
        datapush();
    }, 300000);  
</script>
<style>
    body,html{background:none;padding:0;margin:0;width:100%;}
    .playicon{width:20px;height:20px;padding:5px;overflow: hidden;color:#fff; text-align: center;display: none;}
    
</style>
</head>
    <body>
    <div class="playicon" id="box"><span class="fa-stack fa-lg" style="font-size:10px;">
        <i class="fa fa-cloud fa-stack-2x" id="alertbox" ></i>
        <i class="fa fa-refresh fa-spin fa-stack-1x" id="alert" style="color:#333;" ></i>
        </span></div>
    </body>
</html>