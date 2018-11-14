<?php

/**
 * This is the model class for table "{{logistics_company}}".
 *
 * The followings are the available columns in table '{{logistics_company}}':
 * @property integer $logistics_id
 * @property string $name
 * @property integer $out_identity
 * @property integer $out_status
 * @property string $tax_code
 * @property string $phone
 * @property string $address
 * @property integer $status
 * @property integer $status_time
 * @property integer $effect_time
 * @property string $remark
 * @property integer $create_user_id
 * @property string $create_time
 * @property integer $update_user_id
 * @property string $update_time
 */
class LogisticsCompany extends BaseBusinessActiveRecord
{
	//P端状态
	const INVALID_STATUS = 0; //无效
	const EFFECTIVE_STATUS = 1; //有效
	
	//B端银管家状态
	const INVALID_OUT_STATUS = 1; //冻结
	const EFFECTIVE_OUT_STATUS = 0; //有效
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 't_logistics_company';
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			"logisticsCreditQuota"=>array(self::HAS_ONE, "LogisticsCreditQuota", "logistics_id"),//企业授信额度信息
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'logistics_id' => '物流企业id',
			'name' => '企业名称',
			'out_identity' => '银管家标识',
			'out_status' => '银管家状态',
			'tax_code' => '纳税人识别号',
			'phone' => '电话',
			'address' => '注册地址',
			'status' => '状态',
			'status_time' => '状态时间',
			'effect_time' => '生效时间',
			'remark' => '备注',
			'create_user_id' => '创建用户',
			'create_time' => '创建时间',
			'update_user_id' => '更新用户',
			'update_time' => '更新时间',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return LogisticsCompany the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
