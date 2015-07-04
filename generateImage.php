<?php
if (!isset($rootDir)) $rootDir = './';
require_once($rootDir . "config.php"); //配置
require_once($rootDir . "common/util.php"); //工具类
require_once($rootDir . "lib/mysql.class.php"); //数据类
require_once($rootDir . "lib/func.class.php"); //核心类
require_once($rootDir . "ProductInfo.php");
?>

<?php 
// 删除图片
$result = @unlink($weekYieldRateImage) && @unlink($monthYieldRateImage) && @unlink($quarterYieldRateImage) && @unlink($yearYieldRateImage);
if ($result == true) {
    if(debug()) echo "png file" . ' delete succeed' . '</br>';
} else {
    if(debug()) echo "png file" . ' delete failed' . '</br>';
}

//生成图片
$sql_query = "SELECT * FROM `yield` WHERE code='8193'";
$query_result = $db->query($sql_query);
$pos = $db->recordCount() - 30;
$pos = $pos < 0 ? 0 : $pos;
$sql_query = "SELECT date,weekYieldRate,monthYieldRate,quarterYieldRate,yearYieldRate FROM `yield` WHERE code='8193' limit $pos,30";
$query_result = $db->query($sql_query);
$arr = $db->fetchAll();
$dateList = new ArrayObject();
$weekYieldRateList = new ArrayObject();
$monthYieldRateList = new ArrayObject();
$quarterYieldRateList = new ArrayObject();
$yearYieldRateList = new ArrayObject();
foreach ($arr as $a) {
    $dateList->append(date("m-d",strtotime($a['date'])));
    $weekYieldRateList->append($a['weekYieldRate'] * 100);
    $monthYieldRateList->append($a['monthYieldRate'] * 100);
    $quarterYieldRateList->append($a['quarterYieldRate'] * 100);
    $yearYieldRateList->append($a['yearYieldRate'] * 100);
}
$arr_count = count($arr, COUNT_NORMAL);
if ($arr_count != 0) {
    include_once('Naked.php');
    creatImageFile($dateList, $weekYieldRateList, $weekYieldRateImage, "七日年化收益率", "收益率");
    creatImageFile($dateList, $monthYieldRateList, $monthYieldRateImage, "单月年化收益率", "收益率");
    creatImageFile($dateList, $quarterYieldRateList, $quarterYieldRateImage, "季度年化收益率", "收益率");
    creatImageFile($dateList, $yearYieldRateList, $yearYieldRateImage, "年度年化收益率", "收益率");
}
?>