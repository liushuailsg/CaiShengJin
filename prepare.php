<?php
header("Content-type: text/html; charset=utf-8");
//error_reporting(0);
set_time_limit(200);
if (!isset($rootDir)) $rootDir = './';

require_once($rootDir . "common/ParseHtmlData.php");

$date = date("Y-m-d");
if (isWeekday($date)) {
    $lastWeekday = getLastWeekday($date);
} else {
    $lastWeekday = getLastWeekday(getLastWeekday($date));
}
$query_sql="select * from product_detail where date='$lastWeekday'";
$query_result = $db->query($query_sql);
if ($db->recordCount()) {
    if(debug()) echo 'original_product_detail table is already up-to-date' . '</br>';
} else {
    echo 'prepareData' . '</br>';
    prepareData(8193);
}

//prepareData(107107);

?>