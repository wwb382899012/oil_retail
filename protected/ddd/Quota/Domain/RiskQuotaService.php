<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/6 0006
 * Time: 10:28
 */

namespace ddd\Quota\Domain;


use ddd\Common\Domain\BaseService;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\ExceptionService;
use ddd\Quota\Domain\LogisticsQuota\ILogisticsDailyQuotaRepository;
use ddd\Quota\Domain\LogisticsQuota\ILogisticsQuotaRepository;
use ddd\Quota\Domain\LogisticsQuota\LogisticsDailyQuota;
use ddd\Quota\Domain\LogisticsQuota\LogisticsDailyQuotaService;
use ddd\Quota\Domain\LogisticsQuota\LogisticsQuota;
use ddd\Quota\Domain\LogisticsQuota\LogisticsQuotaService;
use ddd\Quota\Domain\VehicleQuota\IVehicleDailyQuotaRepository;
use ddd\Quota\Domain\VehicleQuota\VehicleDailyQuota;
use ddd\Quota\Domain\VehicleQuota\VehicleDailyQuotaService;
use ddd\Quota\DTO\DomainParams\LogisticsRepayParamsDTO;
use ddd\Quota\DTO\DomainParams\OrderPaymentParamsDTO;

class RiskQuotaService extends BaseService
{
    /**
     * 调整物流企业额度
     * @param $logisticsId
     * @param $amount
     * @param $category
     * @param $relationId
     * @param $remark
     * @throws \Exception
     */
    private function updateLogisticsQuota($logisticsId, $amount, $category, $relationId, $remark)
    {
        $service = DIService::get(LogisticsQuotaService::class);
        $logisticsQuotaEntity = DIService::getRepository(ILogisticsQuotaRepository::class)->findByLogisticsId($logisticsId);
        if (empty($logisticsQuotaEntity))
        {
            //物流企业额度不存在时，新增
            //$logisticsQuotaEntity = $service->createLogisticsQuota($logisticsId);
            ExceptionService::throwEntityInstanceNotExistsException($logisticsId, LogisticsQuota::class);
        }
        $service->updateQuota($logisticsQuotaEntity, $amount, $category, $relationId, $remark);
    }

    /**
     * 调整物流企业当日额度
     * @param $logisticsId
     * @param $amount
     * @param $category
     * @param $relationId
     * @param $remark
     * @throws \Exception
     */
    private function updateLogisticsDailyQuota($logisticsId, $amount, $category, $relationId, $remark)
    {
        $service = DIService::get(LogisticsDailyQuotaService::class);
        $logisticsDailyQuotaEntity = DIService::getRepository(ILogisticsDailyQuotaRepository::class)->findByLogisticsId($logisticsId);
        if (empty($logisticsDailyQuotaEntity))
        {
            //物流企业当日额度不存在时，新增
            //$logisticsDailyQuotaEntity = $service->createLogisticsDailyQuota($logisticsId);
            ExceptionService::throwEntityInstanceNotExistsException($logisticsId, LogisticsDailyQuota::class);
        }
        $service->updateQuota($logisticsDailyQuotaEntity, $amount, $category, $relationId, $remark);
    }

    /**
     * 调整车辆当日额度
     * @param $vehicleId
     * @param $quantity
     * @param $category
     * @param $relationId
     * @param $remark
     * @throws \Exception
     */
    private function updateVehicleDailyQuota($vehicleId, $quantity, $category, $relationId, $remark)
    {
        $service = DIService::get(VehicleDailyQuotaService::class);

        $vehicleDailyQuotaEntity = DIService::getRepository(IVehicleDailyQuotaRepository::class)->findByVehicleId($vehicleId);
        if (empty($vehicleDailyQuotaEntity))
        {
            //车辆当日额度不存在时，新增
            //$vehicleDailyQuotaEntity = $service->createVehicleDailyQuota($vehicleId);
            ExceptionService::throwEntityInstanceNotExistsException($vehicleId, VehicleDailyQuota::class);
        }
        $service->updateQuota($vehicleDailyQuotaEntity, $quantity, $category, $relationId, $remark);
    }

    /**
     * 当订单付款时，调整相关额度
     *      -- 减少物流企业额度
     *      -- 减少物流企业当日额度
     *      -- 减少车辆当日额度
     * @param    OrderPaymentParamsDTO $paramsDTO
     * @throws   \Exception
     */
    public function onOrderPayment(OrderPaymentParamsDTO $paramsDTO)
    {
        if (!empty($paramsDTO))
        {
            //调整物流企业额度
            $this->updateLogisticsQuota($paramsDTO->logistics_id, $paramsDTO->amount, $paramsDTO->category, $paramsDTO->relation_id, $paramsDTO->remark);

            //调整物流企业当日额度
            $this->updateLogisticsDailyQuota($paramsDTO->logistics_id, $paramsDTO->amount, $paramsDTO->category, $paramsDTO->relation_id, $paramsDTO->remark);

            //调整车辆当日额度
            $this->updateVehicleDailyQuota($paramsDTO->vehicle_id, $paramsDTO->quantity, $paramsDTO->category, $paramsDTO->relation_id, $paramsDTO->remark);
        } else
        {
            ExceptionService::throwArgumentNullException("OrderPaymentParamsDTO对象", array('class' => get_class($this), 'function' => __FUNCTION__));
        }
    }

    /**
     * 当物流企业还款时
     *      -- 增加物流企业额度
     * @param LogisticsRepayParamsDTO $paramsDTO
     * @throws \Exception
     */
    public function onLogisticsRepay(LogisticsRepayParamsDTO $paramsDTO)
    {
        if (!empty($paramsDTO))
        {
            //调整物流企业额度
            $this->updateLogisticsQuota($paramsDTO->logistics_id, $paramsDTO->amount, $paramsDTO->category, $paramsDTO->relation_id, $paramsDTO->remark);
        } else
        {
            ExceptionService::throwArgumentNullException("LogisticsRepayParamsDTO对象", array('class' => get_class($this), 'function' => __FUNCTION__));
        }
    }
}