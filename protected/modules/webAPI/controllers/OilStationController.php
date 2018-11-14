<?php

use ddd\OilStation\Application\OilStationService;

/**
 * 油站管理
 * Class OilStationController
 */
class OilStationController extends AttachmentController{

    public function pageInit(){
        parent::pageInit();
        $this->attachmentType = Attachment::C_OIL_STATION;
        $this->rightCode = 'oil-station';
        //$this->publicActions = ['list', 'detail','save'];
    }

    /**
     * @api {GET} /webAPI/oilStation/getFile [getFile] 下载文件
     * @apiName getFile
     * @apiGroup WebAPI - OilStation
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} id 文件id <font color=red>必填</font>
     * @apiParam (输入字段) {fileName} fileName 下载时输出的文件名
     * @apiExample {FormData} 输入示例:
     * {
     *      "id":779,
     *      "fileName":'下载时输出的文件名',
     * }
     * @apiSuccessExample {json} 输出示例:
     * 失败返回：
     * {
     *      "state":1,
     *      "data": ""
     * }
     * 成功输出文件
     */
    public function actionGetFile(){
        parent::actionGetFile();
    }

    /**
     * @api {POST} /webAPI/oilStation/list [list] 获取列表
     * @apiName list
     * @apiGroup WebAPI - OilStation
     * @apiVersion 1.0.0
     * @apiParam (输入字段-search) {int} station_id 油站编号
     * @apiParam (输入字段-search) {string} name 油站名称
     * @apiParam (输入字段-search) {string} company_name 所属企业
     * @apiParam (输入字段-search) {string} corporate 油站联系人
     * @apiParam (输入字段-search) {string} address 油站地址
     * @apiParam (输入字段-search) {int} status 油站状态
     * @apiParam (输入字段-search) {int} status 油站状态
     * @apiParam (输入字段-search) {int} province_id 省份id
     * @apiParam (输入字段-search) {int} province_id 城市id
     * @apiExample {FormData} 输入示例:
     * {
     * "page": 1,
     * "pageSize": 2,
     * "search": {
     *      "station_id": "油站编号",
     *      "name": "油站名称",
     *      "company_name": "所属企业",
     *      "company_id": "所属企业id",
     *      "corporate"=> "油站联系人",
     *      "address"=> "油站地址",
     *      "status"=> "油站状态",
     *      "province_id": "省份id",
     *      "city_id": "城市id",
     * }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "state": 0,
     * "data": [
     * {
     * "station_id": "1",
     * "apply_id": "1",
     * "name": "坪洲加油站",
     * "company_id": "1",
     * "province_id": "1",
     * "city_id": "1",
     * "address": "坪洲",
     * "longitude": "1.000000",
     * "latitude": "21.000000",
     * "contact_person": "马云",
     * "contact_phone": "0795-585248",
     * "remark": "备注",
     * "status": "1",
     * "status_time": "2018-09-05 19:41:19",
     * "effect_time": "2018-09-05 19:41:19",
     * "create_time": "2018-09-05 19:41:19",
     * "create_user_id": "1",
     * "update_user_id": "1",
     * "update_time": "2018-09-05 19:41:19",
     * "company_name": "百度科技",
     * "province": "广东",
     * "city": "深圳",
     * "status_name": "",
     * "create_user_name": "",
     * "update_user_name": ""
     * }
     * ]
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data": []
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 成功时返回列表数据
     * @apiParam (输出字段-data-rows) {int} station_id 加油站编号
     * @apiParam (输出字段-data-rows) {string} name 加油站名称
     * @apiParam (输出字段-data-rows) {int} company_id 企业编号
     * @apiParam (输出字段-data-rows) {string} company_name 企业名称
     * @apiParam (输出字段-data-rows) {int} province_id 省份id
     * @apiParam (输出字段-data-rows) {int} province_id 城市id
     * @apiParam (输出字段-data-rows) {string} province 省份
     * @apiParam (输出字段-data-rows) {string} city 城市
     * @apiParam (输出字段-data-rows) {string} address 加油站地址
     * @apiParam (输出字段-data-rows) {string} longitude 经度
     * @apiParam (输出字段-data-rows) {string} latitude 纬度
     * @apiParam (输出字段-data-rows) {string} contact_person 联系人
     * @apiParam (输出字段-data-rows) {string} contact_phone 联系人电话
     * @apiParam (输出字段-data-rows) {string} remark 备注
     * @apiParam (输出字段-data-rows) {int} status 状态
     * @apiParam (输出字段-data-rows) {string} status_name 状态名称
     */
    public function actionList(){
        $data = OilStationService::service()->getListData($this->getSearch(),$this->getSearchPage(),$this->getSearchPageSize());
        if(empty($data)){
            $this->returnSuccess([]);
        }

        foreach($data->data as $key => & $item){
            $item['status_name'] = \Map::getStatusName('oil_station_status', $item['status']);
            $datum['is_can_view'] = true;
        }

        $this->returnSuccess($data);
    }

