<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/27 11:59
 * Describe：
 */

namespace app\ddd\Common\Repository;


use app\ddd\Cache\Application\CacheService;

trait RedisCache
{

    protected static $cacheKeyPrefix="oil_retail_";

    /**
     * @var CacheService
     */
    protected $cacheService;

    /**
     * 缓存过期时长，单位秒，当小于0时，表示不使用缓存
     * @var int
     */
    protected $expire_seconds=0;

    /**
     * 最长缓存数量限制
     * @var int
     */
    //protected $max_cache_items=100;


    /**
     * @return CacheService
     */
    protected function getCacheService()
    {
        if(empty($this->cacheService))
            $this->cacheService=new CacheService();
        return $this->cacheService;
    }

    /**
     * 获取缓存key
     * @param string $key
     * @return string
     */
    protected function getCacheKey($key="")
    {
        return static::$cacheKeyPrefix.__CLASS__.$key;
    }

    /**
     * 清除缓存
     * @param $key
     */
    public function clearCache($key)
    {
        $key=$this->getCacheKey($key);

        if(!empty($key))
            $this->getCacheService()->clearCache($key);
    }

    /**
     * 设置Redis缓存
     * @param $key
     * @param object $entity
     * @param null $expire 过期时长，单位秒
     * @param string|string[] $dependency 依赖项
     */
    protected function setCache($key,$entity,$expire=null,$dependency=null)
    {
        if($this->expire_seconds<0)
            return;
        $key=$this->getCacheKey($key);
        if(empty($expire))
            $expire=$this->expire_seconds;
        $value=serialize($entity);
        $this->getCacheService()->setCache($key,$value,$expire,$dependency);
        //\Utility::setCache($key,$value,$expire);
    }

    /**
     * 从缓存获取对象
     * @param $key
     * @return bool|mixed
     */
    protected function getEntityFromCache($key)
    {
        if($this->expire_seconds<0)
            return false;
        $key=$this->getCacheKey($key);
        $data= $this->getCacheService()->getCacheValue($key);
        if(!empty($data))
            return unserialize($data);
        else
            return false;
    }

}