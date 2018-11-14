<?php

/**
 * This is the model class for table "{{oil_station_apply}}".
 * The followings are the available columns in table '{{oil_station_apply}}':
 * @property integer $apply_id
 * @property string  $name
 * @property integer $company_id
 * @property integer $province_id
 * @property integer $city_id
 * @property string  $address
 * @property string  $longitude
 * @property string  $latitude
 * @property string  $contact_person
 * @property string  $contact_phone
 * @property string  $remark
 * @property integer $status
 * @property string  $status_time
 * @property string  $effect_time
 * @property string  $create_time
 * @property integer $create_user_id
 * @property integer $update_user_id
 * @property string  $update_time
 */
class OilStationApply extends CActiveRecord{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OilStationApply the static model class
     */
    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName(){
        return '{{oil_station_apply}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules(){
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'company_id, province_id, city_id, status, create_user_id, update_user_id',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'name, address',
                'length',
                'max' => 255
            ),
            array(
                'longitude, latitude',
                'length',
                'max' => 13
            ),
            array(
                'contact_person, contact_phone',
                'length',
                'max' => 32
            ),
            array(
                'remark',
                'length',
                'max' => 512
            ),
            array(
                'status_time, effect_time, create_time, update_time',
                'safe'
            ),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'apply_id, name, company_id, province_id, city_id, address, longitude, latitude, contact_person, contact_phone, remark, status, status_time, effect_time, create_time, create_user_id, update_user_id, update_time',
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
            'apply_id'       => '标识',
            'name'           => '油站名称',
            'company_id'     => '油企ID',
            'province_id'    => '所在省份',
            'city_id'        => '所在城市',
            'address'        => '地址',
            'longitude'      => '经度',
            'latitude'       => '纬度',
            'contact_person' => '联系人',
            'contact_phone'  => '联系电话',
            'remark'         => '备注',
            'status'         => '状态',
            'status_time'    => '状态时间',
            'effect_time'    => '生效时间',
            'create_time'    => '创建时间',
            'create_user_id' => '创建用户',
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

        $criteria->compare('apply_id', $this->apply_id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('company_id', $this->company_id);
        $criteria->compare('province_id', $this->province_id);
        $criteria->compare('city_id', $this->city_id);
        $criteria->compare('address', $this->address, true);
        $criteria->compare('longitude', $this->longitude, true);
        $criteria->compare('latitude', $this->latitude, true);
        $criteria->compare('contact_person', $this->contact_person, true);
        $criteria->compare('contact_phone', $this->contact_phone, true);
        $criteria->compare('remark', $this->remark, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('status_time', $this->status_time, true);
        $criteria->compare('effect_time', $this->effect_time, true);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('create_user_id', $this->create_user_id);
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
            'company'=> [self::BELONGS_TO,'OilCompany','company_id'],
            'province'=>[self::BELONGS_TO,'AreaCode',['province_id' => 'area_code']],
            'city'=>[self::BELONGS_TO,'AreaCode',['city_id' => 'area_code']],
            'files'=> [self::HAS_MANY, OilStationApplyAttachment::class,['base_id' => 'apply_id'],'on'=> 'files.status = 1'],
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
