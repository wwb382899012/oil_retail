<?php

/**
 * @Name            微信关联信息
 * @DateTime        2018年9月3日 17:27:17
 * @Author          youyi000
 */

namespace ddd\Customer\Domain;

use ddd\Common\Domain\BaseEntity;


class WXRelation extends BaseEntity
{
    #region property
    
    /**
     * 小程序或企业号标识 
     * @var   string
     */
    public $wx_identity;
    
    /**
     * 微信标识 
     * @var   string
     */
    public $open_id;
       

    #endregion
    
    /**
     * 创建工厂方法
     */
    public static function create($openId, $wxIdentity=\CustomerWxRelation::MINI_PROGRAM)
    {
        $entity =  new static();
        if(empty($openId))
            return $entity;
        
        $entity->wx_identity = $wxIdentity;
        $entity->open_id     = $openId;

        return $entity;
    }
    
}

