<?php

use ddd\OilStation\Application\OilGoodsService;

/**
 * 油品管理
 * Class OilGoodsController
 */
class OilGoodsController extends Controller{

    public function pageInit(){
        parent::pageInit();
        $this->rightCode = 'oil-goods';
        //$this->publicActions = ['list','detail','save',];
    }

    /**
     * @api {POST} /webAPI/oilGoods/list [list] 获取列表
     * @apiName list
     * @apiGroup WebAPI - OilGoods
     * @apiVersion 1.0.0
     * @apiParam (输入字段-search) {string} name 油品名称
     * @apiParam (输入字段-search) {string} code 油品编码
     * @apiParam (输入字段-search) {int} status 油品状态
     * @apiExample {FormData} 输入示例:
     * {
     * "page": 1,
     * "pageSize": 2,
     * "search": {
     *      "name": "油品名称",
     *      "code": "油品编码",
     *      "status": "状态",
     * }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "state": 0,
     * "data": [
     * {
     * "goods_id": "1",
     * "name": "97#汽油",
     * "code": "25784",
     * "order_index": "1",
     * "remark": "备注",
     * "status": "0",
     * "status_time": "2018-09-05 20:04:22",
     * "effect_time": "2018-09-05 20:04:22",
     * "create_time": "2018-09-05 20:04:22",
     * "create_user_id": "1",
     * "update_user_id": "1",
     * "update_time": "2018-09-05 20:04:22",
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
     * @apiParam (输出字段-data-rows) {int} goods_id 油品id
     * @apiParam (输出字段-data-rows) {string} name 油品名称
     * @apiParam (输出字段-data-rows) {string} code 油品编号
     * @apiParam (输出字段-data-rows) {string} order_index 排序号
     * @apiParam (输出字段-data-rows) {string} remark 备注
     * @apiParam (输出字段-data-rows) {int} status 状态
     * @apiParam (输出字段-data-rows) {string} status_name 状态名称
     * @apiParam (输出字段-data-rows) {string} status_time 状态时间
     * @apiParam (输出字段-data-rows) {string} effect_time 生效时间
     * @apiParam (输出字段-data-rows) {int} create_user_id 创建人ID
     * @apiParam (输出字段-data-rows) {string} create_user_name 创建人
     * @apiParam (输出字段-data-rows) {string} create_time 创建时间
     * @apiParam (输出字段-data-rows) {int} update_user_id 更新人ID
     * @apiParam (输出字段-data-rows) {string} update_user_name 更新人
     * @apiParam (输出字段-data-rows) {string} update_time 更新时间
     */
    public function actionList(){
        $search = $this->getSearch();
        $attr = array(
            'og.name*'=>$search['name'],
            'og.code*'=>$search['code'],
            'og.status'=>$search['status']
        );
        $where = $this->getWhereSql($attr);

        $sql = <<<SQL
SELECT {col} FROM t_oil_goods og $where ORDER BY og.goods_id DESC
SQL;

        $fields = 'og.goods_id,og.name,og.code,og.order_index as sort,og.remark,og.status';

        $data = $this->getPageData($sql, $fields);

        if(empty($data)){
            $this->returnSuccess([]);
        }

        foreach($data->data as $key => & $item){
            $item['status_name'] = \Map::getStatusName('oil_company_status', $item['status']);
            $item['is_can_view'] = true;
            $item['is_can_edit'] = true;
        }

        $this->returnSuccess($data);
    }

    /**
     * @api {POST} /webAPI/oilGoods/save [save] 保存
     * @apiName save
     * @apiGroup WebAPI - OilGoods
     * @apiVersion 1.0.0
     * @apiParam (输入字段-data-rows) {int} goods_id 油品id
     * @apiParam (输入字段-data-rows) {string} name 油品名称
     * @apiParam (输入字段-data-rows) {string} code 油品编号
     * @apiParam (输入字段-data-rows) {string} sort 排序号
     * @apiParam (输入字段-data-rows) {string} remark 备注
     * @apiParam (输入字段-data-rows) {int} status 状态
     * @apiExample {FormData} 输入示例:
     *{
     * "goods_id": "1",
     * "name": "97#汽油",
     * "code": "25784",
     * "sort": "1",
     * "remark": "备注",
     * "status": "0",
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
     * @apiParam (输出字段) {array} data 成功时返回加油品id
     */
    public function actionSave(){
        try{
            $reqData = $this->getRestParams();

            $dto = OilGoodsService::service()->assetDto($reqData);
            if(!$dto->validate()){
                $this->returnError($dto->getErrors());
            }

            $entity =  OilGoodsService::service()->save($dto->toEntity());

            $this->returnSuccess($entity->getId());
        }catch(Exception $e){
            $this->returnError($e->getMessage(),$e->getCode());
        }
    }

    /**
     * @api {POST} /webAPI/oilGoods/detail [detail] 详情
     * @apiName detail
     * @apiGroup WebAPI - OilGoods
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} company_id 油品id
     * @apiExample {FormData} 输入示例:
     * {
     *      "company_id":779
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     *{
     * "state": 0,
     * "data": {
     * "goods_id": "1",
     * "name": "97#汽油",
     * "code": "25784",
     * "sort": "1",
     * "remark": "备注",
     * "status": "0",
     * "status_time": "2018-09-05 20:04:22",
     * "effect_time": "2018-09-05 20:04:22",
     * "create_time": "2018-09-05 20:04:22",
     * "create_user_id": "1",
     * "update_user_id": "1",
     * "update_time": "2018-09-05 20:04:22",
     * "status_name": "",
     * "create_user_name": "",
     * "update_user_name": ""
     * }
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data": []
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 成功时返回数据
     * @apiParam (输出字段-data-rows) {int} goods_id 油品id
     * @apiParam (输出字段-data-rows) {string} name 油品名称
     * @apiParam (输出字段-data-rows) {string} code 油品编号
     * @apiParam (输出字段-data-rows) {string} sort 排序号
     * @apiParam (输出字段-data-rows) {string} remark 备注
     * @apiParam (输出字段-data-rows) {int} status 状态
     * @apiParam (输出字段-data-rows) {string} status_name 状态名称
     * @apiParam (输出字段-data-rows) {string} status_time 状态时间
     * @apiParam (输出字段-data-rows) {string} effect_time 生效时间
     * @apiParam (输出字段-data-rows) {int} create_user_id 创建人ID
     * @apiParam (输出字段-data-rows) {string} create_user_name 创建人
     * @apiParam (输出字段-data-rows) {string} create_time 创建时间
     * @apiParam (输出字段-data-rows) {int} update_user_id 更新人ID
     * @apiParam (输出字段-data-rows) {string} update_user_name 更新人
     * @apiParam (输出字段-data-rows) {string} update_time 更新时间
     */
    public function actionDetail(){
        try{
            $goods_id = Mod::app()->request->getParam('goods_id');
            if (!Utility::checkQueryId($goods_id)) {
                $this->returnError(RetailError::$PARAMS_PASS_ERROR);
            }

            $dto = OilGoodsService::service()->getDetailDto($goods_id);
            $this->returnSuccess($dto);
        }catch(Exception $exception){
            $this->returnError($exception->getMessage(), $exception->getCode());
        }
    }

}