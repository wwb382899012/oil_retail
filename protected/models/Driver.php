<?php

/**
 * This is the model class for table "{{driver}}".
 * The followings are the available columns in table '{{driver}}':
 * @property integer $driver_id
 * @property integer $customer_id
 * @property string  $name
 * @property string  $password
 * @property integer $logistics_id
 * @property string  $phone
 * @property string  $remark
 * @property integer $status
 * @property string  $effect_time
 * @property integer $create_user_id
 * @property string  $create_time
 * @property integer $update_user_id
 * @property string  $update_time
 */
class Driver extends BaseBusinessActiveRecord
{

    const INVALID_STATUS = 0;
    const EFFECTIVE_STATUS = 1;

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Driver the static model class
     */
    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName(){
        return '{{driver}}';
    }


    /**
     * @return array relational rules.
     */
    public function relations(){
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            "vehicles" => [
                self::MANY_MANY,
                "Vehicle",
                "t_driver_vehicle_relation(driver_id, vehicle_id)"
            ],
            "photos" => [
                self::HAS_MANY,
                "PhotoAttachment",
                ["base_id" => "driver_id"],
                "on" => "photos.status=".PhotoAttachment::EFFECTIVE_STATUS." and photos.type=".PhotoAttachment::DRIVER_PHOTO_TYPE
            ],
            "logisticsCompany"=>[
                self::BELONGS_TO,
                "LogisticsCompany",
                ["logistics_id" => "logistics_id"],

            ],
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
        return array(
            'driver_id'      => '标识id',
            'customer_id'    => '客户id',
            'name'           => '姓名',
            'password'       => '交易密码',
            'logistics_id'   => '企业id',
            'phone'          => '手机号',
            'remark'         => '备注',
            'status'         => '状态',
            'status_time'    => '状态时间',
            'effect_time'    => '生效时间',
            'create_user_id' => '创建用户',
            'create_time'    => '创建时间',
            'update_user_id' => '更新用户',
            'update_time'    => '更新时间',
        );
    }

}
