<!DOCTYPE HTML><html>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="content-type" content="text/html" />
	<meta name="author" content="Tuesday 2014-10-16" />
	<title>CaiShengJin</title>

<style type="text/css">
    #content table{ width: 600px;}
    #content a{color: white;}
</style>
</head>

<body>

<?php
//session_start();
//error_reporting(0);
//set_time_limit(200);
if (!isset($rootDir)) $rootDir = './';
require_once($rootDir . "config.php"); //配置
require_once($rootDir . "lib/mysql.class.php"); //数据类
require_once($rootDir . "lib/func.class.php"); //核心类
require_once($rootDir . "ProductInfo.php");
//require_once('csj.php');
?>

<?php
require_once($rootDir . "prepare.php");
?>

<center>
    <!-- form name="myForm" action="processShowData.php" -->
        <table  border="1" cellspacing="0" cellpadding="0">
            <caption>个人理财产品 -- 产品净值 </caption>
                <th valign="middle" style="width: 10%;">产品代码</th>
                <th valign="middle" style="width: 30%;">产品名称</th>
                <th valign="middle" style="width: 20%;">产品净值</th>
                <th valign="middle" style="width: 10%;">净值日期</th>
                <?php
                    $sql_query = "SELECT * FROM `product_detail` WHERE code='8193'";
                    $query_result = $db->query($sql_query);
                    echo '$result is ' . $query_result . '</br>';
                    $arr = $db->fetchAll();
                    $arr_count = count($arr, COUNT_NORMAL);
                    echo '$arr_count length is ' . $arr_count . '</br>';
                    foreach ($arr as $a) {
                        $code = $a['code'];
                        $name = $a['name'];
                        $date = date("Ymd", strtotime($a['date']));
                        $worth = floatval($a['net_worth']);
                ?>
                <tr align="center">
                    <td><?php echo $a['code'] ?></td>
                    <td><?php echo $a['name'] ?></td>
                    <td><?php echo $a['net_worth'] ?></td>
                    <td><?php echo $a['date'] ?></td>
                </tr>
                <?php
                    }
                ?>
        </table>
    <!-- /form -->
</center>


<div style="width: 600px; margin: 0 auto;" id="content">
    <img src="../xampp.gif" /><br />
    <div style="background-color:#73749A; color: white; line-height: 40px; height: 40px;  padding-left: 3px; margin-bottom: 8px;">
<a style="background-color: #73749A;" href="./phpmyadmin">phpmyadmin 账号密码: root/root </a>
    </div>
</div>
    <?php
        //phpinfo();
        $str='hello 你好'.'</br>';
        echo $str;
        
        $mProductInfo = ProductInfo::getInstance($db);
        $mProductInfo->getProductInfo($db);
        $mProductInfo->showProductInfo();
        $mProductInfo = ProductInfo::getInstance($db);
        $mProductInfo->setCurrentDate(date("Y-m-d", strtotime("20150618")));
        $mProductInfo->getProductInfo($db);
        $mProductInfo->showProductInfo();
    ?>
</body>
</html>
