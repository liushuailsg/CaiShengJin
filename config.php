<?php

//配置数据库
$cfg["dbhost"]="localhost:3306"; //数据库主机名
$cfg["dbuser"]="root"; //数据库用户名
$cfg["dbpass"]="1QAZ2wsx-"; //数据库密码
$cfg["dbname"]="caishengjin"; //数据库名称
//$cfg["website"]="http://liushuailsg.sinaapp.com/chelsea"; //网站域名
//$cfg["webtitle"]="chelsea"; //系统名称

$cfg["svhost"]="localhost"; //服务器地址
$cfg["svport"]="80"; //服务器端口

$time_limit =600;
$sync_interval = 60; //60s
$sync_time = strtotime('+5 min') - time();

$storage_path = dirname(__FILE__) . "/../CSJ/";
$log_path = $storage_path . "log/csj.log";
$ImageDir = "./../CSJ/png/";
$weekYieldRateImage = $ImageDir . "weekYieldRate.png";
$monthYieldRateImage = $ImageDir . "monthYieldRate.png";
$quarterYieldRateImage = $ImageDir . "quarterYieldRate.png";
$yearYieldRateImage = $ImageDir . "yearYieldRate.png";

?>