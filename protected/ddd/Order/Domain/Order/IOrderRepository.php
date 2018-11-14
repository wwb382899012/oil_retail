<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/7 0007
 * Time: 14:24
 */

namespace ddd\Order\Domain\Order;

use ddd\Common\Domain\IRepository;

interface IOrderRepository extends IRepository
{
    /**
     * 获取客户订单信息
     * @param $customerId
     * @return Order[]
     */
    public function getByCustomerId($customerId);

    /**
     * 获取油站订单信息
     * @param $stationId
     * @return Order[]
     */
    public function getByOilStationId($stationId);

    /**
     * 订单生效
     * @param Order $order
     * @return mixed
     */
    public function effect(Order $order);

    /**
     * 订单失败
     * @param Order $order
     * @return mixed
     */
    public function failed(Order $order);
}