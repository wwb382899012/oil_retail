<?php

/**
 * This is the model class for table "{{vehicle}}".
 * The followings are the available columns in table '{{vehicle}}':
 * @property integer $vehicle_id
 * @property integer $logistics_id
 * @property string  $number
 * @property string  $model
 * @property string  $optor
 * @property string  $capacity
 * @property string  $start_date
 * @property string  $end_date
 * @property string  $remark
 * @property integer $status
 * @property string  $status_time
 * @property string  $effect_time
 * @property integer $create_user_id
 * @property string  $create_time
 * @property integer $update_user_id
 * @property string  $update_time
 */
class Vehicle extends BaseBusinessActiveRecord
{
    const TRASH_STATUS    = -9;//作废
    const BACK_STATUS     = -1; // 驳回
    const NEW_STATUS      = 0; //新建
    const CHECKING_STATUS = 1; //审核中
    const PASS_STATUS     = 2; //审核通过

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Vehicle the static model class
     */
    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName(){
        return '{{vehicle}}';
    }

    
    /**
     * @return array relational rules.
     */
    public function relations(){
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            "logisticsCompany"=>[
                self::BELONGS_TO,
                "LogisticsCompany",
                ["logistics_id" => "logistics_id"],

            ],
            "photos" => [
                self::HAS_MANY,
                "PhotoAttachment",
                ["base_id" => "vehicle_id"],
                "on" => "photos.status=".PhotoAttachment::EFFECTIVE_STATUS." and photos.type=".PhotoAttachment::VEHICLE_PHOTO_TYPE
            ],
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
        return array(
            'vehicle_id'     => '标识',
            'logistics_id'   => '企业id',
            'number'         => '车牌号',
            'model'          => '车型',
            'optor'          => '添加人',
            'capacity'       => '邮箱容量',
            'start_date'     => '行驶证起效日',
            'end_date'       => '行驶证的截止日',
            'remark'         => '备注',
            'status'         => '状态',
            'status_time'    => '生效时间',
            'effect_time'    => '生效时间',
            'create_user_id' => '创建用户',
            'create_time'    => '创建时间',
            'update_user_id' => '更新用户',
            'update_time'    => '更新时间',
        );
    }

}
