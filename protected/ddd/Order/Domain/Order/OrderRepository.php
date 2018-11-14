<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/7 0007
 * Time: 17:47
 */

namespace ddd\Order\Domain\Order;


use ddd\Infrastructure\DIService;

trait OrderRepository
{
    /**
     * @var IOrderRepository
     */
    protected $orderRepository;

    /**
     * @desc 订单仓储
     * @return IOrderRepository
     * @throws \Exception
     */
    public function getOrderRepository()
    {
        if(empty($this->orderRepository)) {
            $this->orderRepository = DIService::getRepository(IOrderRepository::class);
        }

        return $this->orderRepository;
    }
}