<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/4 0004
 * Time: 14:32
 */

namespace app\ddd\Common\Domain\Value;


use ddd\Common\Domain\BaseValue;

class OilStation extends BaseValue
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
     * 地址
     * @var string
     */
    public $address;

    #endregion

    public function __construct($id = 0, $name = "", $address = "", array $params = null)
    {
        parent::__construct($params);
        if (!empty($id))
        {
            $this->id = $id;
        }
        if (!empty($name))
        {
            $this->name = $name;
        }
        if (!empty($address))
        {
            $this->address = $address;
        }
    }
}