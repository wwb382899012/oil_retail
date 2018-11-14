<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/28 15:08
 * Describe：
 *  缓存项信息
 */

namespace app\ddd\Cache\Domain;


class Cache
{
    public $key;

    protected $value;

    public $expire=0;

    protected $dependency=[];

    protected $status=true;

    /**
     * 构造函数
     * Cache constructor.
     * @param string $key
     * @param mixed $value
     * @param int $expire 过期时间，单位：秒
     * @param string|string[] $dependency 缓存依赖
     */
    public function __construct($key=null,$value=null,$expire=0,$dependency=null)
    {
        if(!empty($key))
            $this->key=$key;
        if($value!==null)
            $this->value=$value;

        if(!empty($expire))
            $this->expire=$expire;

        if(!empty($dependency))
        {
            if(is_array($dependency))
            {
                foreach ($dependency as $item)
                {
                    $this->addDependency($item);
                }
            }
            else
                $this->addDependency($dependency);
        }
    }

    public function __sleep()
    {
        // TODO: Implement __sleep() method.
        return ["key","value","expire","dependency"];
    }

    /**
     * 设置值
     * @param $value
     */
    public function setValue($value)
    {
        $this->status=true;
        $this->value=$value;
    }

    /**
     * 获取缓存值
     * @return bool|null
     */
    public function getValue()
    {
        if($this->status)
            return $this->value;
        else
            return false;
    }

    /**
     * 获取缓存
     * @return array
     */
    public function getDependency()
    {
        return $this->dependency;
    }

    /**
     * 增加缓存
     * @param $key
     */
    public function addDependency($key)
    {
        if(empty($key))
            return;
        $this->dependency[$key]=$key;
    }

    /**
     * 设置缓存项值
     * @param $key
     * @param $value
     */
    public function setDependencyValue($key,$value)
    {
        $this->dependency[$key]=$value;
    }

    /**
     * @param bool $value
     */
    public function setStatus($value)
    {
        $this->status=$value;
    }


}