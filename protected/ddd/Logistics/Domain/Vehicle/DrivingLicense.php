<?php

/**
 * @Name            行驶证
 * @DateTime        2018年9月4日 9:54:21
 * @Author          youyi000
 */
namespace ddd\Logistics\Domain\Vehicle;

use app\ddd\Common\Domain\Value\Attachment;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\DateTime;
use ddd\Infrastructure\error\ZException;

class DrivingLicense extends BaseEntity
{
    #region property
    
    /**
     * 行驶证起效日 
     * @var   Datetime
     */
    public $start_date;
    
    /**
     * 行驶证截至日 
     * @var   Datetime
     */
    public $end_date;
    
    /**
     * 行驶证照片 
     * @var   Attachment[]
     */
    public $photos;    

    #endregion
    
    /**
     * 创建工厂方法
     */
    public static function create($start_date, $end_date, $photos)
    {
        $entity =  new static();
        $entity->start_date = new DateTime($start_date);
        $entity->end_date   = new DateTime($end_date);
        if(!empty($photos) && is_array($photos)){
            foreach ($photos as $photo) {
                $entity->addPhoto(new Attachment($photo->out_id, $photo->file_url));
            }
        }

        return $entity;
    }


    /**
     * 添加照片附件
     */
    public function addPhoto(Attachment $photo)
    {
        if (empty($photo))
        {
            throw new ZException("Attachment对象不存在");
        }

        $this->photos[$photo->id]=$photo;

        return true;
    }
    
    /**
     * 移除照片附件
     * @return   boolean
     */
    public function removePhoto($id)
    {
       unset($this->photos[$id]);
       return true;
    }
    
}

