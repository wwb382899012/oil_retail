<?php
/*
 *CWebapplication的配置文件,所有的配置都在此配置
 *
 */
define('LOG_DIR', realpath(dirname(__FILE__).'/../runtime'));
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'油惠管理系统V1.0',
	'id'=>'OilRetail',
	'charset'=>'utf-8',	
	'language'=>'zh_cn',
	'preload'=>array('log'),

	'import'=>array(
        'application.attachment.*',
		'application.models.*',
		'application.components.*',
        'application.baseControllers.*',
        'application.cmd.*',
	    'application.commands.*',
        'application.services.*',
        'application.services.out.*',
		'application.extensions.PHPExcel.*',
	),

    'modules'=>[
        'admin'=>['class'=>'app.modules.admin.AdminModule',],
        'webAPI'=>['class'=>'app.modules.webAPI.WebAPIModule',],
    ],

	// 组件配置, 通过key引用（如：Mod::app()->bootstrap);
	'components'=>array(
		//url管理组件
		'urlManager'=>array(
			'urlFormat'=>'path',
			//要不要显示url中的index.php
			'showScriptName' => false,
			//url对应的解析规则,类似于nginx和apache的rewite,支持正则
			'rules'=>array(
                ['class' => 'application.components.MyUrlRule'],

                '<module:\w+>/<controller:\w+>/<action:\w+>'=>'<module>/<controller>/<action>',
                '<module:\w+>/<controller:\w+>'=>'<module>/<controller>',
                '<module:\w+>'=>'<module>',
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                '<controller:\w+>'=>'<controller>',
			),
		),
        'errorHandler'=>array(
            'errorAction'=>'site/error',
        ),
        'user' => array(
            'class'=>'WebUser',
        ),
		'curl'=>array(
			'class'=>'system.components.curl.CUrl',
			'options'=>array(
        		CURLOPT_TIMEOUT => 60,
			)
		),
		'mail'=>array(
			'class'=>'system.components.mailer.CWaeMailer',
		),
		//微信企业号用户管理
        'weiXinUser'=>array(
            'class'=>'application.components.weixin.ZWeixinUser',
            'corp_id'=>'ww0f15b7e86e3ad77c',
            'secret'=>'Rdbsjpww9FvR-mypu-lG8M03fkdoeoH3I4rZuG-8Yok',
        ),
        'weiXinOauth'=>array(
            'class'=>'application.components.weixin.ZWeixinOauth',
            'corp_id'=>'ww0f15b7e86e3ad77c',
            'secret'=>'Ys78-X8qxCwEtIHynNYJzTEMX0B4HDOkSPBeV58pLZg',
        ),
        //微信企业号消息发送
        'weiXinMsg'=>array(
            'class'=>'application.components.weixin.ZWeixinMsg',
            'corp_id'=>'ww0f15b7e86e3ad77c',
            'secret'=>'Ys78-X8qxCwEtIHynNYJzTEMX0B4HDOkSPBeV58pLZg',
            'agent_id'=>'1000002',
        ),
        'format'=>array(
            'class'=>'application.components.grid.ZFormatter',
        ),
	),
	"params"=>array(
        "modPath" => "/data/www/framework",
		//"modPath" => "../framework_php7",
        "url_host"=>"",//
        "web_url"=>"http://retail.web.jtjr.com/dist/public_dev/index.html",
        "systemId"=>"11",//系统类别id，主要是针对系统模块表中
        "prefix"=>"oil_retail_",//cookies等的前缀名
        "wx_agent_id"=>"1000002",//微信企业号agentId
        "serverEncode"=>"UTF-8",
        "isSaveActionLog"=>"1",//是否记录系统操作日志，1为异常记录，2为同步实时记录，0为不记录
        "isAutoUpdateWeixinCorp"=>"0",//是否自动更新微信企业号用户
        "web_controllers"=>require(dirname(__FILE__).'/web_controllers.config.php'),//web前端的虚拟controller配置
        'pageSize' => 10, //默认分页数大小
	),
    'di'=>require(dirname(__FILE__).'/di.config.php'),
);

