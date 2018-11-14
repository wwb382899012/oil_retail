<?php

use ddd\OilStation\Application\OilPriceApplyService;

/**
 * 邮件管理
 * Class OilPriceApplyController
 */
class OilPriceApplyController extends AttachmentController{

    public function pageInit(){
        parent::pageInit();
        $this->attachmentType = Attachment::C_OIL_PRICE_APPLY;
//        $this->publicActions = ['excelData','submit'];
        $this->authorizedActions=['saveFile','getFile'];
        $this->rightCode = 'oil-price-apply';
    }

    /**
     * @api {POST} /webAPI/oilPriceApply/saveFile [saveFile] 附件上传
     * @apiName saveFile
     * @apiGroup WebAPI - OilPriceApply
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
     * @api {GET} /webAPI/oilPriceApply/delFile [delFile] 附件删除
     * @apiName delFile
     * @apiGroup WebAPI - OilPriceApply
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
     * @api {GET} /webAPI/oilPriceApply/getFile [getFile] 下载文件
     * @apiName getFile
     * @apiGroup WebAPI - OilPriceApply
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
     * @api {GET} /webAPI/oilPriceApply/excelData [excelData] 获取导入数据
     * @apiName excelData
     * @apiGroup WebAPI - OilPriceApply
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} file_id 文件id
     * @apiExample {FormData} 输入示例:
     * {
     *      "file_id":779,
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "state": 0,
     * "data": {
     * "is_can_submit": false,
     * "data": [
     * {
     * "station_name": "坪洲加油站",
     * "company_name": "百度科技",
     * "goods_name": "97#汽油",
     * "retail_price": 5.1,
     * "agreed_price": 5,
     * "discount_price": 5.05,
     * "company_id": 1,
     * "station_id": 1,
     * "goods_id": 1,
     * "remark": "优惠价＞协议价"
     * },
     * {
     * "station_name": "坪洲加油站",
     * "company_name": "百度科技",
     * "goods_name": "95#汽油",
     * "retail_price": 6.5,
     * "agreed_price": 6,
     * "discount_price": 6.2,
     * "company_id": 1,
     * "station_id": 1,
     * "goods_id": 2,
     * "remark": "优惠价＞协议价"
     * },
     * {
     * "station_name": "说的是",
     * "company_name": "",
     * "goods_name": "",
     * "retail_price": "",
     * "agreed_price": "",
     * "discount_price": "",
     * "company_id": 0,
     * "station_id": 0,
     * "goods_id": 0,
     * "remark": "油企名称缺失；油站不存在；油品名称缺失；零售价缺失；协议价缺失；优惠价缺失"
     * }
     * ]
     * }
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data": ""
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 成功时返回数据
     * @apiParam (输出字段-data) {bool} is_can_submit 是否可提交
     * @apiParam (输出字段-data-data) {string} station_name  站点名称
     * @apiParam (输出字段-data-data) {string} company_name  企业名称
     * @apiParam (输出字段-data-data) {string} goods_name  商品名称
     * @apiParam (输出字段-data-data) {string} retail_price  零售价
     * @apiParam (输出字段-data-data) {string} agreed_price  协议价
     * @apiParam (输出字段-data-data) {string} discount_price  优惠价
     * @apiParam (输出字段-data-data) {string} remark  备注
     */
    public function actionExcelData(){
        $file_id = Mod::app()->request->getParam('file_id');
        if (!Utility::checkQueryId($file_id)) {
            $this->returnError(BusinessError::outputError(RetailError::$PARAMS_PASS_ERROR));
        }

        try{
            $data = OilPriceApplyService::service()->getOilPriceListByExcelFile($file_id);
            $this->returnSuccess($data);
        }catch(Exception $e){
            $this->returnError($e->getMessage(),$e->getCode());
        }
    }

    /**
     * @api {GET} /webAPI/oilPriceApply/submit [submit] 提交价格申请
     * @apiName submit
     * @apiGroup WebAPI - OilPriceApply
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} file_id 文件id
     * @apiExample {FormData} 输入示例:
     * {
     *      "file_id":779,
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "state": 0,
     * "data": "JG201809170307"
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data": ""
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 成功时返回价格申请编号
     */
    public function actionSubmit(){
        $file_id = $this->getRestParam('file_id');
        if (!Utility::checkQueryId($file_id)) {
            $this->returnError(BusinessError::outputError(RetailError::$PARAMS_PASS_ERROR));
        }

        try{
            $dto = OilPriceApplyService::service()->getOilPriceApplyDTO($file_id);
            if(!$dto->validate()){
                $this->returnError($dto->getErrors());
            }

            $entity = $dto->toEntity();

            OilPriceApplyService::service()->submit($entity);

            $this->returnSuccess($entity->getCode());
        }catch(Exception $e){
            $this->returnError($e->getMessage(),$e->getCode());
        }
    }

}