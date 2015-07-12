<?php
if (!isset($rootDir)) $rootDir = './';
require_once($rootDir . "config.php"); //配置
require_once($rootDir . "class/ProductInfo.php");
require_once($rootDir . "common/util.php"); //工具类
require_once($rootDir . "lib/mysql.class.php"); //数据类
require_once($rootDir . "lib/func.class.php"); //核心类
require_once($rootDir . "pChart/pData.class");
require_once($rootDir . "pChart/pChart.class");
?>

<?php
function generateImage($productCode) {
    // 删除图片
    global $weekYieldRateImage, $monthYieldRateImage, $quarterYieldRateImage, $yearYieldRateImage;
    $result = @unlink($weekYieldRateImage) && @unlink($monthYieldRateImage) && @unlink($quarterYieldRateImage) && @unlink($yearYieldRateImage);
    if ($result == true) {
        Log::debug("png file" . ' delete succeed');
    } else {
        Log::debug("png file" . ' delete failed');
    }
    
    //生成图片
    global $db;
    $sql_query = "SELECT * FROM `yield` WHERE code='8193' ORDER BY `yield`.`date` ASC ";
    $query_result = $db->query($sql_query);
    $pos = $db->recordCount() - 30;
    $pos = $pos < 0 ? 0 : $pos;
    $sql_query = "SELECT date,weekYieldRate,monthYieldRate,quarterYieldRate,yearYieldRate
            FROM `yield` WHERE code='8193' ORDER BY `yield`.`date` ASC limit $pos,30";
    $query_result = $db->query($sql_query);
    $arr = $db->fetchAll();
    $dateList = new ArrayObject();
    $weekYieldRateList = new ArrayObject();
    $monthYieldRateList = new ArrayObject();
    $quarterYieldRateList = new ArrayObject();
    $yearYieldRateList = new ArrayObject();
    foreach ($arr as $a) {
        $dateList->append(date("m-d",strtotime($a['date'])));
        $weekYieldRate = $a['weekYieldRate'] * 100;
        if ($weekYieldRate > 0) $weekYieldRateList->append($weekYieldRate);
        $monthYieldRate = $a['monthYieldRate'] * 100;
        if ($monthYieldRate > 0) $monthYieldRateList->append($monthYieldRate);
        $quarterYieldRate = $a['quarterYieldRate'] * 100;
        if ($quarterYieldRate > 0) $quarterYieldRateList->append($quarterYieldRate);
        $yearYieldRate = $a['yearYieldRate'] * 100;
        if ($yearYieldRate > 0) $yearYieldRateList->append($yearYieldRate);
    }
    $arr_count = count($arr, COUNT_NORMAL);
    if ($arr_count != 0 && $dateList->count() != 0) {
        if ($weekYieldRateList->count() != 0) {
            creatImageFile($dateList, $weekYieldRateList, $weekYieldRateImage, "七日年化收益率", "收益率");
        }
        if ($monthYieldRateList->count() != 0) {
            creatImageFile($dateList, $monthYieldRateList, $monthYieldRateImage, "单月年化收益率", "收益率");
        }
        if ($quarterYieldRateList->count() != 0) {
            creatImageFile($dateList, $quarterYieldRateList, $quarterYieldRateImage, "季度年化收益率", "收益率");
        }
        if ($yearYieldRateList->count() != 0) {
            creatImageFile($dateList, $yearYieldRateList, $yearYieldRateImage, "年度年化收益率", "收益率");
        }
    }
}

function creatImageFile($xAxis, $data, $path, $title, $serieName) {
    // Dataset definition
    $DataSet = new pData;
    $DataSet->AddPoint($data);
    $DataSet->AddSerie();
    $DataSet->AddPoint($xAxis, "XAxis");

    $DataSet->SetAbsciseLabelSerie("XAxis");
    $DataSet->SetSerieName($serieName,"Serie1");
    //$DataSet->SetYAxisName("Temperature");
    //$DataSet->SetXAxisName("Month of the year");

    // Initialise the graph
    $Test = new pChart(1200,230);
    $Test->setFontProperties("Fonts/tahoma.ttf",10);
    $Test->setGraphArea(40,30,1180,200);
    $Test->drawGraphArea(252,252,252);
    $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
    $Test->drawGrid(4,TRUE,230,230,230,255);

    // Draw the line graph
    $Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
    $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);

    // Finish the graph
//    $Test->setFontProperties("Fonts/tahoma.ttf",8);
    $Test->setFontProperties("Fonts/YaHei.ttf",8);
    $Test->drawLegend(45,35,$DataSet->GetDataDescription(),255,255,255);
    $Test->setFontProperties("Fonts/YaHei.ttf",10);
    $Test->drawTitle(60,22,$title,50,50,50,585);
    $Test->Render($path);
}

?>