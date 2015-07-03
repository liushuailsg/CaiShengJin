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
require_once($rootDir . "ContentInfo.php");

    function prepareData($productCode) {
        $urlTarget = 'http://www.cmbchina.com/cfweb/personal/prodvalue.aspx?PrdType=T0026&PrdCode=' . $productCode;
        $pageNumber = 0;
        
        do {
            if(debug()) echo '####################################### start ' . '</br>';
            sleep(1);
            $pageNumber++;
            $url = $urlTarget . '&PageNo=' . $pageNumber;
            if(debug()) echo $url . '</br>';
            $condition = ParseFromHTMLFile($url);
            if(debug()) echo 'which continue is ' . $condition . '</br>';
            if(debug()) echo '####################################### end ' . '</br>';
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
            if(debug()) echo 'table list as ' . $tableProp . '</br>';
            if ($tableProp == 'ProductTable') {
                $arrayList = ParseFromDOMElement($table);
                if ($arrayList->count() > 1) {
                    $status = insertToDB($arrayList);
                    if ($status) $ret = true;
                }
            }
            if(debug()) echo '---------------------------------------' . '</br>';
        }
        return $ret;
    }
    
    function ParseFromDOMElement(DOMElement $table) {
        if(debug()) echo 'ParseFromDOMElement.' . '</br>';
        
        $contentList = new ArrayObject();
        $rows_list = $table->getElementsByTagName('tr');
        
        if ($rows_list->length === 0) {
            if(debug()) echo 'rows list is empty!' . '</br>';
        } else {
            if(debug()) echo '$rows_list length is ' . $rows_list->length . '</br>';
        }
        
        foreach ($rows_list as $row)
        {
            $contentInfo = new ContentInfo();
            $contentInfo->ParseFromDOMElement($row);
            $contentList->append($contentInfo);
        }
        
        if ($contentList->count() === 0) {
            if(debug()) echo 'content list is empty!' . '</br>';
        } else {
            if(debug()) echo '$contentList count is ' . $contentList->count() . '</br>';
        }
        
        foreach ($contentList as $content) {
            if(debug()) print_r($content);
            if(debug()) echo '</br>';

            if (empty($content)) {
                if(debug()) echo 'Content is empty.' . '</br>';
            } else {
                if(debug()) echo $content->PrdCode.' '.$content->PrdName.' '.$content->Content.' '.$content->Time . '</br>';
            }
            
        }
        
        return $contentList;
    }
    
    function insertToDB($contentList) {
        global $db;
        
        foreach ($contentList as $content) {
            $is_label = $content->isLabel;
            $content->PrdName = str_utf8_decode($content->PrdName);
            preg_match_all("/[\x{4e00}-\x{9fa5}]+/u", $content->PrdName, $chinese) . '</br>';
            $content_name = implode("", $chinese[0]);
            if ($content->isLabel) {
                $content->PrdCode = str_utf8_decode($content->PrdCode);
                preg_match_all("/[\x{4e00}-\x{9fa5}]+/u", $content->PrdCode, $chinese) . '</br>';
                $content_code = implode("", $chinese[0]);
                $content->Content = str_utf8_decode($content->Content);
                preg_match_all("/[\x{4e00}-\x{9fa5}]+/u", $content->Content, $chinese) . '</br>';
                $content_content = implode("", $chinese[0]);
                $content->Time = str_utf8_decode($content->Time);
                preg_match_all("/[\x{4e00}-\x{9fa5}]+/u", $content->Time, $chinese) . '</br>';
                $content_time = implode("", $chinese[0]);
            } else {
                $content_code = intval($content->PrdCode);
                $content_content = strval(floatval($content->Content));
                $content_time = intval($content->Time);
                
                $query_sql="select * from original_product_detail where code='$content_code' and name='$content_name'
                        and net_worth='$content_content' and date='$content_time'";
                $query_result = $db->query($query_sql);
                if ($db->recordCount()) {
                    if(debug()) echo $content_code.' '.$content_name.' '.$content_content.' '.$content_time . '</br>';
                    return false;
                }
                
                $insert_sql = "insert into original_product_detail (code, name, net_worth, date, is_label) values
                    ('$content_code', '$content_name', '$content_content', '$content_time', '$is_label')";
                $bet_result = $db->query($insert_sql);
                if (!$bet_result)
                {
                    $ret = "插入失败" . '</br>';
                } else {
                    $ret = "插入 ok" . '</br>';
                }
                if(debug()) echo $ret;
                
                $worth = floatval($content_content);
                $date = date("Ymd", strtotime($content_time));
                $insert_sql = "insert into product_detail (code, name, net_worth, date) values
                        ('$content_code', '$content_name', '$worth', '$date')";
                $bet_result = $db->query($insert_sql);
                if (!$bet_result)
                {
                    $ret = "插入失败" . '</br>';
                } else {
                    $ret = "插入 ok" . '</br>';
                }
                if(debug()) echo $ret;
            }
        }
        
        $eid_query = "SELECT * FROM `original_product_detail`";
        $id_result = $db->query($eid_query);
        if(debug()) echo '$result is ' . $id_result . '</br>';
        $eid_arr = $db->fetchAll();
        $eid_count = count($eid_arr, COUNT_NORMAL);
        if(debug()) echo 'count is ' . $eid_count . '</br>';
        
        return true;
    }
?>