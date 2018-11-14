<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/7 0007
 * Time: 14:27
 */

namespace ddd\Order\Domain\Order;


use app\ddd\Common\Domain\Value\Customer;
use app\ddd\Common\Domain\Value\LogisticsCompany;
use app\ddd\Common\Domain\Value\OilCompany;
use app\ddd\Common\Domain\Value\OilGoods;
use app\ddd\Common\Domain\Value\OilStation;
use app\ddd\Common\Domain\Value\Operator;
use app\ddd\Common\Domain\Value\Status;
use app\ddd\Common\Domain\Value\Vehicle;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\DateTime;
use ddd\Common\IAggregateRoot;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ExceptionService;
use ddd\Infrastructure\IDService;
use ddd\Infrastructure\Utility;
use ddd\Logistics\Domain\Driver\IDriverRepository;
use ddd\Logistics\Domain\LogisticsCompany\ILogisticsCompanyRepository;
use ddd\Logistics\Domain\Vehicle\IVehicleRepository;
use ddd\OilStation\Domain\OilCompany\IOilCompanyRepository;
use ddd\OilStation\Domain\OilGoods\IOilGoodsRepository;
use ddd\OilStation\Domain\OilStation\IOilStationRepository;
use ddd\Order\DTO\DomainParams\OrderParamsDTO;

class Order extends BaseEntity implements IAggregateRoot
{
    use OrderRepository;

    /**
     * 标识
     * @var   OrderId
     */
    private $order_id;

    /**
     * 编号
     * @var string
     */
    public $code;

    /**
     * 用户
     * @var   Customer
     */
    public $customer;

    /**
     * 车辆
     * @var   Vehicle
     */
    public $vehicle;

    /**
     * 油站
     * @var   OilStation
     */
    public $oil_station;

    /**
     * 油品
     * @var   OilGoods
     */
    public $goods;

    /**
     * 升数
     * @var   float
     */
    public $quantity = 0;

    /**
     * 采购单价
     * @var   int
     */
    public $price_buy = 0;

    /**
     * 销售价格
     * @var   int
     */
    public $price_sell = 0;

    /**
     * 零售价
     * @var int
     */
    public $price_retail = 0;

    /**
     * 油企
     * @var   OilCompany
     */
    public $oil_company;

    /**
     * 物流企业
     * @var   LogisticsCompany
     */
    public $logistics;

    /**
     * 订单类型
     * @var   string
     */
    public $order_type;

    /**
     * 备注
     * @var   string
     */
    public $remark;

    /**
     * 状态
     * @var   Status
     */
    protected $status;

    /**
     * 生效时间
     * @var   Datetime
     */
    public $effect_time;

    /**
     * 创建时间
     * @var   Datetime
     */
    public $create_time;

    /**
     * 更新用户
     * @var   Operator
     */
    public $update_user;

    /**
     * 更新时间
     * @var   Datetime
     */
    public $update_time;

    /**
     * 创建用户
     * @var   Operator
     */
    public $create_user;


    /**
     * 失败原因
     * @var string
     */
    public $failed_reason;

    /**
     * 生效事件之后
     */
    const EVENT_EFFECTED = "onEffected";

    /**
     * 失败事件之后
     */
    const EVENT_FAILED = "onFailed";

    /**
     * 事件配置，事件名必须以on开头，否则无效
     * @return array
     */
    protected function events()
    {
        return [
            static::EVENT_EFFECTED,
            static::EVENT_FAILED,
        ];
    }

    function getId()
    {
        return $this->order_id;
    }

    function setId($value)
    {
        $this->order_id = $value;
    }

    protected function generateCode() {
        $this->code = 'D'. IDService::getOrderCodeId();
    }

