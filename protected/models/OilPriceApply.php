<?php

/**
 * This is the model class for table "{{oil_price_apply}}".
 * The followings are the available columns in table '{{oil_price_apply}}':
 * @property integer $apply_id
 * @property string  $code
 * @property string  $remark
 * @property integer $status
 * @property string  $status_time
 * @property string  $effect_time
 * @property string  $create_time
 * @property integer $update_user_id
 * @property string  $update_time
 * @property integer $create_user_id
 */
class OilPriceApply extends CActiveRecord{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OilPriceApply the static model class
     */
    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName(){
        return '{{oil_price_apply}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules(){
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'status, update_user_id, create_user_id',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'code',
                'length',
                'max' => 100
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
                'apply_id, code, remark, status, status_time, effect_time, create_time, update_user_id, update_time, create_user_id',
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
            'code'           => '编号',
            'remark'         => '备注',
            'status'         => '状态',
            'status_time'    => '状态时间',
            'effect_time'    => '生效时间',
            'create_time'    => '创建时间',
            'update_user_id' => '更新用户',
            'update_time'    => '更新时间',
            'create_user_id' => '创建用户',
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
        $criteria->compare('code', $this->code, true);
        $criteria->compare('remark', $this->remark, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('status_time', $this->status_time, true);
        $criteria->compare('effect_time', $this->effect_time, true);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('update_user_id', $this->update_user_id);
        $criteria->compare('update_time', $this->update_time, true);
        $criteria->compare('create_user_id', $this->create_user_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @return array relational rules.
     */
    public function relations(){
        return [
            'files'=> [self::HAS_MANY, OilPriceApplyAttachment::class,['base_id' => 'apply_id'],'on'=> 'files.status = 1'],
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
