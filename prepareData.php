<?php
header("Content-type: text/html; charset=utf-8");
//error_reporting(0);
set_time_limit(200);
if (!isset($rootDir)) $rootDir = './';

require_once($rootDir . "config.php"); //配置
require_once($rootDir . "common/util.php"); //工具类
require_once($rootDir . "lib/mysql.class.php"); //数据类
require_once($rootDir . "lib/func.class.php"); //核心类
require_once($rootDir . "common/ParseHtmlData.php");

function prepareData($productCode) {
    $date = date("Y-m-d");
    if (isWeekday($date)) {
        $lastWeekday = getLastWeekday($date);
    } else {
        $lastWeekday = getLastWeekday(getLastWeekday($date));
    }
    
    global $db;
    $query_sql="select * from product_detail where date='$lastWeekday' ORDER BY `product_detail`.`date` DESC ";
    $query_result = $db->query($query_sql);
    if ($db->recordCount()) {
        Log::debug('product_detail table is already up-to-date');
        return false;
    } else {
        prepareParseData($productCode);
        return true;
    }
}

?>