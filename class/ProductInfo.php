<?php
class ProductInfo {

    var $code; //产品代码
    var $name; //产品名称
    var $cost; //成本
    var $date; //购买日期
    
    var $count; //购买份数
    var $currentDate; //当前日期
    var $yields; //累计收益
    var $assets; //总资产

    var $dayYield; //当日收益
//    var $tenThousandCopiesYields; //万份收益

    var $weekYieldRate; //七日年化收益率
    var $monthYieldRate; //单月年化收益率
    var $quarterYieldRate; //季度年化收益率
    var $yearYieldRate; //年度收益率

    public function __construct($PrdCode, $PrdName, $PrdCost, $date) {
        $this->code = $PrdCode;
        $this->name = $PrdName;
        $this->cost = $PrdCost;
        $this->date = $date;
        $this->currentDate = date("Y-m-d");
    }
    
    public static function getInstance($db, $code) {
        $sql_query = "SELECT * FROM `product_info` WHERE code='$code' ORDER BY `product_info`.`date` DESC ";
        $query_result = $db->query($sql_query);
        $arr = $db->fetchAll();
        $arr_count = count($arr, COUNT_NORMAL);
        Log::verbose('ProductInfo getInstance $arr_count length is ' . $arr_count);
        if ($arr_count != 0) {
            $mProductInfo = new ProductInfo($arr[0]['code'], $arr[0]['name'], $arr[0]['cost'], $arr[0]['date']);
            return $mProductInfo;
        }
    }
    
    public  function setCurrentDate($date) {
        $this->currentDate = date("Y-m-d", strtotime($date));
    }
    
    public function getProductInfo($db) {
        $sql_query = "SELECT * FROM `product_detail` WHERE code='8193' AND date='$this->date'
                ORDER BY `product_detail`.`date` DESC ";
        $query_result = $db->query($sql_query);
        $arr = $db->fetchAll();
        $arr_count = count($arr, COUNT_NORMAL);
        if ($arr_count != 0) {
            $this->count = round(($this->cost / $arr[0]['net_worth']), 2);
        }
        //########################################################################
//        $currentDate = date("Y-m-d", strtotime("-1 day", strtotime($this->currentDate)));
        $currentDate = $this->currentDate;
        
        $sql_query = "SELECT * FROM `product_detail` WHERE code='8193' AND date>='$this->date' AND date<'$currentDate'
                ORDER BY `product_detail`.`date` DESC ";
        $query_result = $db->query($sql_query);
        $arr = $db->fetchAll();
        $arr_count = count($arr, COUNT_NORMAL);
        if ($arr_count != 0) {
            $this->assets = round($arr[0]['net_worth'] * $this->count, 2);
            $this->yields = $this->assets - $this->cost;
            $this->yields = round($this->yields, 2);
            if ($arr_count > 1 && isWeekday($currentDate) && getLastWeekday($currentDate) == $arr[0]['date']) {
                $lastAssets = round($arr[1]['net_worth'] * $this->count, 2);
                $this->dayYield = round($this->assets - $lastAssets, 2);
            }
        }
        //########################################################################
        $lastWeek = date("Y-m-d", strtotime("-1 week", strtotime($currentDate)));
        $weekYieldRate = (self::getYieldRate($db, $currentDate, $lastWeek) * $this->count * 365) / $this->cost;
        $this->weekYieldRate = $weekYieldRate;
        $this->weekYieldRate = round($this->weekYieldRate, 4);
        //########################################################################
        $lastMonth = date("Y-m-d", strtotime("-1 month", strtotime($currentDate)));
        $monthYieldRate = (self::getYieldRate($db, $currentDate, $lastMonth) * $this->count * 365) / $this->cost;
        $this->monthYieldRate = $monthYieldRate;
        $this->monthYieldRate = round($this->monthYieldRate, 4);
        //########################################################################
        $lastQuarter = date("Y-m-d", strtotime("-3 month", strtotime($currentDate)));
        $quarterYieldRate = (self::getYieldRate($db, $currentDate, $lastQuarter) * $this->count * 365) / $this->cost;
        $this->quarterYieldRate = $quarterYieldRate;
        $this->quarterYieldRate = round($this->quarterYieldRate, 4);
        //########################################################################
        $lastYear = date("Y-m-d", strtotime("-1 year", strtotime($currentDate)));
        $yearYieldRate = (self::getYieldRate($db, $currentDate, $lastYear) * $this->count * 365) / $this->cost;
        $this->yearYieldRate = $yearYieldRate;
        $this->yearYieldRate = round($this->yearYieldRate, 4);
    }
    
