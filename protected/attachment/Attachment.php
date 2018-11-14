<?php

/**
 * Created by youyi000.
 * DateTime: 2016/6/24 15:57
 * Describe：
 */
class Attachment{

    //油企附件信息
    const C_OIL_COMPANY = 'oilCompany';
    //油站申请附件信息
    const C_OIL_STATION_APPLY = 'oilStationApply';
    //油站附件信息
    const C_OIL_STATION = 'oilStation';
    //油价申请附件信息
    const C_OIL_PRICE_APPLY = 'oilPriceApply';

    /**
     * 当前配置信息
     *  array("key"=>"customer","filePath"=>"/upload/customer/","mapName"=>"customer_attachment_type","tableName"=>"t_customer_attachment","baseFieldName"=>"customer_id",)
     * @var
     */
    public $config;

    public $type = 0;

    /**
     * 附件的配置信息
     * @var array
     */
    public $configs = array(
        //油企附件信息
        'oilCompany'      => [
            "key"           => "oilCompany",
            "filePath"      => "/data/oil_retail/upload/oil_company/",
            "mapName"       => "oil_company_attachment_type",
            "tableName"     => "t_oil_company_attachment",
            "baseFieldName" => "base_id"
        ],
        //油站申请附件信息
        'oilStationApply' => [
            "key"           => "oilStationApply",
            "filePath"      => "/data/oil_retail/upload/oil_station_apply/",
            "mapName"       => "oil_station_apply_attachment_type",
            "tableName"     => "t_oil_station_apply_attachment",
            "baseFieldName" => "base_id"
        ],
        //油站附件信息
        'oilStation'      => [
            "key"           => "oilStation",
            "filePath"      => "/data/oil_retail/upload/oil_station/",
            "mapName"       => "oil_station_attachment_type",
            "tableName"     => "t_oil_station_attachment",
            "baseFieldName" => "base_id"
        ],
        //油价申请附件信息
        'oilPriceApply'   => [
            "key"           => "oilPriceApply",
            "filePath"      => "/data/oil_retail/upload/oil_price_apply/",
            "mapName"       => "oil_price_apply_attachment_type",
            "tableName"     => "t_oil_price_apply_attachment",
            "baseFieldName" => "base_id"
        ],
    );

    /**
     * 附件类型信息
     *  array("id"=>"1","name"=>"营业执照","maxSize"=>40,"fileType"=>"|jpg|png|jpeg|bmp|")
     * @var
     */
    public $typeInfo;

    /**
     * 当前类型的附件配置信息
     * @var
     */
    public $typeConfig;
    public $map;

    public $file = array(
        "id"       => 0,
        "name"     => "",
        "fileUrl"  => "",
        "filePath" => "",
        "status"   => 0
    );


    function __construct($key){
        $this->config = $this->configs[$key];
        $this->map = Map::$v;
        $this->typeConfig = $this->map[$this->config["mapName"]];
        $this->init();
    }

    public function init(){

    }

    /**
     * 保存上传文件主方法
     * @param      $baseId
     * @param      $type
     * @param      $file
     * @param int  $userId
     * @param null $extras
     * @param int  $isWordToPdf 是否自动转PDF
     * @return int|string
     */
    public function saveFile($baseId, $type, $file, $userId = 0, $extras = null, $isWordToPdf = 0){
        //$file=$_FILES["files"];

        $this->type = $type;

        if(empty($file)){
            return "文件不能为空！";
        }

        $this->typeInfo = $this->getTypeInfo();
        //print_r($this->typeInfo);die;

        $fileName = $file["name"][0];
        //echo $fileName;
        //print_r($fileName);die;
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $originalName = $fileName;//pathinfo($fileName, PATHINFO_BASENAME);
        //print_r($fielname);die;

        $res = $this->checkFileIsPermit($file["size"][0], $extension);
        if($res != 1)
            return $res;

        $filePath = $this->getFilePath($baseId);
        //$name=$this->typeInfo["name"]."_".Utility::getRandomKey() . ".".$extension;
        //$name=$fName."_".Utility::getRandomKey() . ".".$extension;
        $name = $baseId."_".$type."_".time()."_".Utility::getRandomKey().".".$extension;
        $filePath = $filePath.$name;
        try{
            //move_uploaded_file($file["tmp_name"][0],iconv("UTF-8","GB2312",ROOT_DIR.$filePath));
            move_uploaded_file($file["tmp_name"][0], $filePath);
            if($isWordToPdf && ($extension == "doc" || $extension == "docx")){
                //AMQPService::publishFileWordToPDF($filePath);
                Utility::wordToPdf($filePath);
            }
        }catch(Exception $e){
            Mod::log('文件上传失败,message:'.$e->getMessage(), 'error');
            return $e->getMessage();
        }

        $this->file["name"] = $originalName;
        $this->file["fileUrl"] = $filePath;
        $this->file["filePath"] = $filePath;

        //保存信息
        $res = $this->saveAttachmentLog($baseId, $userId, $extras, $this->typeInfo["multi"]);


        return $res;
    }


    /**
     * 获取文件存储相对于根目录的路径
     * @param $baseId
     * @return string
     */
    public function getFilePath($baseId){
        $n = 100;
        $filePath = $this->config["filePath"];
        $k = intval($baseId / $n);
        $filePath .= ($k * $n + 1)."-".($k * $n + $n)."/".$baseId."/";
        Utility::checkDirectory($filePath);
        return $filePath;
    }

    /**
     * 获取文件类别相关的信息，一般在子类中重写
     * @return array
     */
    public function getTypeInfo(){
        return $this->map[$this->config["mapName"]][$this->type];
    }

