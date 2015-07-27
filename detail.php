<!DOCTYPE HTML><html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="content-type" content="text/html" />
    <meta name="author" content="Tuesday 2014-10-16" />
    <title>财生金</title>

<!--style type="text/css">
    #content table{ width: 600px;}
    #content a{color: white;}
</style -->
</head>

<body>


<?php
date_default_timezone_set('asia/shanghai');
list($s1, $s2) = explode(' ', microtime());
echo "CaiShengJin detail start time:". date("Y-m-d H:i:s") . ' ' . $s1 . '</br>';

if (!isset($rootDir)) $rootDir = './';
require_once($rootDir . "config.php"); //配置
require_once($rootDir . "class/Log.php"); //Log类
require_once($rootDir . "class/ProductInfo.php");
require_once($rootDir . "common/util.php"); //工具类
require_once($rootDir . "lib/mysql.class.php"); //数据类
require_once($rootDir . "lib/func.class.php"); //核心类
require_once($rootDir . "prepareData.php");
require_once($rootDir . "processData.php");

Log::debug("CaiShengJin detail start time:". date("Y-m-d H:i:s") . ' ' . $s1);
Log::log_echo(date("Y-m-d H:i:s") . ' ' . $s1);

//$productCode = $_GET["code"];
//$productDate = $_GET["date"];
$id = $_GET["id"];

$sql_query = "SELECT * FROM `product_info` WHERE _id='$id'";
$query_result = $db->query($sql_query);
$arr = $db->fetchAll();
$arr_count = count($arr, COUNT_NORMAL);
Log::verbose('ProductInfo getInstance $arr_count length is ' . $arr_count);
if ($arr_count != 0) {
    $productCode = $arr[0]['code'];
    $mProductInfo = new ProductInfo($arr[0]['code'], $arr[0]['name'], $arr[0]['cost'], $arr[0]['date']);
    $mProductInfo->getProductInfo($db);
    $mProductInfo->showProductInfo();
}
Log::debug('detail page product code is ' . $productCode);

//if (!inSync($productCode)) {
//    $ret1 = prepareData($productCode);
//    $ret2 = processData($productCode);
//    if (!$ret1 && !$ret2) {
//        Log::debug('Table data is already up-to-date!');
//    } else {
//        Log::debug('startSync!');
//        startSyncData($cfg["svhost"], $cfg["svport"], $productCode);
//    }
//}

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
                    $sql_query = "SELECT * FROM `product_detail` WHERE code='$productCode'
                            ORDER BY `product_detail`.`date` DESC limit 0,10";
                    $query_result = $db->query($sql_query);
                    $arr = $db->fetchAll();
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

<!-- div style="width: 600px; margin: 0 auto;" id="content">
    <img src="../xampp.gif" /><br />
    <div style="background-color:#73749A; color: white; line-height: 40px; height: 40px;  padding-left: 3px; margin-bottom: 8px;">
<a style="background-color: #73749A;" href="./phpmyadmin">phpmyadmin 账号密码: root/root </a>
    </div>
</div -->

<?php
Log::log_echo('</br>');

//显示图片
$weekYieldRateImage = $ImageDir . $productCode . "-weekYieldRate.png";
$monthYieldRateImage = $ImageDir . $productCode . "-monthYieldRate.png";
$quarterYieldRateImage = $ImageDir . $productCode . "-quarterYieldRate.png";
$yearYieldRateImage = $ImageDir . $productCode . "-yearYieldRate.png";
echo '<img src="' . $weekYieldRateImage . '"/>';
echo '<img src="' . $monthYieldRateImage . '"/>';
echo '<img src="' . $quarterYieldRateImage . '"/>';
echo '<img src="' . $yearYieldRateImage . '"/>';
//$img = imagecreatefrompng($weekYieldRate);
//header('Content-Type:image/png;');
//imagepng($img);

list($s1, $s2) = explode(' ', microtime());
Log::debug("CaiShengJin detail end time:". date("Y-m-d H:i:s") . ' ' . $s1);
?>

</body>
</html>
