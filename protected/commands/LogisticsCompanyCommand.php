<?php

use app\ddd\Logistics\Application\LogisticsCompany\LogisticsCompanyService;
use ddd\Infrastructure\DIService;

class LogisticsCompanyCommand extends AMQPCommand{
    /**
     * 需要监听的队列信息
     * @var array
     */
    protected $queueConfig = array(
        AMQPService::QUEUE_LOGISTICS_COMPANY_TO_FINANCE_SYSTEM => array(
            "fn"         => "pushLogisticsCompanyToFinanceSystem",
            "exchange"   => "oil.retail.direct",
            "routingKey" => AMQPService::QUEUE_LOGISTICS_COMPANY_TO_FINANCE_SYSTEM,
        ),
    );

    public function init(){
        $this->sleepTime = 1;
        $this->maxTaskPerChild = 2000;
        parent::init();
    }

    /**
     * 推送物流企业信息
     * @param $logisticsId
     * @throws \ddd\Infrastructure\error\ZException
     * @throws \ddd\Infrastructure\error\ZModelNotExistsException
     */
    public function pushLogisticsCompanyToFinanceSystem($logisticsId){
        if (!Utility::checkQueryId($logisticsId) || $logisticsId < 0){
            return;
        }

        $entity = DIService::getRepository(LogisticsCompanyService::class)->getEntityById($logisticsId);

        $params['logi_code'] = $entity->getId();
        $params['logistics_name'] = $entity->name ?? '';
        $params['status'] = $entity->isActive() ? 1 : 2;
        $params['address'] = $entity->address ?? '';
        $params['remark'] = $entity->remark ?? '';

        $systemUrl = Mod::app()->params['oil_finance_url'];
        $postUrl = $systemUrl . '/interface/saveLogisticsDetail';

        Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 推送物流企业信息到财务系统入参:' . json_encode($params) . ' || 接口地址:' . $postUrl);
        $res = Utility::postCMD($params, $postUrl);
        Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 推送物流企业信息到财务系统结果:' . json_encode($res));

        if (!isset($res['code']) || $res['code'] != 0){
            throw new Exception('接口调用失败！');
        }
    }
}