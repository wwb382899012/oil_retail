<?php
/**
 * Desc: 外部接口
 * User: susiehuang
 * Date: 2018/7/18 0018
 * Time: 11:28
 */

class OutController extends Controller
{
    /**
     * 接口访问的端口要求
     * @var []
     */
    public $hostPorts = ["9801","8089"];

    public static $cmdMap = array(
        // 下单
        "91010001" => "OrderCMD",
        "91010002" => "OrderCMD",
        "91010003" => "OrderCMD",
        "91010004" => "OrderCMD",

        //物流
        "91020001" => "LogisticsCMD",
        #"91010002" => "LogisticsCMD",
        "91020003" => "LogisticsCMD",
        "91020004" => "LogisticsCMD",
        "91020005" => "LogisticsCMD",
        "91020006" => "LogisticsCMD",
        "91020007" => "LogisticsCMD",
        "91020008" => "LogisticsCMD",

        //客户
        "91040001" => "CustomerCMD",
        "91040002" => "CustomerCMD",
        "91040003" => "CustomerCMD",
        "91040004" => "CustomerCMD",
        "91040005" => "CustomerCMD",
        "91040006" => "CustomerCMD",
        "91040007" => "CustomerCMD",

        //油站
        "91030001" => "OilStationCMD",
        "91030002" => "OilStationCMD",
        "91030003" => "OilStationCMD",
        "91030004" => "OilStationCMD",
    );

    /**
     * 过滤器，自定义过滤方法：端口校验；必须Post方式提交；
     * @return array
     */
    public function filters() {
        return array(
            "interfaceAction",
            "postOnly + index"
        );
    }

    /**
     * @param CFilterChain $filterChain
     * @throws CHttpException
     */
    public function filterInterfaceAction($filterChain) {
        if (in_array($_SERVER['SERVER_PORT'], $this->hostPorts))
            $filterChain->run();
        else
            throw new CHttpException(403, "非法访问");
    }

    /**
     * 返回结果
     * @param \app\cmd\CMDResult $result
     */
    public function returnResult(\app\cmd\CMDResult $result) {
        echo $result->toJson();
        Mod::app()->end();
    }

    /**
     * 对外接口入口
     */
    public function actionIndex() {
        /*$req = Mod::app()->request;
        if (!$req->isPostRequest || !in_array($_SERVER['SERVER_PORT'],self::$hostPort)) {
            $this->returnOutError(CMDCode::CODE_METHOD_PORT_INVALID);
        }*/

        $post = file_get_contents("php://input");
        Mod::log("Request RAW POST:[" . $_SERVER['SERVER_PORT'] . "]" . $post);
        $params = json_decode($post, true);

        if (empty($params)) {
            $this->returnResult(new \app\cmd\CMDResult(CMDCode::CODE_NO_POST_DATA));

        }

        $cmd = $params["cmd"];
        //if (!isset(self::$cmdMap[$cmd]))
        if (!key_exists($cmd, self::$cmdMap)) {
            $this->returnResult(new \app\cmd\CMDResult(CMDCode::CODE_CMD_INVALID));
        }

        $res = $this->invoke($cmd, $params);

        $this->returnResult($res);
    }

    /**
     * @param $cmd
     * @param $params
     * @return \app\cmd\CMDResult
     * @throws \Exception
     */
    private function invoke($cmd, $params) {
        $service = new self::$cmdMap[$cmd];
        Mod::log("INVOKE:" . self::$cmdMap[$cmd] . ", and Params is :" . json_encode($params));
        try {
            return $service->invoke($params);
        } catch (Exception $e) {
            Mod::log('INVOKE Error[' . json_encode($params) . ']:' . $e->getMessage(), "error");
            return new \app\cmd\CMDResult(CMDCode::CODE_CMD_ERROR);
        }
    }
}