    /**
     * 创建
     * @param OrderParamsDTO $dto
     * @return Order
     * @throws \Exception
     */
    public static function create(OrderParamsDTO $dto)
    {
        if (empty($dto))
        {
            ExceptionService::throwArgumentNullException(OrderParamsDTO::class, array('class' => get_called_class(), 'function' => __FUNCTION__));
        }
        $entity = new static();

        $customer = DIService::getRepository(IDriverRepository::class)->findById($dto->customer_id);
        if (empty($customer))
        {
            ExceptionService::throwBusinessException(BusinessError::Customer_Not_Exist, ['customer_id' => $dto->customer_id]);
        }

        if ($dto->customer_trans_password != $customer->password)
        {
            ExceptionService::throwBusinessException(BusinessError::Customer_Trans_Password_Error, ['customer_id' => $dto->customer_id]);
        }

        $oilStation = DIService::getRepository(IOilStationRepository::class)->findById($dto->station_id);
        if (empty($oilStation))
        {
            ExceptionService::throwBusinessException(BusinessError::Oil_Station_Not_Exist, ['station_id' => $dto->station_id]);
        }

        $driverEntity = DIService::getRepository(IDriverRepository::class)->findById($dto->customer_id);
        if (empty($driverEntity))
        {
            ExceptionService::throwBusinessException(BusinessError::Driver_Not_Exist, ['customer_id' => $dto->customer_id]);
        }
        $entity->generateCode();
        $entity->customer = new Customer($dto->customer_id);
        $entity->vehicle = new Vehicle($dto->vehicle_id);
        $entity->oil_station = new OilStation($dto->station_id);
        $entity->goods = new OilGoods($dto->goods_id);
        $entity->price_buy = $dto->price_buy;
        $entity->price_sell = $dto->price_sell;
        $entity->price_retail = $dto->price_retail;
        $entity->quantity = $dto->quantity;
        $entity->remark = $dto->remark;
        $entity->setStatus(new Status(OrderStatusEnum::Status_New, Utility::getDateTime(), '新建'));
        $entity->create_time = new DateTime();
        $entity->oil_company = $oilStation->getCompany();
        $entity->logistics = $driverEntity->company;

        return $entity;
    }

    /**
     * 是否可生效
     * @return bool
     */
    public function isCanEffect()
    {
        return $this->getStatusValue() == OrderStatusEnum::Status_New;
    }

    /**
     * 是否可付款
     * @return bool
     */
    public function isCanPayment()
    {
        return $this->getStatusValue() == OrderStatusEnum::Status_New;
    }

    /**
     * 订单生效
     * @param    boolean $persistent
     * @throws   \Exception
     */
    public function setEffect($persistent = true)
    {
        if ($this->isCanEffect())
        {
            $this->effect_time = Utility::getDateTime();
            $this->status = new Status(OrderStatusEnum::Status_Effected, Utility::getDateTime(), '已生效');
            if ($persistent)
            {
                $this->getOrderRepository()->effect($this);
            }

            $this->publishEvent(static::EVENT_EFFECTED, new OrderEffectedEvent($this));
        } else
        {
            ExceptionService::throwBusinessException(BusinessError::Order_Status_Not_Allow_Effect);
        }
    }

    /**
     * 是否可置为失败
     * @return bool
     */
    public function isCanFailed()
    {
        return $this->getStatusValue() == OrderStatusEnum::Status_New;
    }

    /**
     * 订单失败
     * @param bool $persistent
     * @throws \Exception
     */
    public function setFailed($persistent = true)
    {
        if ($this->isCanFailed())
        {
            $this->status = new Status(OrderStatusEnum::Status_Failed, Utility::getDateTime(), '失败');
            if ($persistent)
            {
                $this->getOrderRepository()->failed($this);
            }

            $this->publishEvent(static::EVENT_FAILED, new OrderFailedEvent($this));
        } else
        {
            ExceptionService::throwBusinessException(BusinessError::Order_Status_Not_Allow_Failed);
        }
    }

    /**
     * 设置状态
     * @param    Status $status
     */
    public function setStatus(Status $status)
    {
        $this->status = $status;
    }

    /**
     * 获取状态
     * @return   Status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * 获取状态值
     * @return int
     */
    public function getStatusValue()
    {
        return $this->status->status;
    }

    /**
     * 获取订单采购总价
     * @return   int
     */
    public function getOrderBuyAmount()
    {
        return round($this->quantity * $this->price_buy);
    }

    /**
     * 获取订单销售总价
     * @return int
     */
    public function getOrderSellAmount()
    {
        return round($this->quantity * $this->price_sell);
    }

    public function rules()
    {
        return [
            ['customer', 'validateCustomer'],
            ['vehicle', 'validateVehicle'],
            ['oil_station', 'validateOilStation'],
            ['goods', 'validateGoods'],
            ['quantity', 'validateQuantity'],
        ];
    }

