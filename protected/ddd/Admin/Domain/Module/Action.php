<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/24 14:34
 * Describe：
 */

namespace app\ddd\Admin\Domain\Module;


use ddd\Common\Domain\BaseValue;

class Action extends BaseValue
{
    #region property

    /**
     * 操作名称
     * @var   string
     */
    public $name;

    /**
     * 操作码
     * @var   string
     */
    public $code;

    #endregion

    public function __construct($name,$code,array $params = null)
    {
        parent::__construct($params);
        $this->name=$name;
        $this->code=$code;
    }


    public function __sleep()
    {
        // TODO: Implement __sleep() method.
        return ["name","code"];
    }


}