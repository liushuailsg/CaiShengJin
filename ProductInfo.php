<?php
class ProductInfo {

    var $ID; //产品代码
    var $name; //产品名称
    var $cost; //成本
    var $count; //份数
    
    var $yields; //累计收益
    var $amount; //总金额
    var $assets; //总资产

    var $dayYield; //当日收益
    var $tenThousandCopiesYields; //万份收益

    var $weekYieldRate; //七日年化收益率
    var $monthYieldRate; //单月年化收益率
    var $quarterYieldRate; //季度年化收益率
    var $yearYieldRate; //年度收益率
//    var $

    public function __construct($PrdCode) {
        $this->ID = $PrdCode;
        $this->name = '天添金稳健型';
        $this->cost = 50000;
        $this->count = 48127.83;
        
    }
    
    public function getAssets($worth) {
        return $this->count * $worth;
    }
}

?>