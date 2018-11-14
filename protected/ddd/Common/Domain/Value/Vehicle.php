<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/4 0004
 * Time: 14:32
 */

namespace app\ddd\Common\Domain\Value;


use ddd\Common\Domain\BaseValue;

class Vehicle extends BaseValue
{
    #region property

    /**
     * 标识或id
     * @var   int
     */
    public $id;

    /**
     * 车牌
     * @var   string
     */
    public $number;

    /**
     * 型号
     * @var string
     */
    public $model;

    #endregion

    public function __construct($id = 0, $number = "", $model = '', array $params = null)
    {
        parent::__construct($params);
        if (!empty($id))
        {
            $this->id = $id;
        }
        if (!empty($number))
        {
            $this->number = $number;
        }
        if (!empty($model))
        {
            $this->model = $model;
        }
    }
}