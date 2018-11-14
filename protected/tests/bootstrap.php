<?php

define("MOD_DEBUG",false);
date_default_timezone_set('Asia/Shanghai');

define('JOB_QUEUE','queue_agent_manage_job');
define('DIV_RULE_MAP','map_agent_manage_div_rule');

define("ROOT_DIR", dirname(__FILE__).'/../../');
require(ROOT_DIR . "/protected/components/Environment.php");

$env = new Environment(null, array('life_time'=>30,'env_name'=>''));

require(ROOT_DIR. $env->getModPath().'/Mod.php');
//require(ROOT_DIR. $env->getMicroPath().'/api/JmfApi.php');
Mod::setPathOfAlias("ddd",ROOT_DIR . "/protected/ddd/");
Mod::createWebApplication($env->getConfig());


//模拟登陆态
$identity=new UserIdentity('wwb', '123456');
$identity->getUser();
$identity->afterAuthenticate();
$user = new WebUser();