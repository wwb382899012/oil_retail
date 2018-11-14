<?php

/**
 * Desc: 系统错误码
 * User: susiehuang
 * Date: 2017/5/8 0008
 * Time: 17:54
 */
class RetailError {
    //100001001 非业务错误码
    public static $NOT_LOGIN_IN = array(100011000, '您当前未登录，请登录后重试');
    public static $NOT_FIND = array(100011001, '${key} not find ${msg}');
    public static $NOT_IN_VALUE = array(100011002, '${key} not in values');
    public static $NOT_INT_ERR = array(100011003, '${str}:输入非法，请输入整数');
    public static $FEN_TO_YUAN_ERR = array(100011004, '${fen} not fen');
    public static $NOT_STANDARD_YUAN = array(100011005, '${yuan}不是正规的元单位');
    public static $YUAN_TO_FEN_ERR = array(100011006, '${yuan} to fen larger than ${num}');
    public static $UNSUPPORT_COMPUTER = array(100011007, '不支持该机器');
    public static $REQUIRED_PARAMS_CHECK_ERROR = array(100011008, '*号标注字段不得为空！');
    public static $PARAMS_PASS_ERROR = array(100011009, '参数传入错误，请检查！');
    public static $OPERATE_FAILED = array(100011010, '操作失败:${reason}！');
    public static $APPROVAL_INFO_NOT_EXIST = array(100011011, '审批信息不存在，审批ID:${check_id}！');
    public static $GOODS_UNIT_CHANGED = array(100011012, '商品${goods_id}的计价单位与已现有数据不一致，重新检查后重新填写！');
    public static $CORPORATION_NOT_SELECTED = array(100011013, '请选择交易主体！');
    public static $PARTNER_NOT_SELECTED = array(100011014, '请选择合作方！');
    public static $CHECK_DETAIL_NOT_EXIST = array(100011015, '审批明细不存在，明细ID:${detail_id}！');
    public static $TRANSACTION_DETAIL_GOODS_NAME_REPEAT = array(100011016, '交易明细品名不得重复！');
    public static $SYSTEM_BUSY = array(100011017, '系统繁忙，稍后重试！');
    public static $USER_INFO_NOT_EXIST = array(100011018, '用户ID:{user_id}信息不存在！');
    public static $ROLE_INFO_NOT_EXIST = array(100011019, '角色ID:{role_id}信息不存在！');
    public static $MODULE_INFO_NOT_EXIST = array(100011020, '模块ID:{id}信息不存在！');

    //物流企业
    public static $LOGISTICS_COMPANY_NOT_EXIST = array(100005001, '物流企业不存在，ID:${logistics_id}！');

    //车辆
    public static $VEHICLE_NOT_EXIST = array(100006001, '车辆不存在，ID:${vehicle_id}！');

    //司机
    public static $DRIVER_NOT_EXIST = array(100007001, '司机不存在，ID:${driver_id}！');

    //订单
    public static $ORDER_NOT_EXIST = array(100008001, '订单不存在，ID:${order_id}！');
}