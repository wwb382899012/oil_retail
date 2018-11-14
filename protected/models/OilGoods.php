<?php

/**
 * This is the model class for table "{{goods}}".
 * The followings are the available columns in table '{{goods}}':
 * @property integer $goods_id
 * @property string  $name
 * @property string  $code
 * @property integer $order_index
 * @property string  $remark
 * @property integer $status
 * @property string  $status_time
 * @property string  $effect_time
 * @property string  $create_time
 * @property integer $create_user_id
 * @property integer $update_user_id
 * @property string  $update_time
 */
class OilGoods extends CActiveRecord{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OilGoods the static model class
     */
    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName(){
        return '{{oil_goods}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules(){
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'order_index, status, create_user_id, update_user_id',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'name, code',
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
                'goods_id, name, code, order_index, remark, status, status_time, effect_time, create_time, create_user_id, update_user_id, update_time',
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
            'goods_id'       => '标识',
            'name'           => '名称',
            'code'           => '编码',
            'order_index'    => '排序',
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

        $criteria->compare('goods_id', $this->goods_id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('code', $this->code, true);
        $criteria->compare('order_index', $this->order_index);
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
            'createUser'=> [self::BELONGS_TO, SystemUser::class, ['create_user_id'=>'user_id']],
            'updateUser'=> [self::BELONGS_TO, SystemUser::class, ['update_user_id'=>'user_id']],
            'price'=> [self::BELONGS_TO, OilPrice::class, 'goods_id'],
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