    /**
     * 判断文件是否允许上传
     * @param $size
     * @param $fileExtension
     * @return int|string
     */
    public function checkFileIsPermit($size, $fileExtension){
        //$typeInfo=$this->getTypeInfo();
        if($size > $this->typeInfo["maxSize"] * 1024 * 1024){
            return "文件大小超出最大限制，最大为".$this->typeInfo["maxSize"]."M";
        }

        if(!(strpos("#".$this->typeInfo["fileType"], $fileExtension) > 0)){
            return "文件类型不是允许的上传类型，允许的文件类型为：".$this->typeInfo["fileType"];
        }

        return 1;

    }

    /**
     * 保存附件信息
     * @param      $baseId
     * @param int  $userId
     * @param null $extras
     * @return int|string
     */
    protected function saveAttachmentLog($baseId, $userId = 0, $extras = null){
        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try{
            $sqls = array();

            $fields = "";
            $value = "";
            $query = "";
            if(is_array($extras) && count($extras) > 0){
                foreach($extras as $k => $v){
                    $fields .= ",".$k;
                    $value .= ",'".$v."'";
                    $query .= " and ".$k."=".$v;
                }
            }


            if($this->typeInfo["multi"] != 1){
                $sql = "update ".$this->config["tableName"]." set status=0,update_time=now(),update_user_id='".$userId."' where type=".$this->type." and ".$this->config["baseFieldName"]."=".$baseId." and status>=1".$query;
                Utility::executeSql($sql);
            }

            $sql = "insert into ".$this->config["tableName"]."(".$this->config["baseFieldName"].",type,name,file_path,file_url,status,create_time,create_user_id,update_time,update_user_id".$fields.")
                values(".$baseId.",".$this->type.",'".$this->file["name"]."',:filePath,:fileUrl,1,now(),'".$userId."',now(),'".$userId."'".$value.")";
            Utility::executeSql($sql, Utility::DB, array(
                "filePath" => $this->file["filePath"],
                "fileUrl"  => $this->file["fileUrl"]
            ));

            $idName = $this->getIdFiledName();

            $sql = "select ".$idName." from ".$this->config["tableName"]." where type=".$this->type." and ".$this->config["baseFieldName"]."=".$baseId." and status=1".$query." order by ".$idName." desc limit 1";
            $data = Utility::query($sql);

            $trans->commit();
            $this->file["id"] = $data[0][$idName];
            return 1;
        }catch(Exception $e){
            try{
                $trans->rollback();
            }catch(Exception $ee){
            }
            return $e->getMessage();
        }
    }

    protected function getIdFiledName(){
        $idName = empty($this->config["idFieldName"]) ? "id" : $this->config["idFieldName"];
        return $idName;
    }

    /**
     * 获取关联id字段名
     * @return string
     */
    protected function getBaseIdFiledName(){
        $baseFieldName = empty($this->config["baseFieldName"]) ? "base_id" : $this->config["baseFieldName"];
        return $baseFieldName;
    }

    /**
     * 获取文件的读取路径
     * @param $id
     * @return null
     */
    public function getFileReadPath($id){
        $idName = $this->getIdFiledName();
        $sql = "select * from ".$this->config["tableName"]." where ".$idName."=".$id."";
        $data = Utility::query($sql);
        //Mod::log("hz_log attachment data:".json_encode($data));
        if(Utility::isNotEmpty($data))
            return $data[0]["file_path"];else
            return null;
    }

    /**
     * 判断指定类别的文件是否上传
     * @param $type
     * @return null
     */
    public function checkIsExistWithType($type){
        $sql = "select * from ".$this->config["tableName"]." where type=".$type." and status=1";
        $data = Utility::query($sql);
        if(Utility::isNotEmpty($data))
            return true;else
            return false;
    }

    /**
     * 删除文件，同时更新文件记录的状态为删除状态
     * @param $id
     * @return bool
     */
    public function deleteFile($id){
        $idName = $this->getIdFiledName();
        $sql = "select * from ".$this->config["tableName"]." where ".$idName."=".$id."";
        $data = Utility::query($sql);
        if(Utility::isNotEmpty($data)){
            if(!key_exists($data[0]["type"], $this->typeConfig)){
                Mod::log("非法删除表".$this->config["tableName"]."中".$id."的文件", "error");
                return false;
            }
            $res = @unlink($data[0]["file_path"]);
            if($res){
                $sql = "update ".$this->config["tableName"]." set status=-1 where ".$idName."=".$id."";
                $res = Utility::execute($sql);
                if($res != -1){
                    return true;
                }else{
                    Mod::log("更新表".$this->config["tableName"]."的文件".$id."的状态出错", "error");
                    return false;
                }

            }else{
                Mod::log("删除表".$this->config["tableName"]."中标识为".$id."的文件出错", "error");
                return false;
            }
        }
        return true;
    }


    /**
     * 获取指定baseId的所有正常的附件信息
     * @param      $baseId
     * @param null $type
     * @return array
     */
    public function getAttachments($baseId, $type = null){
        if(empty($baseId))
            return array();

        if(empty($this->config))
            return array();

        $condition = "";
        if(!empty($type))
            $condition = " and type=".$type;


        $sql = "select * from ".$this->config["tableName"]." where ".$this->config["baseFieldName"]."=".$baseId." ".$condition." and status=1 ";
        $data = Utility::query($sql);
        $attachments = array();
        foreach($data as $v){
            $attachments[$v["type"]][] = $v;
        }

        return $attachments;
    }

}