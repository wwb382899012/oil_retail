<?php

use app\components\PageData;

class DbUtility{

    /**
     * 系统主库
     */
    const DB = 0;
    /**
     * 操作日志库
     */
    const DB_LOG = 2;
    /**
     * 重库
     */
    const DB_SLAVE = 1;
    /**
     * 历史数据库
     */
    const DB_HISTORY = 3;

    /**
     * 获取数据库
     * @param $dbType
     * @return mixed
     */
    public static function getDb($dbType = 0){
        switch($dbType){
            case 1:
                $db = Mod::app()->dbSlave;
                break;
            case 2:
                $db = Mod::app()->dbLog;
                break;
            case 3:
                $db = Mod::app()->db_history;
                break;
            default :
                $db = Mod::app()->db;
                break;
        }
        return $db;
    }

    /**
     * 开始事务
     * @param int $dbType
     * @return mixed
     */
    public static function beginTransaction($dbType = Utility::DB){
        $db = Utility::getDb($dbType);
        return $db->beginTransaction();
    }

    /**
     * 执行查询SQL并返回所有结果集
     * @param       $sql
     * @param int   $dbType
     * @param array $params 参数集
     * @return array
     */
    public static function query($sql, $dbType = 0, $params = array()){
        if(empty($sql))
            return 0;
        $db = Utility::getDb($dbType);
        $command = $db->createCommand($sql);
        $res = $command->query($params)->readAll();
        return $res;
    }

    /**
     * 执行返回单个数字的是查询，存在返回实际值，否则返回0
     * @param     $sql
     * @param     $fieldName
     * @param int $dbType
     * @return int
     */
    public static function queryOneNumber($sql, $fieldName,$dbType=0){
        $data = Utility::query($sql,$dbType);
        if(!Utility::isEmpty($data) && isset($data[0][$fieldName])){
            return $data[0][$fieldName];
        }else{
            return 0;
        }
    }

    /**
     * 查询结果是否为空
     * @param $sql
     * @return int  0：不为空，1为空
     */
    public static function queryIsEmpty($sql){
        $data = Utility::query($sql);
        if(Utility::isEmpty($data))
            return 1;else
            return 0;
    }

    /**
     * 强制在主库执行SQL查询并返回所有结果集
     * @param $sql
     * @return mixed
     */
    public static function queryInMaster($sql, $dbType = 0){
        if(empty($sql))
            return 0;
        $db = Utility::getDb($dbType);
        $db->forceMaster = true;
        $command = $db->createCommand($sql);
        $res = $command->query()->readAll();
        return $res;
    }

    /**
     * 执行SQL命令并返回影响的行数，会自动判断是否有事务，如果没有外部事务会自动启用事务，
     * 如果无外部事务，出错则返回-1；如果有外部事务，则直接抛出异常
     * 参数$sql可以是sql语句的数组，执行启用事务模式。
     * @param       $sql  可以是sql语句的数组
     * @param int   $dbType
     * @param array $params
     * @return array|int
     * @throws Exception
     */
    public static function execute($sql, $dbType = 0, $params = array()){
        if(empty($sql)){
            if(is_array($sql))
                return array(0);else
                return 0;
        }

        $db = Utility::getDb($dbType);

        $isInTrans = Utility::isInDbTrans($dbType);
        if(!$isInTrans){
            $trans = $db->beginTransaction();
        }

        try{
            if(is_array($sql)){
                foreach($sql as $v){
                    if(!empty($v)){
                        $command = $db->createCommand($v);
                        $rowCount[] = $command->execute($params);
                    }else
                        $rowCount[] = 0;
                }

            }else{
                $command = $db->createCommand($sql);
                $rowCount = $command->execute($params);
            }
            if(!$isInTrans){
                $trans->commit();
            }
            return $rowCount;
        }catch(Exception $e){
            Mod::log("Execute Sqls: ".$e->getMessage(), "error");
            if(!$isInTrans){
                try{
                    $trans->rollback();
                }catch(Exception $ee){
                }
                return -1;
            }else
                throw $e;
        }

    }

    /**
     * 执行SQL命令并返回影响的行数，如果出错，返回-1,
     * @param     $sql  需要执行的sql语句数组
     * @param int $dbType
     * @return array|int
     */
    public static function executeWithNoTransaction($sql, $dbType = 0){
        if(empty($sql)){
            if(is_array($sql))
                return array(0);else
                return 0;
        }

        $db = Utility::getDb($dbType);
        try{
            if(is_array($sql)){
                foreach($sql as $v){
                    if(!empty($v)){
                        $command = $db->createCommand($v);
                        $rowCount[] = $command->execute();
                    }else
                        $rowCount[] = 0;
                }

            }else{
                $command = $db->createCommand($sql);
                $rowCount = $command->execute();
            }
        }catch(Exception $e){
            $rowCount = -1;
        }
        return $rowCount;
    }

