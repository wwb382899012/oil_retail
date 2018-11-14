<?php


class CheckUtility{

    /**
     * 判断查询的id参数
     * @param      $id
     * @param bool $ignoreZero
     * @return bool
     */
    public static function isNumeric($id, bool $ignoreZero = false):bool {
        if (!$ignoreZero && empty($id)){
            return false;
        }
        return is_numeric($id);
    }

    /**
     * 判断是否自然数型字符串
     * @param $id
     * @return bool
     */
    public static function isIntString($id):bool {
        return is_numeric($id);
    }

    /**
     * 判断是否为空的数据表，主要针对Sql查询出的数据集
     * @param $data
     * @return bool
     */
    public static function isEmpty($data):bool {
        if (!is_array($data) || count($data) < 1)
            return true;
        else
            return false;
    }

    /**
     * 判断是否为非空的数据表，主要针对Sql查询出的数据集
     * @param $data
     * @return bool
     */
    public static function isNotEmpty($data):bool {
        if (is_array($data) && count($data) > 0)
            return true;
        else
            return false;
    }

    /**
     * 检查数组里是否有指定的键名或索引。
     * 仅仅搜索第一维的键。 多维数组里嵌套的键不会被搜索到。
     * @param array $params
     * @param array $required
     * @param bool  $ignoreZero
     * @return bool
     */
    public static function checkRequiredParams(array $params , array $required,bool $ignoreZero = false):bool {
        if(empty($required) || empty($params)){
            return false;
        }

        foreach($required as $key){
            if(!isset($params[$key])){
                return false;
            }

            if(empty($params[$key])){
                if($ignoreZero && is_numeric($params[$key])){
                    continue;
                }
                return false;
            }
        }

        return true;
    }

    /**
     * 检查数组的每个值是否为空
     * @param $data
     * @return bool
     */
    public static function checkArrayAllValueIsEmpty(array $data):bool {
        if(static::isEmpty($data)){
            return true;
        }

        foreach($data as $value){
            $value = trim($value);
            if(!empty($value)){
                return false;
            }
        }

        return true;
    }
}