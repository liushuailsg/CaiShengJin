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
    $query_sql="select * from product_detail where code='$productCode' AND date='$lastWeekday'
            ORDER BY `product_detail`.`date` DESC ";
    $query_result = $db->query($query_sql);
    if ($db->recordCount()) {
        Log::debug('product_detail table is already up-to-date');
        return false;
    } else {
        prepareParseData($productCode);
        generateYieldRate($productCode);
        return true;
    }
}

function generateYieldRate($productCode) {
    global $db;
    $sql_query = "SELECT * FROM `product_detail` WHERE code='$productCode' ORDER BY `product_detail`.`date` DESC ";
    $query_result = $db->query($sql_query);
    $arr = $db->fetchAll();
    foreach ($arr as $a) {
        $currentDate = $a['date'];
        //########################################################################
        $lastWeek = date("Y-m-d", strtotime("-1 week", strtotime($currentDate)));
        $weekYieldRate = getYieldRate($db, $productCode, $currentDate, $lastWeek) * 365;
        $weekYieldRate = round($weekYieldRate, 4);
        //########################################################################
        $lastMonth = date("Y-m-d", strtotime("-1 month", strtotime($currentDate)));
        $monthYieldRate = getYieldRate($db, $productCode, $currentDate, $lastMonth) * 365;
        $monthYieldRate = round($monthYieldRate, 4);
        //########################################################################
        $lastQuarter = date("Y-m-d", strtotime("-3 month", strtotime($currentDate)));
        $quarterYieldRate = getYieldRate($db, $productCode, $currentDate, $lastQuarter) * 365;
        $quarterYieldRate = round($quarterYieldRate, 4);
        //########################################################################
        $lastYear = date("Y-m-d", strtotime("-1 year", strtotime($currentDate)));
        //$yearYieldRate = (getYieldRate($db, $productCode, $currentDate, $lastYear) * $this->count * 365) / $this->cost;
        $yearYieldRate = getYieldRate($db, $productCode, $currentDate, $lastYear) * 365;
        $yearYieldRate = round($yearYieldRate, 4);
        //########################################################################
        
        $query_sql="select * from product_detail where `code`='$productCode' AND `date`='$currentDate' AND
                `weekYieldRate`='$weekYieldRate' AND `monthYieldRate`='$monthYieldRate' AND
                `quarterYieldRate`='$quarterYieldRate' AND `yearYieldRate`='$yearYieldRate'";
        $query_result = $db->query($query_sql);
        if ($db->recordCount()) {
            Log::debug($productCode.' '.$currentDate.' '.$weekYieldRate.' '.$monthYieldRate . " already exist");
            return false;
        } else {
            $update_sql="update `product_detail` SET `weekYieldRate`='$weekYieldRate',`monthYieldRate`='$monthYieldRate',
                    `quarterYieldRate`='$quarterYieldRate',`yearYieldRate`='$yearYieldRate'
                    WHERE `code`='$productCode' AND `date`='$currentDate'";
            $bet_result = $db->query($update_sql);
            if (!$bet_result)
            {
                $ret = "插入失败";
            } else {
                $ret = "插入 ok";
            }
            Log::debug("product_detail yield rate data " . $ret);
        }
    }
}

/*
 * 此函数获取最大日期和最小日期区间的平均日收益
 * 结果 * 份数 * 365就是年收益，再除以成本就是这几天的年化收益率
 */
function getYieldRate($db, $code, $max_date, $min_date) {
    $sql_query = "SELECT * FROM `product_detail` WHERE code='$code' AND date>='$min_date' AND date<='$max_date'
            ORDER BY `product_detail`.`date` DESC ";
    $query_result = $db->query($sql_query);
    $arr = $db->fetchAll();
    $arr_count = count($arr, COUNT_NORMAL);
    if ($arr_count < 2) {
        return 0;
    }
    
    /*foreach ($arr as $a) {
        Log::debug('$result : ' . $a['date']);
    }*/
    
    $start_date = $arr[$arr_count-1]['date'];
    $end_date = $arr[0]['date'];
    $intervalTime = strtotime($end_date) - strtotime($start_date);
    $intervalDay = $intervalTime / (24*3600);
    Log::verbose('day---------------------------:' . $intervalDay);
    
    $start_worth = $arr[$arr_count-1]['net_worth'];
    $end_worth = $arr[0]['net_worth'];
    $intervalWorth = round(($end_worth - $start_worth), 4);
    Log::verbose('val---------------------------:' . $intervalWorth);
    $yieldRate = $intervalWorth / $intervalDay;
    return $yieldRate;
}

?>