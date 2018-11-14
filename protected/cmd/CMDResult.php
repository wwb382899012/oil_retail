<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/31 11:30
 * Describe：
 */

namespace app\cmd;


class CMDResult
{
    /**
     * 结果码
     * @var int
     */
    public $code;


    /**
     * 返回数据
     * @var mixed
     */
    public $data;

    /**
     * CMDResult constructor.
     * @param int|array|null $code    结果码
     * @param mixed $data   数据
     */
    public function __construct($code=null,$data=null)
    {
        if($code!==null)
        {
            if(is_array($code))
            {
                $this->code=$code["code"];
                $this->data=$code["data"];
            }else{
                $this->code = $code;
            }
        }

        if($data!==null)
            $this->data=$data;
    }

    /**
     * 获取json字符串
     * @return string
     */
    public function toJson()
    {
        $d=[
            "code"=>$this->code,
            "data"=>$this->data,
        ];
        return json_encode($d);
    }

    /**
     * 创建操作成功的结果
     * @param mixed $data
     * @return CMDResult
     */
    public static function createSuccessResult($data="操作成功")
    {
        return new CMDResult(0,$data);
    }



}