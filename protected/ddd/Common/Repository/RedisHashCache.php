<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/27 15:26
 * Describe：
 */

namespace app\ddd\Common\Repository;


use ddd\Common\Domain\BaseEntity;

trait RedisHashCache
{
    protected static $cacheKeyPrefix="oil_retail_";

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
     * @param string|null $fieldName
     * @param string $key
     */
    public function clearCache($fieldName=null,$key="")
    {
        if(!empty($fieldName))
            \Utility::hDelCache(static::getCacheKey($key),$fieldName);
        else
            \Utility::clearCache(static::getCacheKey($key));
    }

    /**
     * 设置Redis缓存
     * @param string $fieldName
     * @param $entity
     * @param string $key
     */
    protected function setCache($fieldName,$entity,$key="")
    {
        $value=serialize($entity);
        \Utility::hSetCache(static::getCacheKey($key),$fieldName,$value);
    }

    /**
     * 从缓存获取对象
     * @param string $fieldName
     * @param string $key
     * @return bool|BaseEntity
     */
    protected function getEntityFromCache($fieldName,$key="")
    {
        if(\Utility::hExists(static::getCacheKey($key),$fieldName))
        {
            $data= \Utility::hGetCache(static::getCacheKey($key),$fieldName);
            return unserialize($data);
        }
        else
            return false;
    }


    /**
     * 从缓存获取所有对象
     * @param string $key
     * @return array|bool
     */
    protected function getAllEntityFromCache($key="")
    {
        $data = [];
        $vArr = \Utility::hValsCache(static::getCacheKey($key));
        if(!empty($vArr) && is_array($vArr)){
            foreach ($vArr as $v) {
                $data[] = unserialize($v);
            }
            
            return $data;
        }
        
        return false;
    }

}