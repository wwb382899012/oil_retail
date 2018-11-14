<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/23 17:09
 * Describe：
 *      分页数据类
 */

namespace app\components;


class PageData
{
    /**
     * 分页大小
     * @var int
     */
    public $pageSize=0;

    /**
     * 总页数
     * @var int
     */
    public $totalPages=0;

    /**
     * 当前页
     * @var int
     */
    public $page=0;

    /**
     * 数据总行数
     * @var int
     */
    public $totalRows=0;

    /**
     * 实际数据
     * @var array
     */
    public $data=[];

    /**
     * 查询条件
     * @var array
     */
    public $searchItems=[];


}