<?php

header("Content-type: text/html; charset=utf-8");
//session_start();
//error_reporting(0);
set_time_limit(200);
if (!isset($rootDir)) $rootDir = '../';


//引入类库及公共方法
require_once($rootDir . "config.php"); //配置
require_once($rootDir . "common/util.php"); //工具类
require_once($rootDir . "lib/mysql.class.php"); //数据类
require_once($rootDir . "lib/func.class.php"); //核心类
require_once($rootDir . "class/ContentInfo.php");

    function prepareParseData($productCode) {
        $urlTarget = 'http://www.cmbchina.com/cfweb/personal/prodvalue.aspx?PrdType=T0026&PrdCode=' . $productCode;
        $pageNumber = 0;
        
        do {
            Log::debug('####################################### start ');
            $pageNumber++;
            $url = $urlTarget . '&PageNo=' . $pageNumber;
            Log::debug($url);
            $condition = ParseFromHTMLFile($url);
            Log::debug('which continue is ' . $condition);
            Log::debug('####################################### end ');
            sleep(5);
        } while ($condition);
    }

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
                $arrayList = ParseFromDOMElement($table);
                if ($arrayList->count() > 1) {
                    $status = insertToDB($arrayList);
                    if ($status) $ret = true;
                }
            }
            Log::debug('---------------------------------------');
        }
        return $ret;
    }
    
    function ParseFromDOMElement(DOMElement $table) {
        Log::debug('ParseFromDOMElement.');
        
        $contentList = new ArrayObject();
        $rows_list = $table->getElementsByTagName('tr');
        
        if ($rows_list->length === 0) {
            Log::debug('rows list is empty!');
        } else {
            Log::debug('$rows_list length is ' . $rows_list->length);
        }
        
        foreach ($rows_list as $row)
        {
            $contentInfo = new ContentInfo();
            $contentInfo->ParseFromDOMElement($row);
            $contentList->append($contentInfo);
        }
        
        if ($contentList->count() === 0) {
            Log::debug('content list is empty!');
        } else {
            Log::debug('$contentList count is ' . $contentList->count());
        }
        
        foreach ($contentList as $content) {
            //print_r($content);

            if (empty($content)) {
                Log::debug('Content is empty.');
            } else {
                Log::verbose($content->PrdCode.' '.$content->PrdName.' '.$content->Content.' '.$content->Time);
            }
            
        }
        
        return $contentList;
    }
    
    function insertToDB($contentList) {
        global $db;
        
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
                
                $query_sql="select * from original_product_detail where code='$content_code' and name='$content_name'
                        and net_worth='$content_content' and date='$content_time'
                        ORDER BY `original_product_detail`.`date` DESC ";
                $query_result = $db->query($query_sql);
                if ($db->recordCount()) {
                    Log::debug($content_code.' '.$content_name.' '.$content_content.' '.$content_time . " already exist");
                    return false;
                }
                
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
                Log::debug("product_detail original data " . $ret);
            }
        }
        
        $eid_query = "SELECT * FROM `original_product_detail` ORDER BY `original_product_detail`.`date` DESC ";
        $id_result = $db->query($eid_query);
        Log::debug('$result is ' . $id_result);
        $eid_arr = $db->fetchAll();
        $eid_count = count($eid_arr, COUNT_NORMAL);
        Log::debug('count is ' . $eid_count);
        
        return true;
    }
?>
