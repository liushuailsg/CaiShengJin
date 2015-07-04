<?php
if (!isset($rootDir)) $rootDir = './';
require_once($rootDir . "config.php"); //配置
require_once($rootDir . "common/util.php"); //工具类
require_once($rootDir . "lib/mysql.class.php"); //数据类
require_once($rootDir . "lib/func.class.php"); //核心类
require_once($rootDir . "ProductInfo.php");
?>

<?php
$sql_query = "SELECT * FROM `product_info` WHERE code='8193'";
$query_result = $db->query($sql_query);
$arr = $db->fetchAll();
$arr_count = count($arr, COUNT_NORMAL);
if(debug()) echo '$arr_count length is ' . $arr_count . '</br>';

if ($arr_count != 0) {
    $currentDate = $arr[0]['date'];
    $maxDate = date("Y-m-d");
    if(debug()) echo '$currentDate is ' . $currentDate . '</br>';
    if(debug()) echo '$maxDate is ' . $maxDate . '</br>';
    for ($i = 0; $currentDate < $maxDate; $i++) {
        if(debug()) echo '$currentDate is ' . date("w", strtotime($currentDate)) . '</br>';
        $mProductInfo = ProductInfo::getInstance($db);
        $mProductInfo->setCurrentDate($currentDate);
        $mProductInfo->getProductInfo($db);
        if(debug()) $mProductInfo->showProductInfo();
        
        $query_sql="select * from yield where code='$mProductInfo->code' and date='$currentDate'";
        $query_result = $db->query($query_sql);
        if ($db->recordCount()) {
            if(debug()) echo $mProductInfo->code . ' ' . $currentDate . '</br>';
        } else {
            $insert_sql = "insert into yield (code, date, yields, assets, dayYield,
                    weekYieldRate, monthYieldRate, quarterYieldRate, yearYieldRate) values
                    ('$mProductInfo->code', '$currentDate', '$mProductInfo->yields', '$mProductInfo->assets',
                    '$mProductInfo->dayYield', '$mProductInfo->weekYieldRate', '$mProductInfo->monthYieldRate',
                    '$mProductInfo->quarterYieldRate', '$mProductInfo->yearYieldRate')";
            $query_result = $db->query($insert_sql);
        }
        
        $currentDate = date("Y-m-d", strtotime("+1 day", strtotime($currentDate)));
    }
}
?>