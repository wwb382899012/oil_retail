<?php

use ddd\OilStation\Application\OilCompanyService;

/**
 * 油企管理
 * Class OilCompanyController
 */
class OilCompanyController extends AttachmentController{

    public function pageInit(){
        parent::pageInit();
        $this->attachmentType = Attachment::C_OIL_COMPANY;
        $this->rightCode = 'oil-company';
        $this->authorizedActions=['saveFile','getFile'];
        //$this->publicActions = array('list','detail','save');
    }


    /**
     * @api {POST} /webAPI/oilCompany/saveFile [saveFile] 附件上传
     * @apiName saveFile
     * @apiGroup WebAPI - OilCompany
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
     * @api {POST} /webAPI/oilCompany/delFile [delFile] 附件删除
     * @apiName delFile
     * @apiGroup WebAPI - OilCompany
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
     * @api {GET} /webAPI/oilCompany/getFile [getFile] 获取附件
     * @apiName getFile
     * @apiGroup WebAPI - OilCompany
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} id 文件id <font color=red>必填</font>
     * @apiParam (输入字段) {string} fileName 文件名称
     * @apiExample {FormData} 输入示例:
     * {
     *      "id":779,
     *      "fileName":779,
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
    public function actionGetFile(){
        parent::actionGetFile();
    }

    /**
     * @api {GET} /webAPI/oilCompany/list [list] 获取列表
     * @apiName list
     * @apiGroup WebAPI - OilCompany
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {string} id 企业名称
     * @apiParam (输入字段) {string} short_name 企业简称
     * @apiParam (输入字段) {string} tax_code 纳税识别号
     * @apiParam (输入字段) {string} corporate 企业法人
     * @apiParam (输入字段) {string} address 企业地址
     * @apiParam (输入字段) {int} status 企业状态
     * @apiExample {FormData} 输入示例:
     * {
     * "page": 1,
     * "pageSize": 2,
     * "search": {
     *      "name": "企业名称",
     *      "short_name": "企业简称",
     *      "tax_code"=> "纳税识别号",
     *      "corporate"=> "企业法人",
     *      "address"=> "企业地址",
     *      "status"=> "企业状态"
     * }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "state": 0,
     * "data": {
     * "search": null,
     * "data": {
     * "pageCount": 1,
     * "rows": [{
     * {
     * "company_id": "1",
     * "name": "百度科技",
     * "short_name": "百度",
     * "tax_code": "584714",
     * "corporate": "李彦宏",
     * "address": "宝安大道",
     * "contact_phone": "0795-852741",
     * "ownership": "1",
     * "build_date": "2018-09-05",
     * "remark": "备注",
     * "status": "1",
     * "ownership_name": "国有",
     * "status_name": "启用"
     * }
     * ],
     * "total": "2",
     * "page": "1"
     * }
     * }
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data": []
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 成功时返回列表数据
     * @apiParam (输出字段-data-rows) {int} company_id 可平移记录id
     * @apiParam (输出字段-data-rows) {string} name 企业名称
     * @apiParam (输出字段-data-rows) {string} short_name 企业简称
     * @apiParam (输出字段-data-rows) {string} tax_code 纳税识别号
     * @apiParam (输出字段-data-rows) {string} corporate 企业法人
     * @apiParam (输出字段-data-rows) {string} address 企业地址
     * @apiParam (输出字段-data-rows) {string} contact_phone 联系电话
     * @apiParam (输出字段-data-rows) {string} ownership 企业所有制
     * @apiParam (输出字段-data-rows) {string} ownership_name 企业所有制名称
     * @apiParam (输出字段-data-rows) {string} build_date 成立日期
     * @apiParam (输出字段-data-rows) {string} remark 备注
     * @apiParam (输出字段-data-rows) {int} status 状态
     * @apiParam (输出字段-data-rows) {string} status_name 状态名称
     */
    public function actionList(){
        $search = $this->getSearch();
        $attr = array(
            'oc.name*'=>$search['name'],
            'oc.short_name*'=>$search['short_name'],
            'oc.tax_code*'=>$search['tax_code'],
            'oc.corporate*'=>$search['corporate'],
            'oc.address*'=>$search['address'],
            'oc.status'=>$search['status']
        );

        $where = $this->getWhereSql($attr);

        $sql = <<<SQL
SELECT {col} FROM t_oil_company oc $where ORDER BY oc.company_id DESC
SQL;

        $fields = [
            'oc.company_id,oc.name,oc.short_name,oc.tax_code,oc.corporate,oc.address,oc.contact_phone,oc.ownership',
            'oc.build_date,oc.remark,oc.status',
        ];

        $data = $this->getPageData($sql, implode(',',$fields));

        if(empty($data)){
            $this->returnSuccess([]);
        }

        foreach($data->data as $key => & $item){
            $item['ownership_name'] = \Map::getStatusName('ownership', $item['ownership']);
            $item['status_name'] = \Map::getStatusName('oil_company_status', $item['status']);
            $item['is_can_view'] = true;
            $item['is_can_edit'] = true;
        }

        $this->returnSuccess($data);
    }

