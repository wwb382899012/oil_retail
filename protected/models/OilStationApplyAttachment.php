<?php

/**
 * This is the model class for table "{{oil_station_apply_attachment}}".
 * The followings are the available columns in table '{{oil_station_apply_attachment}}':
 * @property integer $id
 * @property integer $base_id
 * @property integer $type
 * @property string  $name
 * @property string  $file_path
 * @property string  $file_url
 * @property integer $status
 * @property string  $remark
 * @property integer $create_user_id
 * @property string  $create_time
 * @property integer $update_user_id
 * @property string  $update_time
 */
class OilStationApplyAttachment extends CActiveRecord{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OilStationApplyAttachment the static model class
     */
    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName(){
        return '{{oil_station_apply_attachment}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules(){
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'base_id, type, status, create_user_id, update_user_id',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'name, file_path, file_url, remark',
                'length',
                'max' => 255
            ),
            array(
                'create_time, update_time',
                'safe'
            ),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'id, base_id, type, name, file_path, file_url, status, remark, create_user_id, create_time, update_user_id, update_time',
                'safe',
                'on' => 'search'
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
        return array(
            'id'             => '标识',
            'base_id'        => '关联的主信息Id',
            'type'           => '附件类型',
            'name'           => '附件名称',
            'file_path'      => '物理路径',
            'file_url'       => '地址url',
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
        $criteria->compare('base_id', $this->base_id);
        $criteria->compare('type', $this->type);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('file_path', $this->file_path, true);
        $criteria->compare('file_url', $this->file_url, true);
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

    /**
     * @return array relational rules.
     */
    public function relations(){
        return [
            'createUser'=> [self::BELONGS_TO, SystemUser::class, ['create_user_id'=>'user_id']],
            'updateUser'=> [self::BELONGS_TO, SystemUser::class, ['update_user_id'=>'user_id']],
        ];
    }

    public function beforeSave(){
        if($this->isNewRecord){
            $this->create_time = new CDbExpression("now()");
            $this->create_user_id = Utility::getNowUserId();
        }
        $this->update_time = new CDbExpression("now()");
        $this->update_user_id = Utility::getNowUserId();

        return parent::beforeSave();
    }
}
