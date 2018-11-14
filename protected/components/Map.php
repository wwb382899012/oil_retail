<?php

use ddd\OilStation\Domain\OilCompany\OilCompanyFileEnum;

/**
 * Created by youyi000.
 * DateTime: 2017/8/21 15:31
 * Describe：
 */


class Map
{
    public static $v = array(

        //系统
        "system_id" => array("11" => "石油管理系统V2.0"),

        //系统模块状态
        "module_status" => array("0" => "未启用", "1" => "已启用"),
        //系统模块是否公开，即不需要判断权限
        "module_is_public" => array("0" => "不公开", "1" => "公开"),
        //系统模块的链接是否外部的，即直接新窗口打开
        "module_is_external" => array("0" => "内部", "1" => "外部"),
        //是否菜单
        "module_is_menu" => array("0" => "不是菜单", "1" => "是菜单"),
        //系统用户状态
        "user_status" => array("0" => "未启用", "1" => "启用"),
        //系统角色状态
        "role_status" => array("0" => "未启用", "1" => "启用"),
        //
        "account_status"=>array("0"=>"失效","1"=>"正常"),
        #######Oil Module#######
        //合作方企业所有制
        "ownership"=>array("1"=>"国有","2"=>"民营"),
        //油企状态
        'oil_company_status'=>[
            '0'=>'禁用',
            '1'=>'启用',
        ],
        //油企附件信息
        "oil_company_attachment_type"=>array(
            OilCompanyFileEnum::TYPE_DEFAULT => [
                "id"=>"1", "name"=>"其他附件", "multi"=>1, "maxSize"=>30,
                "fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"
            ],
            OilCompanyFileEnum::TYPE_CERTIFICATE => [
                "id"=>"2", "name"=>"证件附件", "multi"=>1, "maxSize"=>30,
                "fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"
            ],
        ),
        //油品状态
        'oil_goods_status'=>[
            '0'=>'禁用',
            '1'=>'启用',
        ],
        //油站申请状态
        'oil_station_apply_status'=>[
            '0'=>'已保存',
            '3'=>'已驳回',
            '5'=>'已提交',
            '7'=>'已审核',
        ],
        //油站附件
        'oil_station_apply_attachment_type'=>[
            "1"=>array("id"=>"1", "name"=>"附件", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ],
        //油站状态
        'oil_station_status'=>[
            '0'=>'禁用',
            '1'=>'启用',
        ],
        //油站附件
        'oil_station_attachment_type'=>[
            "1"=>array("id"=>"1", "name"=>"附件", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ],
        //油价状态
        'oil_price_status'=>[
            '0'=>'禁用',
            '1'=>'启用',
        ],
        //油价附件
        'oil_price_apply_attachment_type'=>[
            "1"=>array("id"=>"1", "name"=>"附件", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ],
        ##############
        //性别
        "gender"=>array("1"=>"男", "2"=>"女",),

        //物流企业状态
        "logistics_company_status"=>[
            '0'=>'禁用',
            '1'=>'启用',
        ],
        //物流企业银管家状态
        "logistics_company_out_status"=>[
            '1'=>'冻结',
            '0'=>'正常',
        ],
        //司机状态
        "driver_status"=>[
            '0'=>'禁用',
            '1'=>'启用',
        ],
        //企业额度状态
        "logistics_quota_status"=>[
            '-1'=>'过期',
            '1'=>'正常',
        ],
        //企业额度变更原因
        "logistics_quota_log_category"=>[
            '10'=>'订单支付',
            '20'=>'物流还款'
        ],
        //车辆状态
        "vehicle_status"=>[
            '-9' =>'作废',
            '-1' =>'审核驳回',
            '0'  =>'新建',
            '1'  =>'审核中',
            '2'  =>'审核通过'
        ],
        "currency" => array(
            "1"=>array("id" => 1, "name" => "人民币","ico" => "￥"),
            "2"=>array("id" => 2, "name" => "美元","ico" => "$"),
        ),

        "order_status"=>[
            "-1" => "交易失败",
            "0" => "新建",
            "10" => "交易成功"
        ]
    );

    public static function getMap(string $key){
        return self::$v[$key] ?? [];
    }

    public static function getMaps(array $keys) {
        $data = [];
        foreach ($keys as $key) {
            $data[$key] = self::$v[$key];
        }

        return $data;
    }

    /**
     * 获取key/value对象，提供给前端
     * @param string $key
     * @return array
     */
    public static function getKeyValueObject(string $key){
        if(!isset(self::$v[$key])){
            return [];
        }

        $data = [];

        $map = self::$v[$key];
        foreach($map as $key => $value){
            $data[] = [
                'id'=>$key,
                'value'=> $value
            ];
        }

        return $data;
    }

    public static function getStatusName(string $key,$status){
        return self::$v[$key][$status] ?? "";
    }
}
