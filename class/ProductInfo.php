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
    
    public static function getInstance($db, $id) {
        $sql_query = "SELECT * FROM `product_info` WHERE _id='$id'";
        $query_result = $db->query($sql_query);
        $arr = $db->fetchAll();
        $arr_count = count($arr, COUNT_NORMAL);
        Log::verbose('ProductInfo getInstance $arr_count length is ' . $arr_count);
        if ($arr_count != 0) {
            $mProductInfo = new ProductInfo($arr[0]['code'], $arr[0]['name'], $arr[0]['cost'], $arr[0]['date']);
            return $mProductInfo;
        }
    }
    
    public function setCurrentDate($date) {
        $this->currentDate = date("Y-m-d", strtotime($date));
    }
    
    public function getProductInfo($db) {
        $sql_query = "SELECT * FROM `product_detail` WHERE code='$this->code' AND date='$this->date'
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
        
        $sql_query = "SELECT * FROM `product_detail` WHERE code='$this->code' AND date>='$this->date' AND date<'$currentDate'
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
            $this->weekYieldRate = round($arr[0]['weekYieldRate'] * $this->count / $this->cost, 4);
            $this->monthYieldRate = round($arr[0]['monthYieldRate'] * $this->count / $this->cost, 4);
            $this->quarterYieldRate = round($arr[0]['quarterYieldRate'] * $this->count / $this->cost, 4);
            $this->yearYieldRate = round($arr[0]['yearYieldRate'] * $this->count / $this->cost, 4);
        }
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