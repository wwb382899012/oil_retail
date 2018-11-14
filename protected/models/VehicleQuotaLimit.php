<?php

/**
 * This is the model class for table "t_vehicle_quota_limit".
 * The followings are the available columns in table 't_vehicle_quota_limit':
 * @property integer $limit_id
 * @property string  $code
 * @property string  $rate
 * @property integer $status
 * @property string  $remark
 * @property integer $create_user_id
 * @property string  $create_time
 * @property integer $update_user_id
 * @property string  $update_time
 */
class VehicleQuotaLimit extends BaseBusinessActiveRecord{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return VehicleQuotaLimit the static model class
     */
    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName(){
        return 't_vehicle_quota_limit';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules(){
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'status, create_user_id, update_user_id',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'code',
                'length',
                'max' => 32
            ),
            array(
                'rate',
                'length',
                'max' => 20
            ),
            array(
                'remark',
                'length',
                'max' => 256
            ),
            array(
                'create_time, update_time',
                'safe'
            ),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'limit_id, code, rate, status, remark, create_user_id, create_time, update_user_id, update_time',
                'safe',
                'on' => 'search'
            ),
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
     * Retrieves a list of models based on the current search/filter conditions.
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    /*public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('limit_id',$this->limit_id);
        $criteria->compare('code',$this->code,true);
        $criteria->compare('rate',$this->rate,true);
        $criteria->compare('status',$this->status);
        $criteria->compare('remark',$this->remark,true);
        $criteria->compare('create_user_id',$this->create_user_id);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('update_user_id',$this->update_user_id);
        $criteria->compare('update_time',$this->update_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }*/

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
        return array(
            'limit_id' => 'id',
            'code' => '编号',
            'rate' => '当日油箱占比',
            'status' => '状态',
            'remark' => '备注',
            'create_user_id' => '创建用户',
            'create_time' => '创建时间',
            'update_user_id' => '更新用户',
            'update_time' => '更新时间',
        );
    }
}
