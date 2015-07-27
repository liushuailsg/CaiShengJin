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
    $sql_query = "SELECT * FROM `product_info` WHERE code='$productCode' ORDER BY `product_info`.`_id` ASC ";
    $query_result = $db->query($sql_query);
    $arr = $db->fetchAll();
    
    $ret = false;
    foreach ($arr as $a) {
        if (insertDataToDB($a['_id'], $lastWeekday, $a['date'])) {
            $ret = true;
        }
    }
    
    if ($ret) {
        generateImage($productCode);
    } else {
        Log::debug('yield table is already up-to-date');
    }
    return $ret;
}

function insertDataToDB($id, $maxDate, $minDate) {
    $currentDate = $maxDate;
    Log::debug('$id is ' . $id . ',$currentDate is ' . $currentDate . ',$minDate is ' . $minDate);
    
    for ($i = 0; $currentDate > $minDate; $i++) {
        Log::verbose('$currentDate is ' . date("w", strtotime($currentDate)));
        global $db;
        $query_sql="select * from yield where _id='$id' AND date='$currentDate' ORDER BY `yield`.`date` DESC ";
        $query_result = $db->query($query_sql);
        if ($db->recordCount()) {
            Log::debug($id . ' ' . $currentDate . ' already exist!');
            if ($currentDate == $maxDate) {
                return false;
            }
            break;
        } else {
            $mProductInfo = ProductInfo::getInstance($db, $id);
            $mProductInfo->setCurrentDate($currentDate);
            $mProductInfo->getProductInfo($db);
            $mProductInfo->debugProductInfo();
            $insert_sql = "insert into yield (_id, code, date, yields, assets, dayYield,
                    weekYieldRate, monthYieldRate, quarterYieldRate, yearYieldRate) values
                    ($id, '$mProductInfo->code', '$currentDate', '$mProductInfo->yields', '$mProductInfo->assets',
                    '$mProductInfo->dayYield', '$mProductInfo->weekYieldRate', '$mProductInfo->monthYieldRate',
                    '$mProductInfo->quarterYieldRate', '$mProductInfo->yearYieldRate')";
            $query_result = $db->query($insert_sql);
            if (!$query_result)
            {
                $ret = "插入失败";
            } else {
                $ret = "插入 ok";
            }
            Log::debug("yield table " . $ret);
        }
        
        $currentDate = date("Y-m-d", strtotime("-1 day", strtotime($currentDate)));
    }
    return true;
}
?>