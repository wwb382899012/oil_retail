<?php
/**
 * Created by youyi000.
 * DateTime: 2018/7/20 18:14
 * Describe：
 */

namespace ddd\Common\Domain\Currency;


use ddd\Common\Domain\IValue;
use ddd\Common\Domain\Value\BaseCommonValue;

class Currency extends BaseCommonValue
{



    #region property

    /**
     * 标识或id
     * @var   int
     */
    public $id;

    /**
     * 名称
     * @var   string
     */
    public $name;

    /**
     * 符号
     * @var   string
     */
    public $symbol;

    #endregion

    public function __construct($id,$name,$symbol="")
    {
        $this->id=$id;
        $this->name=$name;
        $this->symbol=$symbol;
        parent::__construct();
    }

    #region Factory methods


    /**
     * @return Currency
     * @throws \Exception
     */
    public static function createCNY()
    {
        return CurrencyService::create(CurrencyService::CNY);
        //return new Currency(1,"人民币","￥");
    }

    #endregion

    /**
     * 是否相等
     * @param Currency $value
     * @return bool
     */
    public function equals(IValue $value)
    {
        return $this->id===$value->id;
    }


}