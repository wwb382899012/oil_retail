<?php

/**
 * This is the model class for table "{{vehicle_daily_quota_log}}".
 *
 * The followings are the available columns in table '{{vehicle_daily_quota_log}}':
 * @property integer $log_id
 * @property integer $vehicle_id
 * @property string $current_date
 * @property integer $category
 * @property integer $method
 * @property integer $relation_id
 * @property string $quota
 * @property string $quota_total
 * @property integer $status
 * @property string $status_time
 * @property string $remark
 * @property integer $create_user_id
 * @property string $effect_time
 * @property string $create_time
 * @property integer $update_user_id
 * @property string $update_time
 */
class VehicleDailyQuotaLog extends BaseBusinessActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{vehicle_daily_quota_log}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('vehicle_id, category, method, status, create_user_id, update_user_id', 'numerical', 'integerOnly'=>true),
            array('relation_id', 'length', 'max'=>11),
            array('quota, quota_total', 'length', 'max'=>10),
            array('remark', 'length', 'max'=>256),
            array('current_date, status_time, effect_time, create_time, update_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('log_id, vehicle_id, current_date, category, method, relation_id, quota, quota_total, status, status_time, remark, create_user_id, effect_time, create_time, update_user_id, update_time', 'safe', 'on'=>'search'),
        );
    }

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'log_id' => 'id',
			'vehicle_id' => '车辆id',
			'current_date' => '日期',
			'category' => '10：订单支付',
			'method' => '1：增加 -1：减少',
			'relation_id' => '关联id',
			'quota' => '变更额度',
			'quota_total' => '当前总额度',
			'status' => '状态',
			'status_time' => '状态时间',
			'remark' => '备注',
			'create_user_id' => '创建用户',
			'effect_time' => '生效时间',
			'create_time' => '创建时间',
			'update_user_id' => '更新用户',
			'update_time' => '更新时间',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('log_id',$this->log_id);
		$criteria->compare('vehicle_id',$this->vehicle_id);
		$criteria->compare('current_date',$this->current_date,true);
		$criteria->compare('category',$this->category);
		$criteria->compare('method',$this->method);
		$criteria->compare('relation_id',$this->relation_id);
		$criteria->compare('quota',$this->quota,true);
		$criteria->compare('quota_total',$this->quota_total,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('status_time',$this->status_time,true);
		$criteria->compare('remark',$this->remark,true);
		$criteria->compare('create_user_id',$this->create_user_id);
		$criteria->compare('effect_time',$this->effect_time,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_user_id',$this->update_user_id);
		$criteria->compare('update_time',$this->update_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return VehicleDailyQuotaLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