    /**
     * 执行SQL语句，没有事务，没有try catch
     * @param       $sql
     * @param int   $dbType
     * @param array $params
     * @return array|int
     */
    public static function executeSql($sql, $dbType = 0, $params = array()){
        if(empty($sql)){
            if(is_array($sql))
                return array(0);else
                return 0;
        }

        $db = Utility::getDb($dbType);

        if(is_array($sql)){
            foreach($sql as $v){
                if(!empty($v)){
                    $command = $db->createCommand($v);
                    $rowCount[] = $command->execute($params);
                }else
                    $rowCount[] = 0;
            }

        }else{
            $command = $db->createCommand($sql);
            $rowCount = $command->execute($params);
        }

        return $rowCount;
    }

    /**
     * 获取主数据库名
     * @return mixed
     */
    public static function getMainDbName(){
        return Mod::app()->params['main_db_name'];
    }

    /**
     * SQL注入过滤
     * @param $str
     * @return mixed
     */
    public static function filterInject($str){
        $res = preg_replace('/master|truncate|exec|select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|<|>|javascript|jscript|vbscript|&|\r|\t/', '', $str);
        if($res != $str)
            return self::filterInject($res);else
            return trim($res);
    }

    /**
     * 判断是否在事务中
     * @param int $dbType
     * @return bool
     */
    public static function isInDbTrans($dbType = 0){
        $db = Utility::getDb($dbType);
        $res = $db->getCurrentTransaction();
        if(!empty($res))
            return true;else
            return false;
    }

    /**
     * 获取sql语句中的条件语句
     * @param      $params
     * @param bool $hasWhere
     * @return string
     */
    public static function getWhereSql($params, $hasWhere = true){
        if(isset($params['pageSize']))
            unset($params['pageSize']);
        $where = "";

        if($hasWhere)
            $where = " WHERE 1=1";

        if(!is_array($params) || count($params) < 1)
            return $where;

        $conditions = array();
        foreach($params as $k => $v){
            $v = trim($v);
            if(!empty($v) || $v === 0 || $v === "0"){
                $v = addslashes($v);
                if(substr($k, strlen($k) - 1) == "*"){
                    $key = substr($k, 0, strlen($k) - 1);
                    $conditions[] = $key." like '%".$v."%'";
                }else
                    $conditions[] = $k."='".$v."'";
            }
        }
        if(count($conditions) < 1)
            return $where;
        $where = implode(" AND ", $conditions);
        if($hasWhere)
            $where = " WHERE ".$where;

        return $where;
    }

    /**
     * 获取表格的数据
     * @param string $sql  基本格式：$sql="select {col} from table";
     * @param string $fields    默认为：*
     * @param int $currPage 当前页数
     * @param int $pageSize 每页记录数
     * @param int $totalRows    当=0时表示需要计算记录数，-1时表示不计算记录数，其他则为指定记录数
     * @param int $dbType
     * @return PageData|null
     */
    public static function getTableData(string $sql,string $fields="*", int $currPage = 1,int $pageSize=10,int $totalRows= 0,int $dbType = 0):?PageData {
        if(empty($sql)){
            return null;
        }

        if($totalRows==0){
            $countSql=str_replace("{col}","count(*) as total",$sql);
            //$countSql = 'select count(*) as total from (' . str_replace('{limit}', '', str_replace('{col}', ' 1 ', $sql) . ")cou");
            $d=Utility::query($countSql,$dbType);
            $totalRows=$d[0]["total"];
        }

        $pageData = new PageData();

        if($totalRows!=0){
            $pageSize=empty($pageSize)?10:$pageSize;
            if($pageSize<0 || $pageSize>10000){
                $pageSize=20;
            }

            $page=!empty($currPage) ? $currPage : 1;
            $page=$page<1?1:$page;

            $pageData->pageSize=$pageSize;
            $pageData->page=$page;

            $totalPage = -1;

            if($totalRows>0){
                $totalPage = ceil($totalRows / $pageSize);
                $page= $totalPage < $page ? $totalPage : $page;
                $pageData->totalPages=$totalPage;
                $pageData->totalRows=$totalRows;
            }

            $begin = ($page - 1) * $pageSize;

            $dataSql=str_replace("{col}",$fields,$sql);
            $dataSql.=" limit ".$begin.",".$pageSize;

            $pageData->data=Utility::query($dataSql,$dbType);
        }

        $pageData->searchItems = [];
        return $pageData;
    }

}