<?php
return array(
    'params'=>array(
        "url_host"=>"retail.jyblife.com",
        "modPath" => "/data/www/framework",
        "sms_url"=>"http://101.132.141.119:8081",//短信
        "oil_finance_url" => "http://10.23.140.97:9802", //油品财务系统地址
        "file_url"=>"http://lcs.guojiewuliu.com/nor-lcs/api/fileupload/download?attachementId=", //车辆及司机附件地址
    ),
    'components'=>array(
        'db'=>array(
            'class'=>'CDbConnection',
            'charset' => 'utf8',
            'tablePrefix'=>'t_',
            'connectionString' => 'mysql:host=10.23.62.172;port=3306;dbname=db_oil_retail',
            'username' => 'jyb',
            'password' => 'YHNBGT',
        ),
        'dbSlave'=>array(
            'class'=>'CDbConnection',
            'charset' => 'utf8',
            'tablePrefix'=>'t_',
            'connectionString' => 'mysql:host=10.23.62.172;port=3306;dbname=db_oil_retail',
            'username' => 'jyb',
            'password' => 'YHNBGT',
        ),
        /*'dbLog'=>array(
            'class'=>'CDbConnection',
            'charset' => 'utf8',
            'tablePrefix'=>'t_',
            'connectionString' => 'mysql:host=10.23.62.172;port=3306;dbname=db_oil_retail_log',
            'username' => 'jyb',
            'password' => 'YHNBGT',
        ),*/
        'redis'=>array(
            'class'=>'CRedisCache',
            'serverConfigs'=>array(
                'dev'=>array('host'=>'10.23.217.239', 'port'=>6379, 'password'=>''),
            ),
            'getIDC'=>array('dev'),
            'setIDC'=>'dev',
            'localIDC'=>'dev',
            'getRetryCount'=>2,
            'getRetryInterval'=>0,
            'setRetryCount'=>3,
            'setRetryInterval'=>0.2
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'info, warning,error',
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
            ),
        ),
        'amqp' => array(
            'class' => 'system.components.amqp.CAMQP',
            'host'  => '10.23.90.195',
            'login' => 'jyb',
            'password'=>'Root)(*&',
        ),
        //微信企业号用户管理
        'weiXinUser'=>array(
            'class'=>'application.components.weixin.ZWeixinUser',
            'corp_id'=>'ww0f15b7e86e3ad77c',
            'secret'=>'Rdbsjpww9FvR-mypu-lG8M03fkdoeoH3I4rZuG-8Yok',
        ),
        //微信企业号消息发送
        'weiXinMsg'=>array(
            'class'=>'application.components.weixin.ZWeixinMsg',
            'corp_id'=>'ww0f15b7e86e3ad77c',
            'secret'=>'_l0Ky0Ejrp6DnFppWDxHCGGwfdAB3XfwTWyO_qavp0A',
            'agent_id'=>'1000005',
        ),

    )
);
?>
