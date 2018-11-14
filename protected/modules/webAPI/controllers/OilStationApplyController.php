<?php

use ddd\OilStation\Application\OilStationApplyService;

/**
 * 油站准入管理
 * Class OilStationApplyController
 */
class OilStationApplyController extends AttachmentController{

    public function pageInit(){
        parent::pageInit();
        $this->attachmentType = Attachment::C_OIL_STATION_APPLY;
        $this->rightCode = 'oil-station-apply';
        $this->authorizedActions=['saveFile','getFile'];
        //$this->publicActions = [ 'list','detail','save'];
    }

    /**
     * @api {POST} /webAPI/oilStationApply/saveFile [saveFile] 附件上传
     * @apiName saveFile
     * @apiGroup WebAPI - OilStationApply
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} id 标志id
     * @apiParam (输入字段) {int} type 类型，1是附件
     * @apiParam (输入字段) {arr} files 文件信息
     * @apiExample {FormData} 输入示例:
     * {
     *      "id":779,
     *      "type"=>1,
     *      "files"=>[]
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "state": 0,
     * "data": {
     * "id": 1,
     * "type": 0,
     * "name": "test",
     * "status": 1,
     * "url": "/xxx/xx/test.pdf"
     * }
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 成功时返回附件id
     */
    public function actionSaveFile(){
        parent::actionSaveFile();
    }

    /**
     * @api {GET} /webAPI/oilStationApply/delFile [delFile] 附件删除
     * @apiName delFile
     * @apiGroup WebAPI - OilStationApply
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} id 文件id
     * @apiExample {FormData} 输入示例:
     * {
     *      "id":779,
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     *      "state":0,
     *      "data": 1
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data": ""
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 成功时返回附件id
     */
    public function actionDelFile(){
        parent::actionDelFile();
    }

