<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/24 14:38
 * Describe：
 */

namespace app\ddd\Common\Domain\Value;


use ddd\Common\Domain\BaseValue;

class Operator extends BaseValue
{
   #region property

    /**
     * 标识或id
     * @var   int
     */
    public $id = 0;

    /**
     * 名称
     * @var   string
     */
    public $name = '';

    #endregion

    public function __construct($id,$name='',array $params = null)
    {
        parent::__construct($params);
        $this->id=$id;
        if(!empty($name)){
            $this->name=$name;
        }
    }

}