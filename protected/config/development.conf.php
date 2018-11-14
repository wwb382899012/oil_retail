<?php
return array(

    'params'=>array(
        "url_host"=>"retail.jtjr.com",
        "modPath" => "/data/www/framework_php7",
        "delay_amqp_url"=>"http://172.16.1.8:19532/",//延时队列
        "sms_url"=>"http://101.132.141.119:8081",//短信
        "oil_finance_url" => "http://172.16.1.16:11111", //油品财务系统地址
        "file_url"=>"http://172.16.1.41:8081/nor-lcs/api/fileupload/download?attachementId=",//车辆及司机附件地址
    ),

    'modules'=>[
        'gii'=>[
            'class'=>'system.gii.GiiModule',
            'password'=>'123456'
        ]
    ],

	'components'=>array(
		'db'=>array(
			'class'=>'CDbConnection',
			'charset' => 'utf8',
			'tablePrefix' => 't_',
			'connectionString' => 'mysql:host=172.16.1.141;port=3306;dbname=db_oil_retail',
			'username' => 'root',
			'password' => 'root1234',
            'retryCount' => '1',
		),
		'dbSlave'=>array(
			'class'=>'CDbConnection',
			'charset' => 'utf8',
			'tablePrefix' => 't_',
            'connectionString' => 'mysql:host=172.16.1.141;port=3306;dbname=db_oil_retail',
            'username' => 'root',
            'password' => 'root1234',
            'retryCount' => '1',
		),
        'dbLog'=>array(
            'class'=>'CDbConnection',
            'charset' => 'utf8',
            'tablePrefix' => 't_',
            'connectionString' => 'mysql:host=172.16.1.141;port=3306;dbname=db_oil_retail_log',
            'username' => 'root',
            'password' => 'root1234',
        ),
		'redis'=>array(
			'class'=>'CRedisCache',
			'serverConfigs'=>array(
				'dev'=>array('host'=>'172.16.1.141', 'port'=>6379)
			),
			'getIDC'=>array('dev'),
			'setIDC'=>array('dev'),
			'localIDC'=>array('dev')
		),
		// 日志配置，必须预加载生效
		'log'=>array(
			'class'=>'CLogRouter', 
			'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'trace, info, warning,error',
                    'LogDir'=>LOG_DIR,//此目录可配置,在此目录下，每天一个文件夹
                    'logFileName'=>'all.log'//记录日志的文件名可配置
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error',
                    'LogDir'=>LOG_DIR,//此目录可配置,在此目录下，每天一个文件夹
                    'logFileName'=>'error.log'//记录日志的文件名可配置
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error',
                    'LogDir'=>LOG_DIR,//此目录可配置,在此目录下，每天一个文件夹
                    'categories'=>'oil.import.log',
                    'logFileName'=>'oil.import.log'//记录日志的文件名可配置
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'info,warning',
                    'LogDir'=>LOG_DIR,//此目录可配置,在此目录下，每天一个文件夹
                    'categories'=>'retail.out',
                    'logFileName'=>'retail.out.log'//记录日志的文件名可配置
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error',
                    'LogDir'=>LOG_DIR,//此目录可配置,在此目录下，每天一个文件夹
                    'categories'=>'retail.out',
                    'logFileName'=>'retail.out.error.log'//记录日志的文件名可配置
                ),
				/*array(
					'class'=>'CFileLogRoute', // 写入
					'levels'=>'', // 记录所有级别的
					'LogDir'=>LOG_DIR,//此目录可配置,在此目录下，每天一个文件夹
					'logFileName'=>'all.log'//记录日志的文件名可配置
				)*/
			),
		),
		'FBSdkManager'=>array(
			'class'=>'application.components.fbsdk.FBSdkManager'
		),
		'MsgQ' => array(
			'class'=>'MsgQClient',
			'srvAddr'=>'http://172.16.1.8:8801'
		),
		'amqp' => array(
			'class' => 'system.components.amqp.CAMQP',
			'host'  => '172.16.1.141',
			'login' => 'jyb',
			'password'=>'root',

		),
        //微信企业号消息发送
        'weiXinMsg'=>array(
            'class'=>'application.components.weixin.ZWeixinMsg',
            'corp_id'=>'ww0f15b7e86e3ad77c',
            'secret'=>'pCJb7f9bPgXIqL28kO8-DlhqgiydS1YQFnjQyU4WDkQ',
            'agent_id'=>'1000004',
        ),

	)
);
