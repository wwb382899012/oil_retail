<?php

/**
 * This is the model class for table "{{oil_price}}".
 * The followings are the available columns in table '{{oil_price}}':
 * @property integer $price_id
 * @property integer $apply_id
 * @property integer $item_id
 * @property integer $company_id
 * @property integer $station_id
 * @property integer $goods_id
 * @property string  $retail_price
 * @property string  $agreed_price
 * @property string  $discount_price
 * @property string  $effect_time
 * @property string  $end_time
 * @property string  $remark
 * @property integer $status
 * @property string  $status_time
 * @property string  $create_time
 * @property integer $update_user_id
 * @property string  $update_time
 * @property integer $create_user_id
 */
class OilPrice extends CActiveRecord{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OilPrice the static model class
     */
    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName(){
        return '{{oil_price}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules(){
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'apply_id, item_id, company_id, station_id, goods_id, status, update_user_id, create_user_id',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'retail_price, agreed_price, discount_price',
                'length',
                'max' => 20
            ),
            array(
                'remark',
                'length',
                'max' => 255
            ),
            array(
                'effect_time, end_time, status_time, create_time, update_time',
                'safe'
            ),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'price_id, apply_id, item_id, company_id, station_id, goods_id, retail_price, agreed_price, discount_price, effect_time, end_time, remark, status, status_time, create_time, update_user_id, update_time, create_user_id',
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
            'price_id'       => '标识',
            'apply_id'       => '申请id',
            'item_id'        => '申请item',
            'company_id'     => '油企',
            'station_id'     => '油站',
            'goods_id'       => '油品',
            'retail_price'   => '零售价',
            'agreed_price'  => '协议价',
            'discount_price' => '优惠价',
            'effect_time'    => '生效时间',
            'end_time'       => '失效时间',
            'remark'         => '备注',
            'status'         => '状态',
            'status_time'    => '状态时间',
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

        $criteria->compare('price_id', $this->price_id);
        $criteria->compare('apply_id', $this->apply_id);
        $criteria->compare('item_id', $this->item_id);
        $criteria->compare('company_id', $this->company_id);
        $criteria->compare('station_id', $this->station_id);
        $criteria->compare('goods_id', $this->goods_id);
        $criteria->compare('retail_price', $this->retail_price, true);
        $criteria->compare('agreed_price', $this->agreed_price, true);
        $criteria->compare('discount_price', $this->discount_price, true);
        $criteria->compare('effect_time', $this->effect_time, true);
        $criteria->compare('end_time', $this->end_time, true);
        $criteria->compare('remark', $this->remark, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('status_time', $this->status_time, true);
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
        return array(
            'company'=> [self::BELONGS_TO, OilCompany::class,'company_id'],
            'station'=> [self::BELONGS_TO, OilStation::class ,'station_id'],
            'goods'=> [self::BELONGS_TO,OilGoods::class,'goods_id'],
            'createUser'=> [self::BELONGS_TO, SystemUser::class, ['create_user_id'=>'user_id']],
            'updateUser'=> [self::BELONGS_TO, SystemUser::class, ['update_user_id'=>'user_id']],
        );
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
