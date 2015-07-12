<?php
if (!isset($rootDir)) $rootDir = './';
require_once($rootDir . "config.php"); //配置
require_once($rootDir . "class/ProductInfo.php");
require_once($rootDir . "common/util.php"); //工具类
require_once($rootDir . "lib/mysql.class.php"); //数据类
require_once($rootDir . "lib/func.class.php"); //核心类
require_once($rootDir . "generateImage.php");
?>

<?php
function processData($productCode) {
    $date = date("Y-m-d");
    if (isWeekday($date)) {
        $lastWeekday = getLastWeekday($date);
    } else {
        $lastWeekday = getLastWeekday(getLastWeekday($date));
    }
    
    global $db;
    $query_sql="select * from yield where date='$lastWeekday' ORDER BY `yield`.`date` DESC ";
    $query_result = $db->query($query_sql);
    if ($db->recordCount()) {
        Log::debug('yield table is already up-to-date');
        return false;
    }
    
    $sql_query = "SELECT * FROM `product_info` WHERE code='8193' ORDER BY `product_info`.`date` DESC ";
    $query_result = $db->query($sql_query);
    $arr = $db->fetchAll();
    $arr_count = count($arr, COUNT_NORMAL);
    
    if ($arr_count != 0) {
        $code = '8193';
        $currentDate = $lastWeekday;
        $minDate = $arr[0]['date'];
        Log::debug('$currentDate is ' . $currentDate);
        Log::debug('$minDate is ' . $minDate);
        for ($i = 0; $currentDate > $minDate; $i++) {
            Log::verbose('$currentDate is ' . date("w", strtotime($currentDate)));
            $query_sql="select * from yield where code='$code' and date='$currentDate'  ORDER BY `yield`.`date` DESC ";
            $query_result = $db->query($query_sql);
            if ($db->recordCount()) {
                Log::debug($code . ' ' . $currentDate . ' already exist!');
                break;
            } else {
                $mProductInfo = ProductInfo::getInstance($db, $code);
                $mProductInfo->setCurrentDate($currentDate);
                $mProductInfo->getProductInfo($db);
                $mProductInfo->debugProductInfo();
                $insert_sql = "insert into yield (code, date, yields, assets, dayYield,
                        weekYieldRate, monthYieldRate, quarterYieldRate, yearYieldRate) values
                        ('$mProductInfo->code', '$currentDate', '$mProductInfo->yields', '$mProductInfo->assets',
                        '$mProductInfo->dayYield', '$mProductInfo->weekYieldRate', '$mProductInfo->monthYieldRate',
                        '$mProductInfo->quarterYieldRate', '$mProductInfo->yearYieldRate')";
                $query_result = $db->query($insert_sql);
                if (!$query_result)
                {
                    $ret = "插入失败";
                } else {
                    $ret = "插入 ok";
                }
                Log::debug("yield " . $ret);
                $needToGenerateImage = true;
            }
            
            $currentDate = date("Y-m-d", strtotime("-1 day", strtotime($currentDate)));
        }
    }
    
    if (isset($needToGenerateImage) && $needToGenerateImage) {
        generateImage($productCode);
    }
    return true;
}
?>