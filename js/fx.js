 setInterval(function (){
        $(".honey").removeClass("animated jello");
        setTimeout(function (){
        $(".honey").addClass("animated jello");
        },2000);
},8000);
function xlaunch(){
    setTimeout(function (){
        $(".talk").fadeIn(200).removeClass("animated bounceInLeft");
        $(".talk").fadeOut(200,function(){
            $(".talk span").html("creating database");
            $(".talk").fadeIn(200).addClass("animated bounceInLeft");
        }); 
    },5000);
}
function firstlaunch(){
    var dataString="function=firsttime";
	$.ajax({  
		type: "POST",  
		url: "code/startup.php", 
		data: dataString,  
		timeout: 90000,
		success: function( response, status) { 
		if(status=="success"){
            if(response==200){
                $(".talk").fadeIn(300).addClass("animated bounceInLeft");
                launch(); 
            }else{
               $(".newsetup").show(100).addClass("animated fadeInUp");
               $("#setup").fadeIn(20).addClass("animated fadeInUp");
            }
		  }}
    });
}
function launch(){
    setTimeout(function (){
    $(".talk").fadeIn(200).removeClass("animated bounceInLeft");        
    var dataString="function=exist";
	$.ajax({  
		type: "POST",  
		url: "code/startup.php", 
		data: dataString,  
		timeout: 90000,
		success: function( response, status) { 
		if(status=="success"){
			//console.log(response);
            if(response==200){
            showinfo("Database exists","fa-check color_green"); 
            setTimeout(function (){
            showinfo("Checking network","fa-spin fa-spinner color_white");
            connection(outstandingUpload);
            },2000);
            }else{
            showinfo("Creating database","fa-spin fa-spinner color_white");
            createdb();
            }
        
		  }
        }
	});
            
   
    },2000);
}
function createdb(){
    var dataString="function=createdatabase";
	$.ajax({  
		type: "POST",  
		url: "code/startup.php", 
		data: dataString,  
		timeout: 90000,
		success: function( response, status) { 
		if(status=="success"){
			//console.log(response);
            if(response==200){
            setTimeout(function (){
            showinfo("Database created","fa-check color_green");
            setTimeout(function (){
            showinfo("Checking network","fa-spin fa-spinner color_white");
            connection(downloadProducts);
            },2000);
             },2000);
            }else{
            
            }
		  }
        }
	});
}

function connection(func){
    var dataString="function=connection";
    var noaction=setTimeout(function (){
    showinfo("No internet connection","fa-wifi color_red");
    $("#proceed").fadeIn(100).addClass("animated fadeInUp");
    },15000);
    
	$.ajax({  
		type: "POST",  
		url: "code/startup.php", 
		data: dataString,  
		timeout: 13000,
		success: function( response, status) { 
        console.log(status);
		if(status=="success"){
        clearTimeout(noaction);
        if(response==200){
            showinfo("Connection established","fa-wifi color_green");
            setTimeout(function (){
            func();
             },3000);
		  }
            else{
            setTimeout(function (){
            showinfo("No network connection","fa-wifi color_red");
             $("#proceed").fadeIn(100).addClass("animated fadeInUp");
            //func;
             },3000);
            }
        } 
        }
	});
        
}
function downloadProducts(){
    showinfo("Downloading products","fa-spin fa-spinner color_white");
    startLoadbar();
    var dataString="function=checkdownload";
    $.ajax({  
		type: "POST",  
		url: "code/startup.php", 
		data: dataString,  
		timeout: 90000,
		success: function( response, status) { 
        console.log(status);
		if(status=="success"){
        if(response==200){
            endLoadbar("bar");
            setTimeout(function (){  
            loadData();
            },2000);
        }
        else{
            setTimeout(function (){  
            downloadTransactions();
            },2000); 
        }
        } 
        }
	});
}

