<?php

/**
 * This is the model class for table "{{order}}".
 *
 * The followings are the available columns in table '{{order}}':
 * @property string $order_id
 * @property string $code
 * @property integer $customer_id
 * @property integer $vehicle_id
 * @property integer $station_id
 * @property integer $goods_id
 * @property string $quantity
 * @property integer $price_buy
 * @property integer $price_sell
 * @property integer $price_retail
 * @property integer $oil_company_id
 * @property integer $logistics_id
 * @property string $failed_reason
 * @property string $effect_time
 * @property integer $status
 * @property string $status_time
 * @property string $remark
 * @property integer $create_user_id
 * @property string $create_time
 * @property integer $update_user_id
 * @property string $update_time
 */
class Order extends BaseBusinessActiveRecord
{
    const STATUS_FAILED = -1; //失败

    const STATUS_NEW = 0; //新建

    const STATUS_EFFECTED = 10; //已生效

    const PAY_TYPE_QUOTA = 1; //信用支付

    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{order}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
        return array(
            array('customer_id, vehicle_id, station_id, goods_id, price_buy, price_sell, price_retail, oil_company_id, logistics_id, status, create_user_id, update_user_id', 'numerical', 'integerOnly'=>true),
            array('code', 'length', 'max'=>32),
            array('quantity', 'length', 'max'=>20),
            array('failed_reason', 'length', 'max'=>128),
            array('remark', 'length', 'max'=>256),
            array('effect_time, status_time, create_time, update_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('order_id, code, customer_id, vehicle_id, station_id, goods_id, quantity, price_buy, price_sell, price_retail, oil_company_id, logistics_id, failed_reason, effect_time, status, status_time, remark, create_user_id, create_time, update_user_id, update_time', 'safe', 'on'=>'search'),
        );
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
            "goods" => array(self::BELONGS_TO, "OilGoods", "goods_id", 'select'=>'name'),//油品
            "driver" => array(self::BELONGS_TO, "Driver", "customer_id", 'select'=>'name,phone'),//司机
            "logistics" => array(self::BELONGS_TO, "LogisticsCompany", "logistics_id", 'select'=>'name'),//物流企业
            "vehicle" => array(self::BELONGS_TO, "Vehicle", "vehicle_id", 'select'=>'number,model'),//车辆
            "station" => array(self::BELONGS_TO, "OilStation", "station_id", 'select'=>'name,address'),//油站
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'order_id' => '订单id',
			'code' => '订单编号',
			'customer_id' => '用户id',
			'vehicle_id' => '车辆id',
			'station_id' => '油站id',
			'goods_id' => '油品id',
			'quantity' => '数量',
			'price_buy' => '采购单价',
			'price_sell' => '销售单价',
			'price_retail' => '零售单价',
			'oil_company_id' => '油企id',
			'logistics_id' => '物流企业id',
			'failed_reason' => '订单失败原因',
			'effect_time' => '生效时间',
			'status' => '状态',
			'status_time' => '状态时间',
			'remark' => '备注',
			'create_user_id' => '创建用户',
			'create_time' => '创建时间',
			'update_user_id' => '更新用户',
			'update_time' => '更新时间',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('order_id',$this->order_id,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('customer_id',$this->customer_id);
		$criteria->compare('vehicle_id',$this->vehicle_id);
		$criteria->compare('station_id',$this->station_id);
		$criteria->compare('goods_id',$this->goods_id);
		$criteria->compare('quantity',$this->quantity,true);
		$criteria->compare('price_buy',$this->price_buy);
		$criteria->compare('price_sell',$this->price_sell);
		$criteria->compare('price_retail',$this->price_retail);
		$criteria->compare('oil_company_id',$this->oil_company_id);
		$criteria->compare('logistics_id',$this->logistics_id);
		$criteria->compare('failed_reason',$this->failed_reason,true);
		$criteria->compare('effect_time',$this->effect_time,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('status_time',$this->status_time,true);
		$criteria->compare('remark',$this->remark,true);
		$criteria->compare('create_user_id',$this->create_user_id);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_user_id',$this->update_user_id);
		$criteria->compare('update_time',$this->update_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Order the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