    /*
     * 此函数获取最大日期和最小日期区间的平均日收益
     * 结果 * 份数 * 365就是年收益，再除以成本就是这几天的年化收益率
     */
    public static function getYieldRate($db, $max_date, $min_date) {
        $sql_query = "SELECT * FROM `product_detail` WHERE code='8193' AND date>='$min_date' AND date<'$max_date'
                ORDER BY `product_detail`.`date` DESC ";
        $query_result = $db->query($sql_query);
        $arr = $db->fetchAll();
        $arr_count = count($arr, COUNT_NORMAL);
        if ($arr_count < 2) {
            return 0;
        }
        
        /*foreach ($arr as $a) {
            Log::debug('$result : ' . $a['date']);
        }*/
        
        $start_date = $arr[$arr_count-1]['date'];
        $end_date = $arr[0]['date'];
        $intervalTime = strtotime($end_date) - strtotime($start_date);
        $intervalDay = $intervalTime / (24*3600);
        Log::verbose('day---------------------------:' . $intervalDay);
        
        $start_worth = $arr[$arr_count-1]['net_worth'];
        $end_worth = $arr[0]['net_worth'];
        $intervalWorth = round(($end_worth - $start_worth), 4);
        Log::verbose('val---------------------------:' . $intervalWorth);
        $yieldRate = $intervalWorth / $intervalDay;
        return $yieldRate;
    }
    
    public function showProductInfo() {
        echo '产品代码：' . $this->code . '</br>';
        echo '产品名称：' . $this->name . '</br>';
        echo '成本：' . $this->cost . '</br>';
        echo '购买日期：' . $this->date . '</br>';
        echo '购买份数：' . $this->count . '</br>';
        echo '当前日期：' . $this->currentDate . '</br>';
        echo '累计收益：' . $this->yields . '</br>';
        echo '总资产：' . $this->assets . '</br>';
        echo '昨日收益：' . $this->dayYield . '</br>';
        echo '七日年化收益率：' . $this->weekYieldRate * 100 . '%' . '</br>';
        echo '单月年化收益率：' . $this->monthYieldRate * 100 . '%' . '</br>';
        echo '季度年化收益率：' . $this->quarterYieldRate * 100 . '%' . '</br>';
        echo '年度年化收益率：' . $this->yearYieldRate * 100 . '%' . '</br>';
    }
    
    public function debugProductInfo() {
        Log::verbose('产品代码：' . $this->code);
        Log::verbose('产品名称：' . $this->name);
        Log::verbose('成本：' . $this->cost);
        Log::verbose('购买日期：' . $this->date);
        Log::verbose('购买份数：' . $this->count);
        Log::verbose('当前日期：' . $this->currentDate);
        Log::verbose('累计收益：' . $this->yields);
        Log::verbose('总资产：' . $this->assets);
        Log::verbose('昨日收益：' . $this->dayYield);
        Log::verbose('七日年化收益率：' . $this->weekYieldRate * 100 . '%');
        Log::verbose('单月年化收益率：' . $this->monthYieldRate * 100 . '%');
        Log::verbose('季度年化收益率：' . $this->quarterYieldRate * 100 . '%');
        Log::verbose('年度年化收益率：' . $this->yearYieldRate * 100 . '%');
    }
}

?>