function downloadTransactions(){
    showinfo("Downloading past transactions","fa-spin fa-spinner color_white");
    startLoadbar();
    var dataString="function=checktransaction";
    $.ajax({  
		type: "POST",  
		url: "code/startup.php", 
		data: dataString,  
		timeout: 90000,
		success: function( response, status) { 
        console.log(status);
		if(status=="success"){
        if(response==200){
            endLoadbar("bar");
            setTimeout(function (){  
            loadTranData();
            },2000);
        }
        else{
            hideLoadbar();
            setTimeout(function (){
            showinfo("Voila! you are ready to start","fa-check color_green");
            $("#proceed").fadeIn(100).addClass("animated fadeInUp");
            $("#rewrite").fadeIn(100).addClass("animated fadeInUp");
            $("#rewriteinfo").fadeIn(100).addClass("animated fadeInUp");
            },2000);    
        }
        } 
        }
	});
}
function runUpload(){
    showinfo("Uploading transactions","fa-spin fa-spinner color_white");
    startLoadbar();
    var dataString="function=uploadtransactions";
    $.ajax({  
		type: "POST",  
		url: "code/startup.php", 
		data: dataString,  
		timeout: 900000,
		success: function( response, status) { 
        console.log(status);
		if(status=="success"){
        if(response==200){ 
            endLoadbar();
            setTimeout(function (){  
            downloadProducts();
            },2000);
        }
        else{
            hideLoadbar();
            setTimeout(function (){  
            showinfo("Voila! you are ready to start","fa-check color_green");
            $("#proceed").fadeIn(100).addClass("animated fadeInUp");
            $("#rewrite").fadeIn(100).addClass("animated fadeInUp");
            $("#rewriteinfo").fadeIn(100).addClass("animated fadeInUp");
            },2000);   
        }
        } 
        }
	}); 
}
function loadData(){
    showinfo("Loading product details","fa-spin fa-spinner color_white");
    startLoadbar();
    var dataString="function=loadProducts";
    $.ajax({  
		type: "POST",  
		url: "code/startup.php", 
		data: dataString,  
		timeout: 900000,
		success: function( response, status) { 
        console.log(status);
		if(status=="success"){
        if(response==200){ 
            endLoadbar();
            setTimeout(function (){  
            downloadTransactions();
            },2000);
        }
        else{
            hideLoadbar();
            setTimeout(function (){  
            downloadTransactions();
            },2000);   
        }
        } 
        }
	}); 
}
function loadTranData(){
    showinfo("Loading transaction records","fa-spin fa-spinner color_white");
    startLoadbar();
    var dataString="function=loadTransactions";
    $.ajax({  
		type: "POST",  
		url: "code/startup.php", 
		data: dataString,  
		timeout: 900000,
		success: function( response, status) { 
        console.log(status);
		if(status=="success"){
        if(response==200){ 
            endLoadbar();
            setTimeout(function (){
            showinfo("Voila! you are ready to start","fa-check color_green");
            $("#proceed").fadeIn(100).addClass("animated fadeInUp");
            $("#rewrite").fadeIn(100).addClass("animated fadeInUp");
            $("#rewriteinfo").fadeIn(100).addClass("animated fadeInUp");
            },2000);
        }
        else{
            hideLoadbar();
            setTimeout(function (){
            showinfo("Sorry! something went wrong, rewrite DB");
            $("#rewrite").fadeIn(100).addClass("animated fadeInUp");
            $("#rewriteinfo").fadeIn(100).addClass("animated fadeInUp");
            },2000);    
        }
        } 
        }
	}); 
}
function startLoadbar(){
    $("#bar").html("<div class='loadbar'><div class='movebar'></div></div>");
    $("#bar .movebar").animate({
        width: "50%"
    },4000);
}
function endLoadbar(){
    $("#bar .movebar").stop();
    $("#bar .movebar").animate({
        width: "100%"
},1000);
    setTimeout(function (){
        $("#bar .loadbar").remove();
    },1000);
}
function hideLoadbar(){
    $("#bar .loadbar").hide(300).remove();
}
function outstandingUpload(){
    showinfo("Checking for outstanding uploads","fa-spin fa-spinner color_white");
    var dataString="function=checkupload";
    $.ajax({  
		type: "POST",  
		url: "code/startup.php", 
		data: dataString,  
		timeout: 9000,
		success: function( response, status) { 
        console.log(status);
		if(status=="success"){
        if(response==200){
            setTimeout(function (){
                runUpload();
            },3000); 
        }
        else{
            setTimeout(function (){
            downloadProducts();
            },3000);    
        }
        } 
        }
	});       
}
function showinfo(info,icon){
    $(".talk").fadeOut(200,function(){
        $(".talk i").attr("class","fa "+icon);
        $(".talk span").html(info);
        $(".talk").fadeIn(200).addClass("animated bounceInLeft");
    });
}
function hideinfo(){
    $(".talk").fadeOut(100);
}
function rewriteDB(){
    var pass= prompt("Are you sure you need to take this action");
    console.log(pass);
    if(pass.length >= 5){
        showinfo("Purging Data","fa-spin fa-spinner color_white");
        $.ajax({  
		type: "POST",  
		url: "code/startup.php", 
		data:'function=dbrewrite&data='+pass,  
		timeout: 9000,
		success: function( response, status) { 
        console.log(status);
		if(status=="success"){
        if(response==200){
            setTimeout(function (){
            showinfo("Data Purge Complete","fa-check color_green");
            $("#proceed").fadeOut(100);
            $("#rewrite").fadeOut(100).remove();
            $("#rewriteinfo").fadeOut(100).remove();
            launch();
            },2000);
        }
        else{
            hideinfo();
             alert("Sorry, You don't have the right permission to do that."); 
        }
        } 
        }
	   });
    }
    else{
        alert("Sorry, You don't have the right permission to do that.");
    }
}
function Setup(){
    var error = false;
    if($(".mainurl").val()==""){
       $(".mainurl").focus();
       $(".mainurl").parent().css("background","rgba(255,255,255,0.05)");
       error=true;
    }
    else{
        $(".otherurl  input").each(function(){
            if($(this).val()==""){
                //console.log("field can't be blank");
                $(this).focus();
                $(this).parent().css("background","rgba(255,255,255,0.05)");
                $(this).addClass("animated rubberBand");
                error=true;
            }        
        });
    }
    if(!error){
    $(".newsetup").slideUp(500,function(){
        showinfo("Creating config file","fa-spin fa-spinner color_white");
        $.ajax({  
		type: "POST",  
		url: "code/startup.php", 
		data: $("#writeToconfig").serialize(),  
		timeout: 9000,
		success: function( response, status) { 
        console.log(status);
		if(status=="success"){
        if(response==200){
            setTimeout(function (){
            showinfo("Configuration Complete","fa-check color_green");
                launch();
            },3000);
        }
        else{
             setTimeout(function (){
              hideinfo();
              $(".newsetup").slideDown(500);
                 
            },3000);  
        }
        } 
        }
	});
    });
    }
}