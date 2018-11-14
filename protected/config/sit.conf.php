<?php
return array(

    'params'=>array(
        "url_host"=>"",
        "modPath" => "/data/www/framework_php7",
        "delay_amqp_url"=>"http://10.13.56.73:19532/",//延时队列
        "sms_url"=>"http://101.132.141.119:8081",//短信
        "oil_finance_url" => "http://10.13.2.225:9802", //油品财务系统地址
        "file_url"=>"http://lcs.sit.guojiewuliu.com/nor-lcs/api/fileupload/download?attachementId=",//车辆及司机附件地址
    ),

	'components'=>array(
		'db'=>array(
			'class'=>'CDbConnection',
			'charset' => 'utf8',
			'tablePrefix' => 't_',
			'connectionString' => 'mysql:host=10.13.56.73;port=3306;dbname=db_oil_retail',
			'username' => 'root',
			'password' => 'sit1234',
			'retryCount' => '1',
            'schemaCachingDuration'=>60,//metadata缓存时间，单位s
		),
		'dbSlave'=>array(
			'class'=>'CDbConnection',
			'charset' => 'utf8',
			'tablePrefix' => 't_',
			'connectionString' => 'mysql:host=10.13.56.73;port=3306;dbname=db_oil_retail',
			'username' => 'root',
			'password' => 'sit1234',
			'retryCount' => '1',
		),
        /*'dbLog'=>array(
            'class'=>'CDbConnection',
            'charset' => 'utf8',
            'tablePrefix' => 't_',
            'connectionString' => 'mysql:host=10.13.56.73;port=3306;dbname=db_oil_retail_log',
            'username' => 'root',
            'password' => 'sit1234',
            'retryCount' => '1',
        ),
        'db_history'=>array(
            'class'=>'CDbConnection',
            'charset' => 'utf8',
            'tablePrefix' => 't_',
            'connectionString' => 'mysql:host=10.13.56.73;port=3306;dbname=db_oil_history',
            'username' => 'root',
            'password' => 'sit1234',
            'retryCount' => '1',
        ),*/
		'redis'=>array(
			'class'=>'CRedisCache',
			'serverConfigs'=>array(
				// 'dev'=>array('host'=>'172.16.1.13', 'port'=>6379)
				'dev'=>array('host'=>'10.13.56.73', 'port'=>6380)
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
                    'LogDir'=>LOG_DIR,//此目录可配置,在此目录下，每天一个文件夹p
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
			),
		),
		'amqp' => array(
			'class' => 'system.components.amqp.CAMQP',
			'host'  => '10.13.56.73',
            'login' => 'jybsit',
            'password'=>'jybsit1234',
		),
        //微信企业号消息发送
        'weiXinMsg'=>array(
            'class'=>'application.components.weixin.ZWeixinMsg',
            'corp_id'=>'ww0f15b7e86e3ad77c',
            'secret'=>'e_gM5Vu91F8T2g56idexCeN6pbubyuO27CZoAqOO0pg',
            'agent_id'=>'1000003',
        ),

	)
);
?>
