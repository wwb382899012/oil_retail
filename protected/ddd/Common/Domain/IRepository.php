<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/8 16:01
 * Describe：
 */

namespace ddd\Common\Domain;


interface IRepository
{
    function findById($id);

    function store($entity);
}