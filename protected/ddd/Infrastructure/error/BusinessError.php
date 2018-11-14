<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 18:02
 * Describe：
 *  业务错误码定义，8位数字，前三位区分不同大模块，第四五两位区分小模块，最后三位不同的错误信息
 *      100 - 系统通用
 *              - 00 系统
 *              - 01 参数校验
 *              - 02 表单校验
 *              - 03 操作
 *              - 04 审核
 *      101 - 风控额度相关
 *              - 00 物流企业额度
 *              - 01 物流企业限额
 *              - 02 车辆额度
 *              - 03 车辆限额
 *      102 - 物流企业相关
 *              - 00 物流企业
 *              - 01 车辆
 *              - 02 司机
 *      103 - 客户信息相关
 *      104 - 油企相关
 *              - 00 油企
 *              - 01 油站
 *              - 02 油品
 *              - 03 油价
 *      105 - 订单相关
 *      106 - 收付款相关
 *              - 00 付款
 *              - 01 收款
 */

namespace ddd\Infrastructure\error;


class BusinessError
{
    /**
     * 数据模型不存在
     */
    const MODEL_NOT_EXISTS = 1001;

    const MODEL_SAVE_FALSE = 1002;

    const MODEL_DELETE_FALSE = 1003;

    /**
     * 实体对象不存在
     */
    const ENTITY_NOT_EXISTS = 2001;


    const Argument_Required = array("10000001", "{name}不得为空");
    const Argument_Invalid = array("10000002", "{name}不得为空");
    const Form_Item_Required = array("10002001", "{name}不得为空");
    const Readonly_Property = array("10002002", "{name}为只读");
    const Operate_Error = array("10003001", "操作失败，失败原因：{reason}");
    const Validate_Error = array("10003002", "验证失败：{reason}");
    const System_Busy = array("10003003", "系统繁忙，请稍后再试！");

    const Check_Detail_Not_Exist = array("10004001", "审核信息不存在！审核信息ID:{detail_id}");
    const Check_Obj_Empty = array("10004002", "审核对象为空");
    const Check_Obj_Not_Exist = array("10004003", "审核对象不存在");
    const Obj_Check_Detail_Not_Exist = array("10004004", "对象:{obj_id}审核信息不存在");

    #region 100
    const SystemModule_Can_Not_Edit = array("10005001", "当前模块:{id}不能编辑");
    #endregion

    #region 101
    const Logistics_Quota_Not_Exist=array("10100001","物流企业:{logistics_id}，额度信息不存在！");
    const Logistics_Credit_Quota_Not_Exist=array("10100002","物流企业:{logistics_id}，授信额度信息不存在！");
    const Logistics_Daily_Quota_Not_Exist=array("10100003","物流企业:{logistics_id}，当日额度信息不存在！");

    const Logistics_Quota_Limit_Not_Exist=array("10101001","物流企业限额信息不存在！");

    const Vehicle_Quota_Limit_Not_Exist=array("10103001","车辆限额信息不存在！");
    #endregion

    #region 102
    const Logistics_Company_Not_Exist=array("10200001","物流企业:{logistics_id},不存在！");
    const Logistics_Status_Not_Active=array("10200002","物流企业账号失效，请联系所在物流公司");

    const Vehicle_Not_Exist=array("10201001","车辆:{vehicle_id},不存在！");
    const Vehicle_Daily_Quota_Not_Exist=array("10201002","车辆:{vehicle_id}，当日额度信息不存在！");
    const Vehicle_Max_Quantity_Is_Empty = array("10201003", "获取订单:{order_id}，车辆最大可加油失败！");

    const Driver_Status_Not_Active=array("10202001","司机账号失效，请联系所在物流公司");
    const Driver_Not_Exist=array("10202002","当前司机:{customer_id},不存在！");
    #endregion

    #region 103
    const Customer_Not_Exist = array("10300001","客户:{customer_id},不存在！");
    const Customer_Trans_Password_Error = array("10300002","交易密码错误！");
    const Customer_Wx_Is_Bound = array("10300003","该客户已绑定微信！");
    #endregion

    #region 104
    const Oil_Company_Not_Exist = array("10400001","油企:{oil_company_id},不存在！");

    const Oil_Station_Not_Exist = array("10401001","油站:{station_id},不存在！");
    const Oil_Station_Not_Active = array("10401002","当前油站已禁用");

    const Oil_StationApply_Not_Allow_Submit = array("10401201","当前油站申请不允许提交！");

    const Oil_Goods_Not_Exist = array("10402001","油品:{goods_id},不存在！");
    const Oil_Goods_Can_Sell_Not_Exist = array("10402002","暂无可售油品！");
    const Oil_Goods_Not_Active = array("10402003","当前油品:{goods_id},不可用！");

    const Oil_Price_Active_Not_Exist = array("10403001","油站:{station_id},油品:{goods_id},可用价格不存在！");
    const Oil_Goods_Can_Not_Sell = array("10403002","当前油品不可售！");

    const Oil_Price_Excel_Data_Is_Empty = ['10404001','导入的油价数据不能为空！'];
    const Oil_Price_Excel_Data_Not_Allow_Submit = ['10404001','导入的油价数据不符合要求，不能提交！'];
    const Oil_Price_Excel_Data_Error = ['10404002','导入的油价数据第{num}行错误:{error}！'];
    #endregion

    #region 105
    const Order_Status_Not_Allow_Effect = array("10500001","当前状态的订单不能生效");
    const Order_Status_Not_Allow_Failed = array("10500002","当前状态的订单不能置为失效");
    const Order_Not_Exist = array("10500003","当前订单:{order_id}，不存在！");
    const Order_Create_Error = array("10500004","订单生成失败！");
    const Order_Status_Not_Allow_Payment = array("10500005","当前状态的订单:{order_id}，不能进行付款！");
    const Order_Amount_Gt_Logistics_Quota = array("10500006","订单:{order_id}，销售价格:{sell_amount} 超出物流企业可用额度:{max_amount}！");
    const Order_Amount_Gt_Logistics_Daily_Quota = array("10500007","订单:{order_id}，销售价格:{sell_amount} 超出物流企业当日可用额度:{max_daily_amount}！");
    const Order_Quantity_Gt_Vehicle_Daily_Quota = array("10500008","订单:{order_id}，本次加油量:{quantity} 超出车辆当日可用额度:{max_quantity}！");
    #endregion

    #region 106
    const Order_Payment_Status_Not_Allow_Payment = array("10600001","当前状态的付款单不能进行付款");
    const Order_Payment_Status_Not_Allow_Paid = array("10600002","当前状态的付款单不能进行完成付款");
    const Order_Payment_Error = array("10600003", "订单:{order_id}，支付出错！");
    #endregion
}