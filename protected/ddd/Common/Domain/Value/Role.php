<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/29 11:42
 * Describe：
 */

namespace app\ddd\Common\Domain\Value;


use ddd\Common\Domain\BaseValue;

class Role extends BaseValue
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

    #endregion

    public function __construct($id=0,$name="",array $params = null)
    {
        parent::__construct($params);
        if(!empty($id))
            $this->id=$id;
        if(!empty($name))
            $this->name=$name;
    }
}