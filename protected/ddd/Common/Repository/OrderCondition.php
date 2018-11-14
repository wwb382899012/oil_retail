<?php
/**
 * Created by youyi000.
 * DateTime: 2018/9/12 15:55
 * Describe：
 */

namespace app\ddd\Common\Repository;


use ddd\Common\Domain\BaseValue;

class OrderCondition extends BaseValue
{
    const ORDER_BY_ASC="asc";
    const ORDER_BY_DESC="desc";

    public $name;

    public $type="asc";

    public function __construct($name=null,$type="asc")
    {
        $params=[
            "name"=>$name,
            "type"=>$type
        ];
        parent::__construct($params);
    }
}