<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/28 10:13
 * Describe：司机
 */

namespace app\ddd\Logistics\DTO\Driver;


use app\ddd\Common\Domain\Value\Attachment;
use app\ddd\Common\Domain\Value\LogisticsCompany;
use app\ddd\Common\Domain\Value\Status;
use app\ddd\Logistics\DTO\Vehicle\VehicleDTO;
use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\Logistics\Domain\Driver\Driver;

class DriverDTO extends BaseDTO
{
    #region property

    /**
     * 标识（编号）
     * @var      int
     */
    public $id = 0;

    /**
     * 客户id
     * @var      int
     */
    public $customer_id = 0;

    /**
     * 姓名
     * @var      string
     */
    public $name;


    /**
     * 物流企业id
     * @var      string
     */
    public $logistics_id;

    /**
     * 物流企业
     * @var      string
     */
    public $logistics_name;

    /**
     * 手机号
     * @var      string
     */
    public $phone ;

    /**
     * 交易密码
     * @var   string
     */
    public $password ;

    /**
     * 状态
     * @var int
     */
    public $status;

    /**
     * 状态名
     * @var      string
     */
    public $status_name;

    /**
     * 驾驶证照片附件
     * @var      array
     */
    public $files;

    /**
     * 车辆信息
     * @var VehicleDTO[]
     */
    public $vehicles;


    #endregion

    public function rules()
    {
        return [
            ["name","required"],
            ["logistics_id","numerical", "allowEmpty" => false, "integerOnly" => true, "min" => 1, "message" => "物流企业id{attribute}必须为大于0的数字", 'on'=>'auto'],
            ["status","numerical", "allowEmpty" => false, "integerOnly" => true,"message"=>"状态{attribute}必须为数字"],
            ['password','match','pattern'=>'/^\d{6}$/','message'=>'{attribute}交易密码必须是6位数字'],
            ['phone','match','pattern'=>'/^1[345678]{1}\d{9}$/','message'=>'{attribute}请填写正确的手机号'],
        ];
    }

    /**
     * 转换为DTO对象
     * @name fromEntity
     * @param * @param BaseEntity $entity
     * @throw
     * @return void
     */
    public function fromEntity(BaseEntity $entity)
    {
        $values = $entity->getAttributes(["id","phone","name","password","customer_id"]);
        $this->setAttributes($values);
        $this->status         = $entity->getStatus()->status;
        $this->status_name    = \Map::$v['driver_status'][$this->status];
        $this->logistics_id   = $entity->company->id;
        $this->logistics_name = $entity->company->name;

        $prefixUrl = \Mod::app()->params["file_url"];
        if(!empty($entity->photos) && is_array($entity->photos)){
            foreach($entity->photos as $item){
                if(empty($item->id))
                    continue;
                $fileUrl    = $prefixUrl . $item->id;
                $attachment = array();
                $attachment['file_id']  = $item->id;
                $attachment['file_url'] = $fileUrl;
                $this->files[]          = $attachment;
            }
        }


    }

    /**
     * 转换为实体对象
     * @name toEntity
     * @param
     * @throw * @throws \ddd\Logistics\Domain\Driver\ZException
     * @return Driver
     */
    public function toEntity()
    {
        $entity= new Driver();
        $values = $this->getAttributes(["id","phone","name","password","customer_id"]);
        $entity->setAttributes($values);
        $entity->setStatus(new Status($this->status));
        $entity->company = new LogisticsCompany($this->logistics_id, $this->logistics_name);
        $prefixUrl = \Mod::app()->params["file_url"];
        if(!empty($this->files) && is_array($this->files)){
            foreach ($this->files as $file) {
                if(empty($file['file_id']))
                    continue;
                $fileUrl = $prefixUrl . $file['file_id'];
                $entity->addPhoto(new Attachment($file['file_id'],$fileUrl));
            }
        }
        
        return $entity;
    }

}