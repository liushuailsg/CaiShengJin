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
?>