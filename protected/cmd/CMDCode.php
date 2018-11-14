<?php

/**
 * Created by youyi000.
 * DateTime: 2016/10/9 14:59
 * Describe：
 */

class CMDCode {


    const CODE_METHOD_PORT_INVALID = ["code"=>"1001","msg"=>"请求方法或端口异常"]; //请求方法或端口异常
    const CODE_NO_POST_DATA =["code"=>"1002","msg"=>"没有请求内容"];//没有请求内容
    const CODE_CMD_INVALID =["code"=>"1003","msg"=>"命令字不存在"]; //命令字不存在
    const CODE_PARAM_MISSING = ["code"=>"1004","msg"=>"请求参数缺失"];//请求参数缺失
    const CODE_PARAM_CHECK_ERROR =["code"=>"1005","msg"=>"参数校验失败"]; //参数校验失败
    const CODE_NO_AUTHORIZED = ["code"=>"1006","msg"=>"非法调用"];
    const CODE_PASSWORD_ERROR = ["code"=>"1007","msg"=>"交易密码必须是6位数字"]; //命令执行出错
    const CODE_CMD_ERROR = ["code"=>"1009","msg"=>"命令执行出错"]; //命令执行出错


    /**
     * @api {POST} / 错误码描述
     * @apiName errorCode
     * @apiParam (字段) {string} 1001 请求方法或端口异常
     * @apiParam (字段) {string} 1002 没有请求内容
     * @apiParam (字段) {string} 1003 命令字不存在
     * @apiParam (字段) {string} 1004 请求参数缺失
     * @apiParam (字段) {string} 1005 参数校验失败
     * @apiParam (字段) {string} 1006 非法调用

     * @apiParam (字段) {string} 1009 命令执行出错
     * @apiParam (字段) {string} 0 命令执行成功

     *
     * @apiGroup 0ErrorCode
     * @apiVersion 1.0.0
     */
    protected function none() {
    }
}