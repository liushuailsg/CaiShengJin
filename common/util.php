<?php

    function str_utf8_decode($str) {
        $str = utf8_decode($str);
        //Log::debug('str_utf8_decode is ' . $str);
        return $str;
    }
    
    function debug() {
        return true;
        //return false;
    }
    
    function isWeekday($date) {
        $weekNum = date("w", strtotime($date));
        if ($weekNum > 0 && $weekNum < 6) {
            return true;
        }
        return false;
    }
    
    function getLastWeekday($date) {
        $lastDateTimestamp = strtotime($date);
        do {
            $lastDateTimestamp = strtotime("-1 day", $lastDateTimestamp);
            $lastDate = date("Y-m-d", $lastDateTimestamp);
        } while(!isWeekday($lastDate));
        return $lastDate;
    }
    
    function getMillisecond() {
        list($s1, $s2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }
    
    function inSync($productCode) {
        global $db;
        $query_sql="select * from sync_task where code='$productCode'";
        $query_result = $db->query($query_sql);
        $arr = $db->fetchAll();
        $arr_count = count($arr, COUNT_NORMAL);
        if ($arr_count != 0) {
            if ($arr[0]['status']) {
                Log::debug('sync task is running!');
                return true;
            }
        }
        return false;
    }
    
    function startSyncData($host, $port, $productCode) {
        Log::debug('startSyncData function run');
//        $path = '/CaiShengJin/syncData.php';
//        pclose(popen('php ' . $path . ' &', 'r'));
        $ch = curl_init();
//        $curl_opt = array(
//            CURLOPT_URL, 'http://localhost/CaiShengJin/syncData.php?param=8193',
//            CURLOPT_RETURNTRANSFER,1,
//            CURLOPT_TIMEOUT,1
//        );
//        curl_setopt_array($ch, $curl_opt);
        curl_setopt($ch, CURLOPT_URL, "http://localhost/CaiShengJin/syncData.php" . "?param=" . $productCode);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
    }
    
    function startSync($host, $port, $productCode) {
        $fp = fsockopen($host, $port, $errno, $errstr, 5);
        if(!$fp) {
            Log::debug("$errstr ($errno)");
            return;
        }
        stream_set_blocking($fp, 0);
        sleep(1);
        
//        $urlinfo = parse_url('http://localhost/CaiShengJin/syncData.php');
//        $host = $urlinfo['host'];
//        $path = $urlinfo['path'];
        $path = '/CaiShengJin/syncData.php';
        $out = "POST ".$path."?param=".$productCode." HTTP/1.1\r\n";
        $out .= "host:".$host."\r\n";
        $out .= "content-type:application/x-www-form-urlencoded\r\n";
        $out .= "connection:close\r\n\r\n";
        fputs($fp, $out);
        fclose($fp);
    }
    
    function debug_log($content) {
        $LOG_FILE = '/debug.log';
        $fullname = dirname(__FILE__).$LOG_FILE;
        Log::debug('$fullname is ' . $fullname);
        $content = date('[Y-m-d H:i:s]').$content."\n";
        file_put_contents($fullname, $content, FILE_APPEND);
    }
    
    function isUpToDate() {
        $date = date("Y-m-d");
        if (isWeekday($date)) {
            $lastWeekday = getLastWeekday($date);
        } else {
            $lastWeekday = getLastWeekday(getLastWeekday($date));
        }
        
        global $db;
        $query_sql="select * from product_detail where date='$lastWeekday'";
        $query_result = $db->query($query_sql);
        if ($db->recordCount()) {
            Log::debug('util product_detail table is already up-to-date');
            return true;
        } else {
            return false;
        }
    }
?>
