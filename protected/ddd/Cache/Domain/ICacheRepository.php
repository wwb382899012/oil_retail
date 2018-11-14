<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/28 18:48
 * Describe：
 */

namespace app\ddd\Cache\Domain;


interface ICacheRepository
{
    /**
     * 获取缓存项
     * @param $key
     * @return Cache|null
     */
    public function getCache($key);

    /**
     * 设置缓存
     * @param Cache $cache
     */
    public function setCache(Cache &$cache);

    /**
     * 删除缓存
     * @param $key
     * @return mixed
     */
    public function deleteCache($key);
}