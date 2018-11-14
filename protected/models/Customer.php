<?php

/**
 * This is the model class for table "{{customer}}".
 *
 * The followings are the available columns in table '{{customer}}':
 * @property integer $id
 * @property string $account
 * @property string $phone
 * @property string $register_time
 * @property integer $login_count
 * @property string $login_time
 * @property string $token
 * @property string $remark
 * @property integer $status
 * @property string $create_time
 * @property integer $create_user_id
 * @property string $update_time
 * @property integer $update_user_id
 */
class Customer extends BaseBusinessActiveRecord
{
	const INVALID_STATUS   = 0;
	const EFFECTIVE_STATUS = 1;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{customer}}';
	}


	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			"driver"=>array(self::HAS_ONE, "Driver", "customer_id"),//客户关联司机信息
			"wxRelation"=>array(self::HAS_MANY, "CustomerWxRelation", "customer_id"),//客户关联微信号
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '标识',
			'account' => '帐号',
			'phone' => '手机号',
			'register_time' => '注册时间',
			'login_count' => '登录次数',
			'login_time' => '最后登录时间',
			'token' => '登录标识',
			'remark' => '备注',
			'status' => '状态',
			'create_time' => '创建时间',
			'create_user_id' => '创建用户',
			'update_time' => '更新时间',
			'update_user_id' => '更新用户',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Customer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