    /**
     * 用户有效性校验
     * @param $attribute
     * @throws \Exception
     */
    public function validateCustomer($attribute)
    {
        $customer = $this->$attribute;
        if (empty($customer))
        {
            $this->addError($attribute, '司机不能为空！');
        }

        $driverEntity = DIService::getRepository(IDriverRepository::class)->findById($customer->id);
        if (empty($driverEntity))
        {
            $this->addError($attribute, '当前司机:' . $customer->id . '，不存在！');
        }

        if (!$driverEntity->isActive())
        {
            $this->addError($attribute, '司机账号失效，请联系所在物流公司');
        }

        if (empty($driverEntity->company))
        {
            $this->addError($attribute, '当前司机:' . $customer->id . '，所属物流企业不能为空！');
        }

        $logistics = DIService::getRepository(ILogisticsCompanyRepository::class)->findById($driverEntity->company->id);
        if (empty($logistics))
        {
            $this->addError($attribute, '当前司机:' . $customer->id . '，所属物流企业:' . $driverEntity->company->id . '，不存在！');
        }

        if (!$logistics->isActive())
        {
            $this->addError($attribute, '物流企业账号失效，请联系所在物流公司');
        }

        if (\Utility::isEmpty($driverEntity->vehicle_items))
        {
            $this->addError($attribute, '当前司机:' . $customer->id . '，名下车辆为空！');
        }

        $flag = false;
        foreach ($driverEntity->vehicle_items as $vehicle)
        {
            if ($this->vehicle->id == $vehicle->id)
            {
                $flag = true;
                break;
            }
        }

        if (!$flag)
        {
            $this->addError($attribute, '车辆失效，请联系所在物流公司');
        }
    }

    /**
     * 验证车辆信息
     * @param $attribute
     * @throws \Exception
     */
    public function validateVehicle($attribute)
    {
        $vehicle = $this->$attribute;
        if (empty($vehicle))
        {
            $this->addError($attribute, '车辆不能为空！');
        }

        $vehicleEntity = DIService::getRepository(IVehicleRepository::class)->findById($vehicle->id);
        if (empty($vehicleEntity))
        {
            $this->addError($attribute, '当前车辆:' . $vehicle->id . '，不存在！');
        }

        if (!$vehicleEntity->isActive())
        {
            $this->addError($attribute, '车辆失效，请联系所在物流公司');
        }
    }

    /**
     * 验证油站信息
     * @param $attribute
     * @throws \Exception
     */
    public function validateOilStation($attribute)
    {
        $oilStation = $this->$attribute;
        if (empty($oilStation))
        {
            $this->addError($attribute, '油站不能为空！');
        }

        $oilStationEntity = DIService::getRepository(IOilStationRepository::class)->findById($oilStation->id);
        if (empty($oilStationEntity))
        {
            $this->addError($attribute, '当前油站:' . $oilStation->id . '，不存在！');
        }

        if (!$oilStationEntity->isActive())
        {
            $this->addError($attribute, '当前油站已禁用');
        }

        if (empty($oilStationEntity->getCompany()))
        {
            $this->addError($attribute, '当前油站:' . $oilStation->id . '，所属油企不能为空！');
        }

        $oilCompanyId = $oilStationEntity->getCompany()->getId();
        $oilCompany = DIService::getRepository(IOilCompanyRepository::class)->findById($oilCompanyId);
        if (empty($oilCompany))
        {
            $this->addError($attribute, '当前油站:' . $oilStation->id . '，所属油企:' . $oilCompanyId . '，不存在！');
        }

        if (!$oilCompany->isActive())
        {
            $this->addError($attribute, '当前油企不可用');
        }
    }

    /**
     * 验证油品
     * @param $attribute
     * @throws \Exception
     */
    public function validateGoods($attribute)
    {
        $goods = $this->$attribute;
        if (empty($goods))
        {
            $this->addError($attribute, '油品不能为空！');
        }

        $goodsEntity = DIService::getRepository(IOilGoodsRepository::class)->findById($goods->id);
        if (empty($goodsEntity))
        {
            $this->addError($attribute, '当前油品:' . $goods->id . '不存在！');
        }

        if (!$goodsEntity->isActive())
        {
            $this->addError($attribute, '当前油品不可用！');
        }
    }

    /**
     * 验证加油升数
     * @param $attribute
     * @throws \Exception
     */
    public function validateQuantity($attribute)
    {
        $quantity = $this->$attribute;
        if (empty($quantity))
        {
            $this->addError($attribute, '加油数量不能为空！');
        }

        try
        {
            $maxQuantityLimit = DIService::get(OrderService::class)->getVehicleMaxOilQuantity($this->customer->id, $this->oil_station->id, $this->goods->id, $this->vehicle->id, $this->price_sell);
            if (empty($maxQuantityLimit) || \Utility::isEmpty($maxQuantityLimit[0]->items))
            {
                $this->addError($attribute, '获取车辆最大可加油量为空！');
            }

            $vehicleMaxQuantity = $maxQuantityLimit[0]->items[0]->max_available_quantity;
            if (bccomp($quantity, $vehicleMaxQuantity, 2) === 1)
            {
                $this->addError($attribute, '当前车辆加油升数已超出当日可用升数');
            }
        } catch (\Exception $e)
        {
            $this->addError($attribute, $e->getMessage());
        }
    }
}