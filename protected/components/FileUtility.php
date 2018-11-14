<?php

class FileUtility{

    /**
     * 获取excel 的数据
     * @param array  $fieldNames [field=>name]
     * @param string $filePath
     * @return array
     */
    public static function getExcelData(array $fieldNames,string $filePath):array {
        $data = static::getOriginalExcelData($filePath);
        if(empty($data) && count($data) < 2){
            return [];
        }

        $keys = [];
        foreach($data[0] as $key => $name){
            $field = array_search($name, $fieldNames);
            if($field){
                $keys[$key] = $field;
            }
        }

        array_shift($data);

        foreach($data as & $datum){
            $datum = array_filter($datum,function($v, $k) use ($keys){
                return isset($keys[$k]);
            },ARRAY_FILTER_USE_BOTH);
            $datum = array_combine($keys,$datum);

            //去除字符串首尾处的空白字符
            $datum = array_map(function($v){
                return trim($v);
            }, $datum);
        }

        return $data;
    }

    /**
     * 获取excel未加工的原始数据
     * @param string $filePath
     * @return array
     */
    public static function getOriginalExcelData(string $filePath):array {
        $extPath = implode(DIRECTORY_SEPARATOR, [
            Mod::app()->getExtensionPath(),
            'PHPExcel',
            'PHPExcel',
            'IOFactory.php'
        ]);
        require_once $extPath;

        try{
            $inputFileType = PHPExcel_IOFactory::identify($filePath);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($filePath);

            //读取excel文件中的第一个工作表
            $sheet = $objPHPExcel->getSheet(0);
            return $sheet->toArray('');
        }catch(Exception $e){
            Mod::log($e->getMessage(), CLogger::LEVEL_ERROR);
        }

        return [];
    }
}