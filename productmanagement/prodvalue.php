<?php
//====================================================
//      FileName: prodvalue.php
//      Summary:  Product net worth
//====================================================
header("Content-type: text/html; charset=utf-8");
//session_start();
//error_reporting(0);
set_time_limit(200);
$rootDir = '../';


//引入类库及公共方法
require_once($rootDir . "class/config.php"); //配置
require_once($rootDir . "lib/mysql.class.php"); //数据类
require_once($rootDir . "lib/func.class.php"); //核心类
require_once($rootDir . "ContentInfo.php");
//echo '<meta http-equiv=Content-Type content="text/html;charset=utf-8">';
//error_reporting(E_ALL & ~E_NOTICE);

?>


<?php 
$productCode = $_GET["code"];
Log::debug('Product code is ' . $productCode);
?>

<?php
$urlTarget = 'http://www.cmbchina.com/cfweb/personal/prodvalue.aspx?PrdType=T0026&PrdCode=' . $productCode;
$pageNomboer = 0;
Log::debug("urlTarget is " . $urlTarget);

//$urlTarget = "../prodvalue.html";

global $db;
$del_sql = "delete from original_product_detail";
$db->query($del_sql);
$del_sql = "delete from product_detail";
$db->query($del_sql);

do {
    Log::debug('####################################### start ');
    sleep(1);
    $pageNomboer++;
    $url = $urlTarget . '&PageNo=' . $pageNomboer;
    Log::debug($url);
    $condition = ParseFromHTMLFile($url);
    Log::debug('which continue is ' . $condition);
    Log::debug('####################################### end ');
} while ($condition);

?>


<?php

    function ParseFromHTMLFile($url) {
        //建立Dom对象，分析HTML文件；
        $htmDoc = new DOMDocument;
        $htmDoc->loadHTMLFile($url);
        $htmDoc->normalizeDocument();
        
        //获得到此文档中每一个Table对象；
        $tables_list = $htmDoc->getElementsByTagName('table');
        $ret = false;
        foreach ($tables_list as $table) {
            //得到Table对象的class属性
            $tableProp = $table->getAttribute('class');
            Log::debug('table list as ' . $tableProp);
            if ($tableProp == 'ProductTable') {
                $count = ParseFromDOMElement($table);
                if ($count > 1) {
                    $ret = true;
                }
            }
            Log::debug('---------------------------------------');
        }
        return $ret;
    }
    
    function ParseFromDOMElement(DOMElement $table) {
        Log::debug('ParseFromDOMElement.');
        
        $rows_list = $table->getElementsByTagName('tr');
        
        if ($rows_list->length === 0) {
            Log::debug('rows list is empty!');
            return 0;
        } else {
            Log::debug('$rows_list length is ' . $rows_list->length);
        }
        
        $contentList = new ArrayObject();
        foreach ($rows_list as $row)
        {
            $contentInfo = new ContentInfo();
            $contentInfo->ParseFromDOMElement($row);
            $contentList->append ($contentInfo);
        }
        
        if ($contentList->count() === 0) {
            Log::debug('content list is empty!');
            return 0;
        } else {
            Log::debug('$contentList count is ' . $contentList->count());
        }
        
        foreach ($contentList as $content) {
//            print_r($content);

            if (empty($content)) {
                Log::debug('Content is empty.');
            } else {
                Log::debug($content->PrdCode.' '.$content->PrdName.' '.$content->Content.' '.$content->Time);
            }
            
        }
        
        global $db;
//        $del_sql = "delete from original_product_detail";
//        $db->query($del_sql);
        
        foreach ($contentList as $content) {
            $is_label = $content->isLabel;
            $content->PrdName = str_utf8_decode($content->PrdName);
            preg_match_all("/[\x{4e00}-\x{9fa5}]+/u", $content->PrdName, $chinese);
            $content_name = implode("", $chinese[0]);
            if ($content->isLabel) {
                $content->PrdCode = str_utf8_decode($content->PrdCode);
                preg_match_all("/[\x{4e00}-\x{9fa5}]+/u", $content->PrdCode, $chinese);
                $content_code = implode("", $chinese[0]);
                $content->Content = str_utf8_decode($content->Content);
                preg_match_all("/[\x{4e00}-\x{9fa5}]+/u", $content->Content, $chinese);
                $content_content = implode("", $chinese[0]);
                $content->Time = str_utf8_decode($content->Time);
                preg_match_all("/[\x{4e00}-\x{9fa5}]+/u", $content->Time, $chinese);
                $content_time = implode("", $chinese[0]);
            } else {
                $content_code = intval($content->PrdCode);
                $content_content = strval(floatval($content->Content));
                $content_time = intval($content->Time);
                
                $insert_sql = "insert into original_product_detail (code, name, net_worth, date, is_label) values
                    ('$content_code', '$content_name', '$content_content', '$content_time', '$is_label')";
                $bet_result = $db->query($insert_sql);
                if (!$bet_result)
                {
                    $ret = "插入失败";
                } else {
                    $ret = "插入 ok";
                }
                Log::debug("original_product_detail " . $ret);

                $worth = floatval($content_content);
                $date = date("Ymd", strtotime($content_time));
                $insert_sql = "insert into product_detail (code, name, net_worth, date) values
                        ('$content_code', '$content_name', '$worth', '$date')";
                $bet_result = $db->query($insert_sql);
                if (!$bet_result)
                {
                    $ret = "插入失败";
                } else {
                    $ret = "插入 ok";
                }
                Log::debug("product_detail " . $ret);
            }
        }
        
        $eid_query = "SELECT * FROM `original_product_detail` ORDER BY `original_product_detail`.`date` DESC ";
        $id_result = $db->query($eid_query);
        Log::debug('$result is ' . $id_result);
        $eid_arr = $db->fetchAll();
        $eid_count = count($eid_arr, COUNT_NORMAL);
        Log::debug('count is ' . $eid_count);
        
        return $contentList->count();
    }
?>

<?php
function str_conv_utf8($str) {
    $charset =  mb_detect_encoding($str, array('ASCII', 'GB2312', 'GBK', 'UTF-8'));
    $charset = strtolower($charset);
    Log::debug('$charset is ' . $charset);
    if('cp936' == $charset) {
        $charset='GBK';
    }
    if("utf-8" != $charset) {
        Log::debug('$charset is ' . $charset);
//        $str = iconv($charset, "UTF-8//IGNORE", $str);
        $str = iconv($charset, "UTF-8", $str);
    }
//    $text = mb_convert_encoding($str, "UTF-8", array('ASCII', 'GB2312', 'GBK', 'UTF-8'));
//    Log::debug('$text = ' . $text);
    return $str;
}

function str_utf8_decode($str) {
    $str = utf8_decode($str);
    //Log::debug('str_utf8_decode is ' . $str);
    return $str;
}
?>

