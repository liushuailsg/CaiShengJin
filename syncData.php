<?php
if (!isset($rootDir)) $rootDir = './';
require_once($rootDir . "class/Log.php"); //Log类
require_once($rootDir . "config.php"); //配置
require_once($rootDir . "common/util.php"); //工具类
require_once($rootDir . "lib/mysql.class.php"); //数据类
require_once($rootDir . "lib/func.class.php"); //核心类
require_once($rootDir . "prepareData.php");
require_once($rootDir . "processData.php");

$value = ignore_user_abort(true); // run script in background
Log::verbose('$value is ' . $value);
set_time_limit(0); // run script forever

//ob_end_flush();
//echo str_repeat(" ",256);
//echo str_repeat("　",256); //ie下 需要先发送256个字节

$productCode = $_GET["param"];
Log::debug('param is ' . $productCode . ', param count is ' . count($_GET["param"]));
syncData($productCode);

function syncData($productCode) {
    global $db;
    $query_sql="select * from sync_task where code='$productCode'";
    $query_result = $db->query($query_sql);
    $arr = $db->fetchAll();
    $arr_count = count($arr, COUNT_NORMAL);
    
    if ($arr_count != 0) {
        if ($arr[0]['status']) {
            Log::debug('sync task already start!');
            return false;
        } else {
            Log::debug('syncData start!');
            
            $newStatus = !$arr[0]['status'];
            $code = $arr[0]['code'];
            $update_sql="update `sync_task` SET `status`='1' WHERE `code`='$code'";
            $query_result = $db->query($update_sql);
            
            try {
                global $sync_time;
                $timeout = time() + $sync_time;
                while (inSync($productCode) && time() < $timeout) {
                    Log::debug('while time is ' . time() . ', $timeout is ' . $timeout);
                    $ret1 = prepareData($productCode);
                    $ret2 = processData($productCode);
                    if (!$ret1 && !$ret2) {
                        Log::debug('Data is already up-to-date!');
                        break;
                    }
                    ob_flush();
                    flush();
                    global $sync_interval;
                    sleep($sync_interval); // wait 1 minutes
                }
                Log::debug('syncData end!');
            } catch (Exception $e) {
                Log::log_exception($e);
            }
            
            $update_sql="update `sync_task` SET `status`='0' WHERE `code`='$productCode'";
            $query_result = $db->query($update_sql);
            return true;
        }
    } else {
        Log::debug($productCode . ' sync task not exist!');
        return false;
    }
}

?>
