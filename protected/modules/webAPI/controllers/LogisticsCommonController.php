<?php

class LogisticsCommonController extends WebAPIController{

    public function pageInit() {
        parent::pageInit();
        $this->authorizedActions = ['dropDownListMap'];
    }

    /**
     * @api {GET} webAPI/logisticsCommon/dropDownListMap 获取下拉列表map
     * @apiName webAPI - logisticsCommon
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     *{
     *    "code": 0,
     *    "data":[
     *      'logistics_company_status' => [], //物流企业状态
     *      'logistics_company_out_status' => [], //物流企业银管家状态
     *      'driver_status' => [], //司机状态
     *      'logistics_quota_status' => [], //额度状态
     *      'logistics_quota_log_category' => [], //额度变更原因
     * ]
     *}
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup admin - common
     * @apiVersion 1.0.0
     */
    public function actionDropDownListMap(){
        $data = [
            'logistics_company_status'=>\Map::getKeyValueObject('logistics_company_status'),
            'logistics_company_out_status'=>\Map::getKeyValueObject('logistics_company_out_status'),
            'driver_status'=>\Map::getKeyValueObject('driver_status'),
            'logistics_quota_status'=>\Map::getKeyValueObject('logistics_quota_status'),
            'logistics_quota_log_category'=>\Map::getKeyValueObject('logistics_quota_log_category'),
        ];

        $this->returnSuccess($data);
    }

}