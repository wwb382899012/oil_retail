<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/28 19:08
 * Describe：
 *      缓存服务
 */

namespace app\ddd\Cache\Application;


use app\ddd\Cache\Domain\Cache;
use app\ddd\Cache\Domain\ICacheRepository;
use app\ddd\Cache\Repository\RedisCacheRepository;
use ddd\Common\Application\BaseService;

class CacheService extends BaseService
{
    /**
     * @var ICacheRepository
     */
    protected $repository;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->repository=new RedisCacheRepository();
    }


    /**
     * 设置单键缓存
     * @param $key
     * @param $value
     * @param int $expire
     * @param null $dependency
     */
    public function setCache($key,$value,$expire=0,$dependency=null)
    {
        $cache=new Cache($key,$value,$expire,$dependency);
        $this->repository->setCache($cache);
    }

    /**
     * 获取缓存项
     * @param $key
     * @return Cache|null
     */
    public function getCache($key)
    {
        return $this->repository->getCache($key);
    }

    /**
     * 获取缓存值
     * @param $key
     * @return bool|null
     */
    public function getCacheValue($key)
    {
        $cache=$this->repository->getCache($key);
        if(empty($cache))
            return false;
        return $cache->getValue();

    }

    /**
     * 清除缓存
     * @param $key
     */
    public function clearCache($key)
    {
        $this->repository->deleteCache($key);
    }

}