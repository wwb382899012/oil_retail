<?php
/**
 * Created by youyi000.
 * DateTime: 2018/9/12 15:46
 * Describe：
 */

namespace app\ddd\Common\Repository;


use ddd\Common\Domain\BaseValue;

class SearchCondition extends BaseValue
{
    /**
     * 相等
     */
    const COMPARE_METHOD_EQUAL="=";

    /**
     * 不相等
     */
    const COMPARE_METHOD_NOT_EQUAL="=";

    /**
     * 相似
     */
    const COMPARE_METHOD_LIKE="like";

    const COMPARE_METHOD_LAGER=">";

    const COMPARE_METHOD_SMALLER="<";

    const COMPARE_METHOD_LAGER_AND_EQUAL=">=";
    const COMPARE_METHOD_SMALLER_AND_EQUAL="<=";

    public $name;

    public $value;

    public $compareMethod="=";

    public function __construct($name=null,$value=null,$compareMethod="=")
    {
        $params=[
            "name"=>$name,
            "value"=>$value,
            "compareMethod"=>$compareMethod,
        ];
        parent::__construct($params);
    }

    public function getSqlCondition($tablePrefix=null)
    {

    }


}