    /**
     * @api {POST} /webAPI/oilStation/detail [detail] 详情
     * @apiName detail
     * @apiGroup WebAPI - OilStation
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} station_id 油站id
     * @apiExample {FormData} 输入示例:
     * {
     *      "station_id":779
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     *{
     * "state": 0,
     * "data": {
     * "company_id": "1",
     * "name": "百度科技",
     * "short_name": "百度",
     * "tax_code": "584714",
     * "corporate": "李彦宏",
     * "province_id": "4360",
     * "province": "广东",
     * "city_id": "4366",
     * "city": "深圳",
     * "address": "宝安大道",
     * "contact_phone": "0795-852741",
     * "ownership": "1",
     * "build_date": "2018-09-05",
     * "remark": "备注",
     * "status": "0",
     * "status_time": "2018-09-05 18:08:21",
     * "effect_time": "2018-09-05 18:08:21",
     * "create_time": "2018-09-05 18:08:21",
     * "create_user_id": "1",
     * "create_user_name": "创建人",
     * "update_user_id": "1",
     * "update_user_name": "更新人",
     * "update_time": "2018-09-05 18:08:21"
     * }
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 成功时返回数据
     * @apiParam (输出字段-data-rows) {int} station_id 加油站编号
     * @apiParam (输出字段-data-rows) {string} name 加油站名称
     * @apiParam (输出字段-data-rows) {int} company_id 所属企业ID
     * @apiParam (输出字段-data-rows) {string} province 省份
     * @apiParam (输出字段-data-rows) {int} province_id 省份id
     * @apiParam (输出字段-data-rows) {string} city 城市
     * @apiParam (输出字段-data-rows) {int} city_id 城市id
     * @apiParam (输出字段-data-rows) {string} address 加油站地址
     * @apiParam (输出字段-data-rows) {string} longitude 经度
     * @apiParam (输出字段-data-rows) {string} latitude 纬度
     * @apiParam (输出字段-data-rows) {string} contact_person 联系人
     * @apiParam (输出字段-data-rows) {string} contact_phone 联系人电话
     * @apiParam (输出字段-data-rows) {string} remark 备注
     * @apiParam (输出字段-data-rows) {int} status 状态
     * @apiParam (输出字段-data-rows) {array} files 附件
     * @apiParam (输出字段-data-rows-files) {int} id 附件id
     * @apiParam (输出字段-data-rows-files) {string} name 附件名称
     * @apiParam (输出字段-data-rows-files) {int} status 附件状态
     * @apiParam (输出字段-data-rows-files) {string} file_url 附件id
     */
    public function actionDetail(){
        try{
            $station_id = Mod::app()->request->getParam('station_id');
            if(!Utility::checkQueryId($station_id)){
                $this->returnError(RetailError::$PARAMS_PASS_ERROR);
            }

            $dto = OilStationService::service()->getDetailDto($station_id);
            $this->returnSuccess($dto);
        }catch(Exception $exception){
            $this->returnError($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @api {POST} /webAPI/oilStation/onOff [onOff] 详情
     * @apiName detail
     * @apiGroup WebAPI - OilStation
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} station_id 油站id
     * @apiParam (输入字段) {int} state 是否启用,0禁用,1启用
     * @apiExample {FormData} 输入示例:
     * {
     *      "station_id":779,
     *      "state":true
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     *      "state":0,
     *      "data": '操作成功'
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 错误时返回原因
     */
    public function actionOnOff(){
        try{
            $station_id = $this->getRestParam('station_id');
            if(!Utility::checkQueryId($station_id)){
                $this->returnError(RetailError::$PARAMS_PASS_ERROR);
            }
            $state = (bool) $this->getRestParam('state',true);

            $dto = OilStationService::service()->setOnOff($station_id,$state);
            $this->returnSuccess($dto);
        }catch(Exception $exception){
            $this->returnError($exception->getMessage(), $exception->getCode());
        }
    }

}