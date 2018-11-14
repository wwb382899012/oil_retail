<?php

/**
 * This is the model class for table "{{oil_company}}".
 * The followings are the available columns in table '{{oil_company}}':
 * @property integer $company_id
 * @property string  $name
 * @property string  $short_name
 * @property string  $tax_code
 * @property string  $corporate
 * @property string  $address
 * @property string  $contact_phone
 * @property integer $ownership
 * @property string  $build_date
 * @property string  $remark
 * @property integer $status
 * @property string  $status_time
 * @property string  $effect_time
 * @property string  $create_time
 * @property integer $create_user_id
 * @property integer $update_user_id
 * @property string  $update_time
 */
class OilCompany extends CActiveRecord{
    /**
     * @return string the associated database table name
     */
    public function tableName(){
        return '{{oil_company}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules(){
        return array(
            array(
                'ownership, status, create_user_id, update_user_id',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'name, short_name, tax_code, corporate, contact_phone',
                'length',
                'max' => 100
            ),
            array(
                'address',
                'length',
                'max' => 256
            ),
            array(
                'remark',
                'length',
                'max' => 512
            ),
            array(
                'build_date, status_time, effect_time, create_time, update_time',
                'safe'
            ),
            array(
                'company_id, name, short_name, tax_code, corporate, address, contact_phone, ownership, build_date, remark, status, status_time, effect_time, create_time, create_user_id, update_user_id, update_time',
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
            'company_id'     => '标识ID',
            'name'           => '油企名称',
            'short_name'     => '企业简称',
            'tax_code'       => '纳税人识别号',
            'corporate'      => '法人代表',
            'address'        => '地址',
            'contact_phone'  => '联系电话',
            'ownership'      => '1,国有
            2,民营',
            'build_date'     => '成立日期',
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

        $criteria->compare('company_id', $this->company_id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('short_name', $this->short_name, true);
        $criteria->compare('tax_code', $this->tax_code, true);
        $criteria->compare('corporate', $this->corporate, true);
        $criteria->compare('address', $this->address, true);
        $criteria->compare('contact_phone', $this->contact_phone, true);
        $criteria->compare('ownership', $this->ownership);
        $criteria->compare('build_date', $this->build_date, true);
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
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OilPhone the static model class
     */
    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    /**
     * @return array relational rules.
     */
    public function relations(){
        return [
            'files'=> [self::HAS_MANY, OilCompanyAttachment::class,['base_id' => 'company_id'],'on'=> 'files.status = 1'],
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