    /**
     * @api {POST} /webAPI/oilCompany/save [save] 保存
     * @apiName save
     * @apiGroup WebAPI - OilCompany
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} company_id 企业编号
     * @apiParam (输入字段) {string} name 企业名称
     * @apiParam (输入字段) {string} short_name 企业简称
     * @apiParam (输入字段) {string} tax_code 纳税编号
     * @apiParam (输入字段) {string} corporate 企业法人
     * @apiParam (输入字段) {string} address 企业地址
     * @apiParam (输入字段) {string} contact_phone 联系电话
     * @apiParam (输入字段) {int} ownership 企业所有制
     * @apiParam (输入字段) {string} build_date 成立日期
     * @apiParam (输入字段) {string} remark 备注
     * @apiParam (输入字段) {int} status 企业状态
     * @apiParam (输入字段) {array} files 附件
     * @apiParam (输入字段-files) {int} id 附件id
     * @apiParam (输入字段-files) {string} name 附件名称
     * @apiParam (输入字段-files) {int} status 附件状态
     * @apiParam (输入字段-files) {string} file_url 附件id
     *
     * @apiExample {json} 输入示例:
     *{
     * "company_id": "1",
     * "name": "百度科技",
     * "short_name": "企业简称",
     * "tax_code": "584714",
     * "corporate": "李彦宏",
     * "address": "宝安大道",
     * "contact_phone": "0795-852741",
     * "ownership": "1",
     * "build_date": "2018-09-05",
     * "remark": "备注",
     * "status": "0",
     * "files":[
     * "id": 2,
     * "name": "附件2",
     * "status": 1,
     * "file_url": "/xxx/xx/test.pdf"
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
     * @apiParam (输出字段) {array} data 成功时返回油企id
     */
    public function actionSave(){
        try{
            $reqData = $this->getRestParams();

            $dto = OilCompanyService::service()->assetDto($reqData);
            if(!$dto->validate()){
                $this->returnError($dto->getErrors());
            }

            $entity =  OilCompanyService::service()->save($dto->toEntity());

            $this->returnSuccess($entity->getId());
        }catch(Exception $e){
            $this->returnError($e->getMessage(),$e->getCode());
        }
    }

    /**
     * @api {POST} /webAPI/oilCompany/detail [detail] 详情
     * @apiName detail
     * @apiGroup WebAPI - OilCompany
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} company_id 企业编号
     * @apiExample {FormData} 输入示例:
     * {
     *      "company_id":779
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
     * "address": "宝安大道",
     * "contact_phone": "0795-852741",
     * "ownership": "1",
     * "build_date": "2018-09-05",
     * "remark": "备注",
     * "status": "0",
     * }
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 成功时返回数据
     * @apiParam (输出字段-data-rows) {int} company_id 企业id
     * @apiParam (输出字段-data-rows) {string} name 企业名称
     * @apiParam (输出字段-data-rows) {string} short_name 企业简称
     * @apiParam (输出字段-data-rows) {string} tax_code 纳税识别号
     * @apiParam (输出字段-data-rows) {string} corporate 企业法人
     * @apiParam (输出字段-data-rows) {string} address 企业地址
     * @apiParam (输出字段-data-rows) {string} contact_phone 联系电话
     * @apiParam (输出字段-data-rows) {string} ownership 企业所有制
     * @apiParam (输出字段-data-rows) {string} ownership_name 企业所有制名称
     * @apiParam (输出字段-data-rows) {string} build_date 成立日期
     * @apiParam (输出字段-data-rows) {string} remark 备注
     * @apiParam (输出字段-data-rows) {int} status 状态
     * @apiParam (输出字段-data-rows) {string} status_name 状态名称
     * @apiParam (输出字段-data-rows) {array} files 附件
     * @apiParam (输出字段-data-rows-files) {int} id 附件id
     * @apiParam (输出字段-data-rows-files) {string} name 附件名称
     * @apiParam (输出字段-data-rows-files) {int} status 附件状态
     * @apiParam (输出字段-data-rows-files) {string} file_url 附件id
     */
    public function actionDetail(){
        try{
            $company_id = Mod::app()->request->getParam('company_id');
            if (!Utility::checkQueryId($company_id)) {
                $this->returnError(RetailError::$PARAMS_PASS_ERROR);
            }

            $dto = OilCompanyService::service()->getDetailDto($company_id);
            $this->returnSuccess($dto);
        }catch(Exception $exception){
            $this->returnError($exception->getMessage(),$exception->getCode());
        }
    }

}