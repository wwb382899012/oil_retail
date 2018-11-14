<?php

/**
 * This is the model class for table "{{photo_attachment}}".
 *
 * The followings are the available columns in table '{{photo_attachment}}':
 * @property string $id
 * @property string $base_id
 * @property string $name
 * @property string $file_path
 * @property string $file_url
 * @property integer $status
 * @property string $remark
 * @property integer $create_user_id
 * @property string $create_time
 * @property integer $update_user_id
 * @property string $update_time
 */
class PhotoAttachment extends BaseBusinessActiveRecord
{
	const INVALID_STATUS   = 0; //无效状态
	const EFFECTIVE_STATUS = 1; //有效状态

	const DRIVER_PHOTO_TYPE  = 1; //司机附件类型
	const VEHICLE_PHOTO_TYPE = 10; //车辆附件类型

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{photo_attachment}}';
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
			'id' => '标识id',
			'base_id' => '关联id',
			'name' => '附件名',
			'file_path' => '路径',
			'file_url' => '路径url',
			'status' => '状态',
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
	 * @return PhotoAttachment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
