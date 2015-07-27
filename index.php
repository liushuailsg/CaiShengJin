<!DOCTYPE HTML><html>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="content-type" content="text/html" />
	<meta name="author" content="Tuesday 2014-10-16" />
	<title>财生金</title>

<!--html5 speedup-->
<link rel="dns-prefetch" href="//lib.sinaapp.com">
<link rel="dns-prefetch" href="//libs.baidu.com">
<link rel="dns-prefetch" href="//cdnjs.bootcss.com">

<!-- link rel="prefetch" href="syncData.php" / -->
<!-- link rel="prerender" href="syncData.php" / -->


<link href="http://lib.sinaapp.com/js/bootstrap/2.3.1/css/bootstrap.min.css" rel="stylesheet">
<style>
::-webkit-scrollbar{width:8px}
::-webkit-scrollbar-track-piece{background-color:#d2d2d2}
::-webkit-scrollbar-thumb{background:#888;}
::-webkit-scrollbar-thumb:hover{background-color:#999;}
#grid {
position: fixed;
top: 0;
left: 0;
width: 100%;
height: 100%;
z-index: -11;
margin: 0;
padding: 0;
overflow: hidden;
#background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAYAAABytg0kAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAABdJREFUeNpi/P//PwMYgBjGxsb/AQIMAEepB5QXCK53AAAAAElFTkSuQmCC) repeat;
}
</style>
</head>

<body>

<?php
date_default_timezone_set('asia/shanghai');
list($s1, $s2) = explode(' ', microtime());
echo "CaiShengJin index start time:". date("Y-m-d H:i:s") . ' ' . $s1 . '</br>';

if (!isset($rootDir)) $rootDir = './';
require_once($rootDir . "config.php"); //配置
require_once($rootDir . "class/Log.php"); //Log类
require_once($rootDir . "class/ProductInfo.php");
require_once($rootDir . "common/util.php"); //工具类
require_once($rootDir . "lib/mysql.class.php"); //数据类
require_once($rootDir . "lib/func.class.php"); //核心类
require_once($rootDir . "prepareData.php");
require_once($rootDir . "processData.php");
//require_once($rootDir . "syncData.php");

set_time_limit($time_limit);
//error_reporting(0);

?>

<?php
list($s1, $s2) = explode(' ', microtime());
Log::debug("CaiShengJin index start time:". date("Y-m-d H:i:s") . ' ' . $s1);
//Log::log_echo(date("Y-m-d H:i:s") . ' ' . $s1);

?>

<!-- center>
        <table  border="1" cellspacing="0" cellpadding="0">
            <caption>个人理财产品</caption>
                <th valign="middle" style="width: 10%;">产品代码</th>
                <th valign="middle" style="width: 30%;">产品名称</th>
                <th valign="middle" style="width: 20%;">购买本金</th>
                <th valign="middle" style="width: 10%;">购买日期</th>
                <?php
                    $sql_query = "SELECT * FROM `product_info`";
                    $query_result = $db->query($sql_query);
                    $arr = $db->fetchAll();
                    foreach ($arr as $a) {
                        $code = $a['code'];
                        $name = $a['name'];
                        $cost = floatval($a['cost']);
                        $date = date("Y-m-d", strtotime($a['date']));
                ?>
                <tr align="center">
                    <td><?php echo $code ?></td>
                    <td><?php echo $name ?></td>
                    <td><?php echo $cost ?></td>
                    <td><?php echo $date ?></td>
                </tr>
                <?php
                    }
                ?>
        </table>
</center -->

<div id="grid"></div>
<div class="container">
    <div class="row">
        <div class="span12">
            <div class="page-header" align="center">
                <h3>个人理财产品 </h3>
            </div>
            <table class="table table-hover" border="1" cellspacing="0" cellpadding="0">
                <!-- caption>个人理财产品</caption -->
                    <th valign="middle" style="width: 10%;">产品代码</th>
                    <th valign="middle" style="width: 20%;">产品名称</th>
                    <th valign="middle" style="width: 10%;">购买本金</th>
                    <th valign="middle" style="width: 10%;">购买日期</th>
                    <th valign="middle" style="width: 10%;">操作</th>
                    <?php
                        $color=array('success','error','warning','info');
                        $col=$color[0];
                        $sql_query = "SELECT * FROM `product_info`";
                        $query_result = $db->query($sql_query);
                        $arr = $db->fetchAll();
                        foreach ($arr as $a) {
                            $code = $a['code'];
                            $name = $a['name'];
                            $cost = floatval($a['cost']);
                            $date = date("Y-m-d", strtotime($a['date']));
                    ?>
                    <tr class="<?=$col?>" align="center">
                        <td><?php echo $code ?></td>
                        <td><?php echo $name ?></td>
                        <td><?php echo $cost ?></td>
                        <td><?php echo $date ?></td>
                        <td><button class="btn btn-primary btn-small" type="button" onClick="detail('<?=$a['_id']?>')">详情</button></td>
                    </tr>
                    <?php
                        }
                    ?>
            </table>
            
            <div><h5>财生金</h5>Copyright © 2015 <a href="http://www.baidu.com">Leo</a>. All rights reserved. <a href="login.php">管理</a></div>
            <div class="alert alert-success" id="msgbox" style="display:none">
                <div id="msg"></div>
            </div>
        </div>
    </div>
</div>

<script src="http://libs.baidu.com/jquery/1.9.1/jquery.min.js"></script> 
<script src="http://lib.sinaapp.com/js/bootstrap/2.3.1/js/bootstrap.min.js"></script>
<script src="http://cdnjs.bootcss.com/ajax/libs/jquery-backstretch/2.0.3/jquery.backstretch.min.js"></script>
<script>

    // Create an array of images that you'd like to use
    var images = ["http://ww3.sinaimg.cn/large/6d8e6738tw1e6k3yi3smej21ao0rs7cb.jpg",
              "http://ww2.sinaimg.cn/large/6d8e6738tw1e6k3z9jxrsj211x0lcadn.jpg",
              "http://ww2.sinaimg.cn/large/6d8e6738tw1e6k3zh9psgj211x0lcq59.jpg"];
    
    // A little script for preloading all of the images
    // It's not necessary, but generally a good idea
    $(images).each(function(){
       $('<img/>')[0].src = this; 
    });
    
    // The index variable will keep track of which image is currently showing
    var index = 0;
    
    // Call backstretch for the first time,
    // In this case, I'm settings speed of 500ms for a fadeIn effect between images.
    $.backstretch(images[index], {speed: 1000});
    
    // Set an interval that increments the index and sets the new image
    // Note: The fadeIn speed set above will be inherited
    setInterval(function() {
        index = (index >= images.length - 1) ? 0 : index + 1;
        $.backstretch(images[index]);
    }, 10000);


function detail(id) {
//    $("#msg").html('Detail page starting ,Please Wait.');
//    $("#msgbox").fadeIn();
    window.open("detail.php?id="+id);
//    xmlhttp=new XMLHttpRequest();
//    xmlhttp.open("GET", "detail.php?id="+id, true);
//    xmlhttp.send();
    
//    htmlobj=$.ajax({url:"detail.php?id="+id,async:false});
//    if(htmlobj.responseText!="error") {
//        location=htmlobj.responseText;
//        $("#msgbox").fadeOut();
//    } else {
//        $("#msg").html("error ,please try again.");
//        $("#msgbox").fadeIn();
//    }
}
</script>

<?php
ob_flush();
flush();
Log::debug("flush flush flush");

global $db;
$query_sql="select * from sync_task";
$query_result = $db->query($query_sql);
$arr = $db->fetchAll();
foreach ($arr as $a) {
    if (!$a['status']) {
        startSyncData($cfg["svhost"], $cfg["svport"], $a['code']);
        //syncData($a['code']);
    }
}

list($s1, $s2) = explode(' ', microtime());
Log::debug("CaiShengJin index end time:". date("Y-m-d H:i:s") . ' ' . $s1);
?>

</body>
</html>
