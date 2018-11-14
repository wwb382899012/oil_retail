<?php
/**
 * Created by youyi000.
 * DateTime: 2018/9/4 15:31
 * Describe：
 */

//namespace app\components;


class MyUrlRule extends \CBaseUrlRule
{
    /**
     * web前端的虚拟controller配置
     * @var array
     */
    public $webControllersConfig=[];

    public function __construct()
    {
        $this->webControllersConfig=Mod::app()->params["web_controllers"];
        if(!is_array($this->webControllersConfig))
            $this->webControllersConfig=[];
    }


    public function createUrl($manager, $route, $params, $ampersand)
    {
        // TODO: Implement createUrl() method.
        return false;
    }

    public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
    {
        // TODO: Implement parseUrl() method.
        //'/^(\w+)(-(\d+))(-(\d+))?$/'
        if (preg_match_all('/(\w+)/', $pathInfo, $matches))
        {
            //var_dump($matches);
            if(is_array($matches))
            {
                if(is_array($matches[1]))
                {
                    if(key_exists($matches[1][0],$this->webControllersConfig))
                    {
                        //$actions=$this->webControllersConfig[$matches[1][0]];
                        return "/site/web";
                    }
                }
            }
        }

        return false;
    }

}