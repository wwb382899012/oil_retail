<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/28 10:13
 * Describe：额度日志
 */

namespace app\ddd\Qutoa\DTO\LogisticsQuota;


use app\ddd\Admin\Domain\Menu\Menu;
use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;

class LogisticsQuotaLogDTO extends BaseDTO
{
    #region property

    /**
     * 标识
     * @var      int
     */
    public $limit_id = 0;

    /**
     * 时间
     * @var      string
     */
    public $addDate;

    /**
     * 额度明细（元）
     * @var      float
     */
    public $quota = 0;

    /**
     * 编号
     * @var      Status
     */
    protected $code;

    /**
     * 收支类型
     * @var      int
     */
    public $category;


    #endregion

    public function customAttributeNames()
    {
        return array();
    }

    public function fromEntity(BaseEntity $entity)
    {
        $this->setAttributes($entity->getAttributes());

    }

    /**
     * 根据菜单实体创建DTO
     * @return Menu
     * @throws \Exception
     */
    public  function toEntity()
    {
        $entity= new Menu();
        $entity->setAttributes($this->getAttributes());
        return $entity;
    }

}