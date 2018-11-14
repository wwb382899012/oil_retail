<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/23 19:40
 * Describe：
 */

namespace app\components;


class ExcelHelper
{
    /**
     * 读取Excel文件内容
     * @param string $fileName 文件路径名
     * @param int $sheetIndex
     * @return array|null
     * @throws \Exception
     */
    public static function readExcel($fileName,$sheetIndex=0)
    {
        $PHPExcel = new \PHPExcel();
        /**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/
        $PHPReader = new \PHPExcel_Reader_Excel2007();

        if(!$PHPReader->canRead($fileName)){
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if(!$PHPReader->canRead($fileName))
            {
                //echo 'no Excel';
                \Utility::log("无法读取Excel文件：".$fileName);
                return null;
            }
        }

        $PHPExcel=$PHPReader->load($fileName);
        $content = $PHPExcel->getSheet($sheetIndex)->toArray();
        $data=array();
        if(count($content)>0)
        {
            $data=array();
            $header=$content[0];
            unset($content[0]);
            foreach($content as $v)
            {
                $item=array();
                foreach($v as $ek=>$ev)
                {
                    $item[$header[$ek]]=$ev;
                }
                $data[]=$item;
            }
        }

        return $data;
    }

}