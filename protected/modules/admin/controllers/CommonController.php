<?php

class CommonController extends WebAPIController{

    public function pageInit() {
        parent::pageInit();
        $this->authorizedActions = ['dropDownListMap'];
    }

    /**
     * @api {GET} admin/common/dropDownListMap 获取下拉列表map
     * @apiName dropDownListMap
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     *{
     *    "code": 0,
     *    "data":[
     *      'module_status' => [], //系统模块状态
     *      'module_is_public' => [], //系统模块是否公开，即不需要判断权限
     *      'module_is_external' => [], //系统模块的链接是否外部的，即直接新窗口打开
     *      'module_is_menu' => [], //是否菜单
     *      'user_status' => [], //系统用户状态
     *      'role_status' => [], //系统角色状态
     * ]
     *}
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup admin - common
     * @apiVersion 1.0.0
     */
    public function actionDropDownListMap(){
        $data = [
            'module_status' => \Map::getKeyValueObject('module_status'),
            'module_is_public' => \Map::getKeyValueObject('module_is_public'),
            'module_is_external' => \Map::getKeyValueObject('module_is_external'),
            'module_is_menu' => \Map::getKeyValueObject('module_is_menu'),
            'user_status' => \Map::getKeyValueObject('user_status'),
            'role_status' => \Map::getKeyValueObject('role_status'),
        ];

        $this->returnSuccess($data);
    }

}