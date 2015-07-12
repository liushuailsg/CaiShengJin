<?php
set_error_handler(array('Log', 'log_error'));
set_exception_handler(array('Log', 'log_exception'));

class Log {
    
    const NONE = 1;
    /**
     * Priority constant for the println method; use Log.v.
     */
    const VERBOSE = 2;

    /**
     * Priority constant for the println method; use Log.d.
     */
    const DEBUG = 3;

    /**
     * Priority constant for the println method; use Log.i.
     */
    const INFO = 4;

    /**
     * Priority constant for the println method; use Log.w.
     */
    const WARN = 5;

    /**
     * Priority constant for the println method; use Log.e.
     */
    const ERROR = 6;

    /**
     * Priority constant for the println method.
     */
    const ASSERT = 7;
    
    public static function level() {
        return self::DEBUG;
    }
    
    public static function verbose($content) {
        if (self::level() > self::VERBOSE) {
            return;
        }
        self::log_print($content);
    }
    
    public static function debug($content) {
        if (self::level() > self::DEBUG) {
            return;
        }
        self::log_print($content);
    }
    
    public static function log_print($content) {
        //$LOG_FILE = '/../log/debug.log';
        //$fullname = dirname(__FILE__).$LOG_FILE;
        //echo '$fullname is ' . $fullname . '</br>';
        
        global $log_path;
        $fullname = $log_path;
        $mysql_thread_id = sprintf("[%s]", mysql_thread_id());
        $content = date('[Y-m-d H:i:s]') . $mysql_thread_id.$content . "\n";
        file_put_contents($fullname, $content, FILE_APPEND);
    }
    
    public static function log_echo($content) {
        if (self::level() > self::DEBUG) {
            return;
        }
        echo $content . '</br>';
    }
    
    public static function log_error($errno, $errstr, $errfile, $errline) {
//        echo "<b>Custom error:</b> [$errno] $errstr<br />";
//        echo " Error on line $errline in $errfile<br />";
//        echo "Ending Script";

//        $error = "Custom error: [$errno] $errstr" . "\n".
//        "Error on line $errline in $errfile" . "\n";
        $error = "Custom error: [$errno] $errstr in $errfile on line $errline";
        self::debug($error);
        //die();
    }
    
    public static function log_exception($content) {
//        echo "<b>Exception:</b> " , $exception->getMessage();
        $exception = "Exception: " . $exception->getMessage() . "\n".
        self::debug($exception);
    }
}
