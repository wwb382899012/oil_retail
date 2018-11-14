<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/28 15:16
 * Describe：
 */

namespace app\ddd\Cache\Repository;


use app\ddd\Cache\Domain\Cache;
use ddd\Common\Repository\BaseRepository;

class RedisCacheRepository extends BaseRepository
{


    /**
     * @var \CRedisCache
     */
    public $redis;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->redis=\Mod::app()->redis;
    }


    /**
     * 获取Key
     * @param $key
     * @return Cache|null
     * @throws \Exception
     */
    public function getCache($key)
    {
        if($this->redis->exists($key))
        {
            $data=$this->redis->get($key);
            $cache=unserialize($data);
            if(!is_a($cache,Cache::class))
            {
                $this->redis->delete($key);
                return null;
            }
            //$cache=new RedisCache();
            $status=$this->getCacheStatus($cache);
            if(!$status)
                $this->redis->delete($key);
            $cache->setStatus($status);

            return $cache;
        }
        else
            return null;
    }

    /**
     * 获取缓存依赖的状态
     * @param Cache $cache
     * @return bool
     * @throws \Exception
     */
    protected function getCacheStatus(Cache $cache)
    {
        $dependency=$cache->getDependency();
        if(is_array($dependency))
        {
            foreach ($dependency as $key=>$value)
            {
                $res=$this->redis->get($key);
                if($res===false || $res!=$value)
                    return false;
            }
        }
        return true;
    }

    /**
     * 设置Redis缓存
     * @param Cache $cache
     * @throws \Exception
     */
    public function setCache(Cache &$cache)
    {
        $dependency=$cache->getDependency();
        foreach ($dependency as $key=>$item)
        {
            if($this->redis->exists($key))
                $cache->setDependencyValue($key,$this->redis->get($key));
            else
            {
                $value=time().random_int(100,999);
                $this->redis->set($key,$value);
                $cache->setDependencyValue($key,$value);
            }
        }
        $data=serialize($cache);
        if ($cache->expire > 0)
            $this->redis->setex($cache->key, $cache->expire, $data);
        else
            $this->redis->set($cache->key,$data);
    }


    /**
     * 删除缓存
     * @param $key
     */
    public function deleteCache($key)
    {
        if($this->redis->exists($key))
            $this->redis->delete($key);
    }
}