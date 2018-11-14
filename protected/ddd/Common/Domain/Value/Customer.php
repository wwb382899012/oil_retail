<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/24 14:38
 * Describe：
 */

namespace app\ddd\Common\Domain\Value;


use ddd\Common\Domain\BaseValue;

class Customer extends BaseValue
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
     * 手机号
     * @var string
     */
    public $phone;

    /**
     * 交易密码
     * @var string
     */
    public $password;

    #endregion

    public function __construct($id = 0, $name = "", $phone="", $password="", array $params = null)
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
        if (!empty($phone))
        {
            $this->phone = $phone;
        }
        if (!empty($password))
        {
            $this->password = $password;
        }
    }
}