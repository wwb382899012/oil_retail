<?php

/**
 * This is the model class for table "t_vehicle_daily_quota".
 * The followings are the available columns in table 't_vehicle_daily_quota':
 * @property integer $id
 * @property integer $vehicle_id
 * @property string  $current_date
 * @property string  $frozen_quota
 * @property string  $used_quota
 * @property integer $status
 * @property string  $remark
 * @property integer $create_user_id
 * @property string  $create_time
 * @property integer $update_user_id
 * @property string  $update_time
 */
class VehicleDailyQuota extends BaseBusinessActiveRecord{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return VehicleDailyQuota the static model class
     */
    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName(){
        return 't_vehicle_daily_quota';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules(){
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('vehicle_id, status, create_user_id, update_user_id', 'numerical', 'integerOnly'=>true),
            array('frozen_quota, used_quota', 'length', 'max'=>10),
            array('remark', 'length', 'max'=>256),
            array('current_date, status_time, effect_time, create_time, update_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, vehicle_id, current_date, frozen_quota, used_quota, status, status_time, remark, create_user_id, effect_time, create_time, update_user_id, update_time', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations(){
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
        return array(
            'id'             => 'id',
            'vehicle_id'     => '车辆id',
            'current_date'   => '日期',
            'frozen_quota'   => '冻结额度',
            'used_quota'     => '已使用额度',
            'status'         => '状态',
            'remark'         => '备注',
            'create_user_id' => '创建用户',
            'create_time'    => '创建时间',
            'update_user_id' => '更新用户',
            'update_time'    => '更新时间',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search(){
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('vehicle_id', $this->vehicle_id);
        $criteria->compare('current_date', $this->current_date, true);
        $criteria->compare('frozen_quota', $this->frozen_quota, true);
        $criteria->compare('used_quota', $this->used_quota, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('remark', $this->remark, true);
        $criteria->compare('create_user_id', $this->create_user_id);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('update_user_id', $this->update_user_id);
        $criteria->compare('update_time', $this->update_time, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}
