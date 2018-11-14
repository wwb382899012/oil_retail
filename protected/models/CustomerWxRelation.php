<?php

/**
 * This is the model class for table "{{customer_wx_relation}}".
 *
 * The followings are the available columns in table '{{customer_wx_relation}}':
 * @property integer $id
 * @property integer $customer_id
 * @property string $wx_identity
 * @property string $open_id
 * @property string $create_time
 * @property integer $create_user_id
 * @property string $update_time
 * @property integer $update_user_id
 */
class CustomerWxRelation extends BaseBusinessActiveRecord
{
	const MINI_PROGRAM       = 1;
	const ENTERPRISE_ACCOUNT = 2;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{customer_wx_relation}}';
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
			'id' => '标识',
			'customer_id' => '用户id',
			'wx_identity' => '小程序或企业号标识',
			'open_id' => '微信标识',
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
	 * @return CustomerWxRelation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

}
