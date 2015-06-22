<?php
/*
 Naked: Naked and easy!
 */

// Standard inclusions
include("pChart/pData.class");
include("pChart/pChart.class");

echo 'Naked start'.'</br>';

function creatImageFile($data) {
    // Dataset definition
    $DataSet = new pData;
    $DataSet->AddPoint($data);
    $DataSet->AddSerie();
    $DataSet->SetSerieName("Sample data","Serie1");

    // Initialise the graph
    $Test = new pChart(700,230);
    $Test->setFontProperties("Fonts/tahoma.ttf",10);
    $Test->setGraphArea(40,30,680,200);
    $Test->drawGraphArea(252,252,252);
    $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
    $Test->drawGrid(4,TRUE,230,230,230,255);

    // Draw the line graph
    $Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
    $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);

    // Finish the graph
    $Test->setFontProperties("Fonts/tahoma.ttf",8);
    $Test->drawLegend(45,35,$DataSet->GetDataDescription(),255,255,255);
    $Test->setFontProperties("Fonts/tahoma.ttf",10);
    $Test->drawTitle(60,22,"My pretty graph",50,50,50,585);
    $Test->Render("YieldRate.png");
}

echo 'Naked end'.'</br>';
?>