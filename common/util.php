<?php

    function str_utf8_decode($str) {
        $str = utf8_decode($str);
        //echo 'str_utf8_decode is ' . $str . '</br>';
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
?>