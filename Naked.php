<?php
/*
 Naked: Naked and easy!
 */

// Standard inclusions
include("pChart/pData.class");
include("pChart/pChart.class");

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