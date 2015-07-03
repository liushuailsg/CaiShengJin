<?php
header("Content-type: text/html; charset=utf-8");
//error_reporting(0);
set_time_limit(200);
if (!isset($rootDir)) $rootDir = './';

require_once($rootDir . "common/ParseHtmlData.php");

prepareData(8193);
//prepareData(107107);

?>