    /**
     * @api {GET} /webAPI/oilStationApply/getFile [getFile] 下载文件
     * @apiName getFile
     * @apiGroup WebAPI - OilStationApply
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
     * @api {POST} /webAPI/oilStationApply/list [list] 获取油站申请列表
     * @apiName list
     * @apiGroup WebAPI - OilStationApply
     * @apiVersion 1.0.0
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
     *      "name": "油站名称",
     *      "company_id": "所属企业id",
     *      "company_name": "所属企业",
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
     * "apply_id": "1",
     * "name": "坪洲加油站",
     * "apply_id": "1",
     * "province_id": "1",
     * "city_id": "1",
     * "address": "坪洲",
     * "longitude": "1.000000",
     * "latitude": "21.000000",
     * "contact_person": "马云",
     * "contact_phone": "0795-585248",
     * "remark": "备注",
     * "status": "1",
     * "company_name": "百度科技",
     * "province_name": "广东",
     * "city_name": "深圳",
     * "status_name": "",
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
     * @apiParam (输出字段-data-rows) {int} apply_id 加申请id
     * @apiParam (输出字段-data-rows) {string} name 加油站名称
     * @apiParam (输出字段-data-rows) {int} apply_id 企业编号
     * @apiParam (输出字段-data-rows) {string} company_name 企业名称
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
        $search = $this->getSearch();
        $attr = array(
            'os.apply_id'  => $search['station_id'],
            'oc.company_id' => $search['company_id'],
            'os.name*'       => $search['name'],
            'oc.name*'       => $search['company_name'],
            'os.corporate*'  => $search['corporate'],
            'os.address*'    => $search['address'],
            'os.province_id' => $search['province_id'],
            'os.city_id'     => $search['city_id'],
            'os.status'      => $search['status']
        );
        $where = $this->getWhereSql($attr);

        $sql = <<<SQL
SELECT {col} FROM t_oil_station_apply AS os 
LEFT JOIN t_oil_company AS oc ON os.company_id = oc.company_id 
LEFT JOIN t_area_code AS ac ON os.province_id = ac.area_code 
LEFT JOIN t_area_code AS acb ON os.city_id = acb.area_code 
$where ORDER BY os.apply_id DESC
SQL;

        $fields = [
            'os.apply_id,os.name,os.company_id,os.province_id,os.city_id,os.address,os.longitude,os.latitude',
            'os.contact_person,os.contact_phone,os.status,oc.name AS company_name,ac.area_name AS province_name,acb.area_name AS city_name',
        ];

        $data = $this->getPageData($sql, implode(',', $fields));

        if(empty($data)){
            $this->returnSuccess([]);
        }

        foreach($data->data as $key => & $item){
            $item['status_name'] = \Map::getStatusName('oil_station_apply_status', $item['status']);
            $item['is_can_view'] = true;
            $item['is_can_edit'] = OilStationApplyService::service()->isCanEdit($item['status']);
        }

        $this->returnSuccess($data);
    }

    /**
     * @api {POST} /webAPI/oilStationApply/save [save] 保存油站申请
     * @apiName save
     * @apiGroup WebAPI - OilStationApply
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {bool} is_submit 是否提交
     * @apiParam (输入字段) {int} apply_id 申请id
     * @apiParam (输入字段) {string} name 加油站名称
     * @apiParam (输入字段) {int} apply_id 所属企业ID
     * @apiParam (输入字段) {int} province_id 省份id
     * @apiParam (输入字段) {int} city_id 城市id
     * @apiParam (输入字段) {string} address 加油站地址
     * @apiParam (输入字段) {string} longitude 经度
     * @apiParam (输入字段) {string} latitude 纬度
     * @apiParam (输入字段) {string} contact_person 联系人
     * @apiParam (输入字段) {string} contact_phone 联系人电话
     * @apiParam (输入字段) {string} remark 备注
     * @apiParam (输入字段) {int} status 状态
     * @apiParam (输入字段) {array} files 附件
     * @apiParam (输入字段-files) {int} id 附件id
     * @apiParam (输入字段-files) {string} name 附件名称
     * @apiParam (输入字段-files) {int} status 附件状态
     * @apiParam (输入字段-files) {string} file_url 附件id
     * @apiExample {FormData} 输入示例:
     *{
     * "apply_id": "1",
     * "name": "百度科技",
     * "short_name": "企业简称",
     * "tax_code": "584714",
     * "corporate": "李彦宏",
     * "address": "宝安大道",
     * "province_id": "4433",
     * "city_id": "4436",
     * "contact_phone": "0795-852741",
     * "ownership": "1",
     * "build_date": "2018-09-05",
     * "remark": "备注",
     * "status": "0",
     * "files":[
     * "id": 2,
     * "name": "附件2",
     * "status": 1,
     * "file_url": "/xxx/xx/test.pdf",
     * "is_submit": false,
     * ]
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     *      "state":1,
     *      "data": "操作成功!"
     * }
     * 失败返回：
     * {
     *      "state": 1,
     *      "data": "失败信息"
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 成功时返回申请id
     */
    public function actionSave(){
        try{
            $isSubmit = $this->getRestParam('is_submit', false);
            $reqData = $this->getRestParams();

            $dto = OilStationApplyService::service()->assetDto($reqData);
            if(!$dto->validate()){
                $this->returnError($dto->getErrors());
            }

            $entity = OilStationApplyService::service()->save($dto->toEntity(),$isSubmit);

            $this->returnSuccess($entity->getId());
        }catch(Exception $e){
            $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    public function actionDelete(){
        //TODO:
    }

    /**
     * @api {POST} /webAPI/oilStationApply/detail [detail] 油站申请详情
     * @apiName detail
     * @apiGroup WebAPI - OilStationApply
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} apply_id 申请id
     * @apiExample {FormData} 输入示例:
     * {
     *      "apply_id":779
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     *{
     * "state": 0,
     * "data": {
     * "apply_id": "1",
     * "name": "百度科技",
     * "short_name": "百度",
     * "tax_code": "584714",
     * "corporate": "李彦宏",
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
     * @apiParam (输出字段-data-rows) {int} apply_id 加申请id
     * @apiParam (输出字段-data-rows) {string} name 加油站名称
     * @apiParam (输出字段-data-rows) {int} apply_id 所属企业ID
     * @apiParam (输出字段-data-rows) {string} address 加油站地址
     * @apiParam (输出字段-data-rows) {string} longitude 经度
     * @apiParam (输出字段-data-rows) {string} latitude 纬度
     * @apiParam (输出字段-data-rows) {string} contact_person 联系人
     * @apiParam (输出字段-data-rows) {string} contact_phone 联系人电话
     * @apiParam (输出字段-data-rows) {string} remark 备注
     * @apiParam (输出字段-data-rows) {int} status 状态
     * @apiParam (输出字段-data-rows) {string} status_name 状态名称
     * @apiParam (输出字段-data-rows) {bool} is_can_submit 是否可提交
     * @apiParam (输出字段-data-rows) {bool} is_can_audit 是否可审核
     * @apiParam (输出字段-data-rows) {array} files 附件
     * @apiParam (输出字段-data-rows-files) {int} id 附件id
     * @apiParam (输出字段-data-rows-files) {string} name 附件名称
     * @apiParam (输出字段-data-rows-files) {int} status 附件状态
     * @apiParam (输出字段-data-rows-files) {string} file_url 附件id
     */
    public function actionDetail(){
        try{
            $apply_id = Mod::app()->request->getParam('apply_id');
            if (!Utility::checkQueryId($apply_id)) {
                $this->returnError(RetailError::$PARAMS_PASS_ERROR);
            }

            $dto = OilStationApplyService::service()->getDetailDto($apply_id);
            $this->returnSuccess($dto);
        }catch(Exception $exception){
            $this->returnError($exception->getMessage(), $exception->getCode());
        }
    }

}