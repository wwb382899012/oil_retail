<?php

/**
 * This is the model class for table "t_logistics_credit_quota".
 * The followings are the available columns in table 't_logistics_credit_quota':
 * @property integer $id
 * @property integer $logistics_id
 * @property integer $credit_quota
 * @property string  $start_date
 * @property string  $end_date
 * @property integer $status
 * @property string  $remark
 * @property integer $create_user_id
 * @property string  $create_time
 * @property integer $update_user_id
 * @property string  $update_time
 */
class LogisticsCreditQuota extends BaseBusinessActiveRecord{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return LogisticsCreditQuota the static model class
     */
    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName(){
        return 't_logistics_credit_quota';
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
            'logistics_id'   => '物流企业id',
            'credit_quota'   => '授信额度',
            'start_date'     => '开始日期',
            'end_date'       => '结束日期',
            'status'         => '状态',
            'remark'         => '备注',
            'create_user_id' => '创建用户',
            'create_time'    => '创建时间',
            'update_user_id' => '更新用户',
            'update_time'    => '更新时间',
        );
    }

}
