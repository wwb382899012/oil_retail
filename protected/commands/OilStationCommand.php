<?php

use ddd\Infrastructure\DIService;
use ddd\OilStation\Application\OilStationService;

class OilStationCommand extends AMQPCommand{

    /**
     * 需要监听的队列信息
     * @var array
     */
    protected $queueConfig = array(
        AMQPService::QUEUE_OIL_STATION_TO_FINANCE_SYSTEM => array(
            "fn"         => "pushStationInfoToFinanceSystem",
            "exchange"   => "oil.retail.direct",
            "routingKey" => AMQPService::QUEUE_OIL_STATION_TO_FINANCE_SYSTEM,
        ),
    );

    public function init(){
        $this->sleepTime = 1;
        $this->maxTaskPerChild = 2000;
        parent::init();
    }

    /**
     * 推送油站信息到财务系统
     * @param $stationId
     * @throws Exception
     */
    public function pushStationInfoToFinanceSystem($stationId):void {
        if (!Utility::checkQueryId($stationId) || $stationId < 0){
            return;
        }

        $oilStationEntity = DIService::getRepository(OilStationService::class)->getEntityById($stationId);

        $params['oil_code'] = $oilStationEntity->getId();
        $params['oil_name'] = $oilStationEntity->getName();
        $params['oil_parent_code'] = $oilStationEntity->getCompanyId();
        $params['oil_parent_name'] = $oilStationEntity->getCompanyName();
        $params['city'] = $oilStationEntity->getCityName();
        $params['address'] = $oilStationEntity->getAddress();
        $params['remark'] = $oilStationEntity->getRemark();
        $params['status'] = $oilStationEntity->isActive() ? 1 : 2;

        $systemUrl = Mod::app()->params['oil_finance_url'];
        $postUrl = $systemUrl . '/interface/saveOilDetail';

        Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 推送油站信息到财务系统入参:' . json_encode($params) . ' || 接口地址:' . $postUrl);
        $res = Utility::postCMD($params, $postUrl);
        Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 推送油站信息到财务系统结果:' . json_encode($res));

        if (!isset($res['code']) || $res['code'] != 0){
            throw new Exception('接口调用失败！');
        }
    }
}