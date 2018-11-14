<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/28 10:13
 * Describe：车辆
 */

namespace app\ddd\Logistics\DTO\Vehicle;


use app\ddd\Common\Domain\Value\Attachment;
use app\ddd\Common\Domain\Value\LogisticsCompany;
use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\Logistics\Domain\Vehicle\DrivingLicense;
use ddd\Logistics\Domain\Vehicle\Vehicle;

class VehicleDTO extends BaseDTO
{
    #region property

    /**
     * 标识（编号）
     * @var      int
     */
    public $vehicle_id = 0;

    /**
     * 车牌号
     * @var      string
     */
    public $number;

    /**
     * 车型
     * @var      string
     */
    public $model;

    /**
     * 油箱容量
     * @var      float
     */
    public $capacity = 0;

    /**
     * 物流企业id
     * @var int
     */
    public $logistics_id;

    /**
     * 物流企业
     * @var      string
     */
    public $logistics_name;

    /**
     * 添加时间
     * @var      string
     */
    public $add_time;

    /**
     * 添加人
     * @var      string
     */
    public $operator;

    /**
     * 行驶证有效开始日期
     * @var      string
     */
    public $start_date;

    /**
     * 行驶证有效结束日期
     * @var      string
     */
    public $end_date;

    /**
     * 行驶证附件
     * @var      array
     */
    public $files;

    /**
     * 备注
     * @var      string
     */
    public $remark;

    /**
     * 状态
     * @var      int
     */
    public $status;

    /**
     * 状态名
     * @var      int
     */
    public $status_name;

    /**
     * 当日油量
     * @var float
     */
    public $day_capacity;

    /**
     * 剩余油量
     * @var float
     */
    public $balance_capacity;

    #endregion
    
    public function rules()
    {
        return [
            ["logistics_id,number,model,capacity,operator,start_date","required"],
            ['logistics_id','numerical', 'integerOnly'=>true],
            ['capacity','numerical', 'integerOnly'=>false],
            ["end_date","validateDate",],
        ];
    }

    /**
     * 校验日期
     * @param $attribute
     * @param $params
     */
    public function validateDate($attribute, $params)
    {
        if(empty($this->$attribute))
            $this->addError($attribute,"".$attribute."不得为空");

        $startTime = strtotime($this->start_date);
        $endTime   = strtotime($this->end_date);
        // $nowTime   = strtotime(date('Y-m-d'));
        if($endTime < $startTime)
            $this->addError($attribute, '截止日期不得早于开始日期');
        // else if ($endTime < $nowTime)
        //     $this->addError($attribute, '截止日期不得早于当前日期');
        // else if ($nowTime < $startTime)
        //     $this->addError($attribute, '开始日期不得早于当前日期');
    }


    /**
     * 转换成DTO对象
     *  fromEntity
     * @param Vehicle $entity
     * @throws \Exception
     */
    public function fromEntity(BaseEntity $entity)
    {

        $values = $entity->getAttributes(["vehicle_id","number","model","capacity","remark"]);
        $this->setAttributes($values);
        $this->operator       = $entity->optor; 
        $this->status         = $entity->getStatus()->status;
        $this->status_name    = \Map::$v['vehicle_status'][$this->status];
        $this->logistics_id   = $entity->company->id;
        $this->logistics_name = $entity->company->name;
        if(!empty($entity->create_time))
            $this->add_time       = $entity->create_time->toDateTime();
        if(!empty($entity->driving_license))
        {
            if(!empty($entity->driving_license->start_date))
            $this->start_date = $entity->driving_license->start_date->toDate();
            if(!empty($entity->driving_license->end_date))
            $this->end_date   = $entity->driving_license->end_date->toDate();

            $prefixUrl = \Mod::app()->params["file_url"];
            if(!empty($entity->driving_license->photos) && is_array($entity->driving_license->photos)){
                foreach($entity->driving_license->photos as $item){
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

    }

    /**
     * 转换为实体对象
     * @name toEntity
     * @param
     * @throw * @throws \ddd\Logistics\Domain\Vehicle\ZException
     * @return Vehicle
     */
    public  function toEntity()
    {
        $entity= new Vehicle();
        $values = $this->getAttributes(["vehicle_id","number","model","capacity","remark"]);
        $entity->setAttributes($values);
        $entity->setStatus(new Status($this->status));
        $entity->optor   = $this->operator;
        $entity->company = new LogisticsCompany($this->logistics_id, $this->logistics_name);
        
        $photos = [];
        if(!empty($this->files) && is_array($this->files)){
            $prefixUrl = \Mod::app()->params["file_url"];
            foreach ($this->files as $file) {
                if(empty($file['file_id']))
                    continue;
                $fileUrl         = $prefixUrl . $file['file_id'];
                $photo           = new \PhotoAttachment();
                $photo->out_id   = $file['file_id'];
                $photo->file_url = $fileUrl;
                $photos[]        = $photo;
            }
        }
        $drivingLicense  = DrivingLicense::create($this->start_date, $this->end_date, $photos);
        
        $entity->addDrivingLicense($drivingLicense);
        
        return $entity;
    }

}