<?php

use app\components\PageData;

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
    /**
     * 需要授权的actions
     * @var array
     */
    public $authorizedActions=array();
    /**
     * 访问可以访问的actions
     * @var array
     */
    public $guestActions=array();
    /**
     * 可以完全访问的actions
     * @var array
     */
    public $publicActions=array();

    /**
     * 需要忽略权限强验证的action
     * @var string  不同的action以逗号（,）隔开，例如：login,loginOut
     */
    public $filterActions="";

    //public $mainActive;

    /**
     * @var \app\components\SystemModuleItem
     */
    public $moduleItem;

    /**
     * 模块权限码
     * @var string
     */
    public $rightCode="";

    /**
     * 树形菜单码
     * @var string
     */
    public $treeCode="";


    public $userId=0;

    /**
     * 是否是移动版视图
     * @var bool
     */
    public $isMobileView=false;

    public $mainUrl="";
    public $backUrl="";

    /**
     * 移动版视图后缀名
     * @var string
     */
    public $mobileViewSuffix="";

    /**
     * 是否新窗口打开
     * @var int
     */
    public $isExternal=1;


    public $map;

    public function init()
    {
        $this->backUrl=$_REQUEST["url"];
        $this->mainUrl = "/" . $this->getId() . "/";
        $this->layout = "main";
        $this->isMobileView = $this->isMobile();
        $this->pageInit();

    }

    /**
     * 页面初始化代码
     */
    public function pageInit()
    {

    }

    /**
     * 初始化权限码，对于同一个Controller使用多个权限码时，在此修改
     */
    public function initRightCode()
    {

    }

    /**
     * 获取Search查询条件
     * @return mixed
     */
    public function getSearch():array {
        $search = Mod::app()->request->getParam('search');
        if(empty($search)){
            $search = $this->getRestParam('search',[]);
        }

        $id = empty($this->action) ? "index" : $this->action->getId();
        $key=$this->id."_".$id."_search";

        if(empty($search)){
            $search=$_COOKIE[$key];
            if(!empty($search)){
                $search = json_decode($search, true);
            }
        }else{
            setcookie($key, json_encode($search), time()+1500);
        }

        return (array) $search;
    }

    /**
     * 获取查询页数
     * @return int
     */
    public function getSearchPage()
    {
        $page = Mod::app()->request->getParam('page');
        if (empty($page))
        {
            $page = $this->getRestParam('page', 1);
        }
        $key = $this->id . "_" . $this->action->getId() . "_page";

        //从本页面代理过来，不使用page缓存，所有的查询结果都从第一页开始; 从其他页面代理过来，使用page缓存，默认当前页是缓存页面
        /*$referrer = Mod::app()->getRequest()->getUrlReferrer();
        $referrer = strpos($referrer, '?') !== false ? strstr($referrer, '?', true) : $referrer;
        $requestUrl = Utility::getRequestUrl();
        $requestUrl = strpos($requestUrl, '?') !== false ? strstr($requestUrl, '?', true) : $requestUrl;

        if ($referrer != $requestUrl)
        {
            $page = $_COOKIE[$key];
        }*/

        $page = empty($page) ? 1 : $page;
        setcookie($key, $page, time() + 1500);

        return (int)$page;
    }

    /**
     * 获取查询每页记录数
     * @return int
     */
    public function getSearchPageSize()
    {
        $pageSize = Mod::app()->request->getParam('pageSize');
        if(empty($pageSize)){
            $pageSize = $this->getRestParam('pageSize', Mod::app()->params["pageSize"]);
        }

        $key=$this->id."_".$this->action->getId()."_pageSize";
        if(empty($pageSize)){
            $pageSize=$_COOKIE[$key];
            if(!empty($pageSize)){
                $pageSize=json_decode($pageSize,true);
            }
        }
        else{
            setcookie($key, json_encode($pageSize), time()+1500);
        }

        return (int) $pageSize;
    }



    public function goHome()
    {
        Mod::app()->request->redirect("/");
    }

    public function filters(){
        return array(
            'accessControl',
        );
    }

    /**
     * 获取当前模块授权的actions数组
     * @return array
     * @throws Exception
     */
    public function getAuthorizedActions()
    {
        if(!empty($this->filterActions))
            $this->authorizedActions=array_merge($this->authorizedActions,explode(",",$this->filterActions));

        $actions=\app\ddd\Admin\Application\Right\AuthorizeService::service()->getActionCodesWithModuleCode($this->rightCode);
        //$actions=SystemUser::getAuthorizedActions($this->rightCode);
        if(is_array($actions) && count($actions)>0)
            $actions=array_merge($this->authorizedActions,$actions);
        else
            $actions=$this->authorizedActions;

        if(!empty($this->rightCode))
            $actions=empty($actions)?array('yii'):$actions;
        return $actions;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function accessRules()
    {
        $this->userId=Utility::getNowUserId();
        $this->initRightCode();

        /*if(empty($this->rightCode))
            $this->rightCode=$this->id;*/
        //var_dump($this->id);
        if(empty($this->treeCode))
            $this->treeCode=$this->rightCode;

        $actions=$this->getAuthorizedActions();

        return array(
            array('allow',
                'actions'=>empty($this->publicActions)?array('yii'):$this->publicActions,
                'users'=>array('*'),
            ),
            array('allow',
                'actions'=>empty($this->guestActions)?array('yii'):$this->guestActions,
                'users'=>array('?'),
            ),
            array('allow',
                'actions'=>$actions,
                'users'=>array('@'),
            ),
            array('deny',
                'actions'=>array(),
                'users'=>array('*'),
            ),
        );
    }


    /**
     * Renders a view with a layout.
     * @param string $view
     * @param [] $data 传递给View的数据，默认为null
     * @param bool $return
     * @return null|string|string[]|void
     */
    public function render($view,$data=null,$return=false)
    {
        $this->isExternal=$_GET["t"];
        if($this->isExternal && $this->layout == "main")
            $this->layout="emptyMain";
        else
        {
            if ($this->isMobileView && $this->layout == "main")
            {
                $this->layout = "mobileMain";
            }
        }

        parent::render($view,$data,$return);
    }

    /**
     * 显现错误信息页面
     * @param string $msg
     * @param string $backUrl
     * @param bool $ignoreAjax
     */
    public function renderError($msg="",$backUrl="",$ignoreAjax=false)
    {
        if(!$ignoreAjax && Mod::app()->request->isAjaxRequest)
        {
            // ajax 请求的处理方式
            $this->returnError($msg);
        }
        else
        {
            if(empty($backUrl))
                $backUrl="/".$this->getId()."/";
            $this->render("/layouts/error", array("msg" => $msg, "backUrl" => $backUrl));
            Mod::app()->end();
        };

    }

    /**
     * 获取sql语句中的条件语句
     * @param $params
     * @param bool $hasWhere
     * @return string
     */
    public function getWhereSql($params,$hasWhere=true)
    {
        if(isset($params['pageSize'])) unset($params['pageSize']);
        $where="";

        if($hasWhere)
            $where=" where 1=1";

        if(!is_array($params) || count($params)<1)
            return $where;

        $conditions=array();
        foreach ($params as $k=>$v)
        {
            $v=trim($v);
            //if($v != '')
            if(!empty($v) || $v===0 || $v==="0")
            {
                $v = addslashes($v);
                if (substr($k, strlen($k) - 1) == "*")
                {
                    $key = substr($k, 0, strlen($k) - 1);
                    $conditions[]=$key." like '%".$v."%'";
                }
                else
                    $conditions[]=$k."='".$v."'";
            }
        }
        if(count($conditions)<1)
            return $where;
        $where=implode(" and ",$conditions);
        if($hasWhere)
            $where=" where ".$where;

        return $where;
    }

    #region 新的列表数据获取及展示代码

    /**
     * 获取显示的数据
     * @param string $sql  基本格式：$sql="select {col} from table";
     * @param string $fields    默认为：*
     * @param int $totalRows    当=0时表示需要计算记录数，-1时表示不计算记录数，其他则为指定记录数
     * @param int $pageSize 每页记录数
     * @param int $dbType
     * @return PageData|null
     */
    public function getPageData($sql,$fields="*",$totalRows= 0,$pageSize=10, $dbType = 0):? PageData
    {
        $_pageSize=$this->getSearchPageSize();
        $pageSize=empty($_pageSize)?$pageSize:$_pageSize;
        $pageSize=empty($pageSize)?10:$pageSize;
        if($pageSize<0 || $pageSize>10000){
            $pageSize=20;
        }

        $currPage = $this->getSearchPage();
        $page=!empty($currPage) ? $currPage : 1;
        $page=$page<1?1:$page;
        $data = DbUtility::getTableData($sql, $fields, $page, $pageSize,$totalRows,$dbType);
        if(empty($data)){
            return $data;
        }

        $data->searchItems = $this->getSearch();

        return $data;
    }

    /**
     * @param $modelErrors
     * @return string
     */
    public function formatModelErrors(array $modelErrors = []){
        $msg = [];
        if(is_array($modelErrors)){
            foreach($modelErrors as $errors){
                foreach($errors as $error){
                    $msg[] = $error;
                }
            }
        }

        return implode(";",$msg);
    }

    #endregion

    /**
     * 判断客户端浏览器是否是移动设备
     *
     * @return bool
     */
    function isMobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA']))
        {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT']))
        {
            $clientkeywords = array ('nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT']))
        {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * [getExportButtonConfig 获取导出按钮]
     * @param
     * @param  [string] $exportAction [导出方法的函数名]
     * @return array
     */
    public function getExportButtonConfig($exportAction='export')
    {
        $url=$this->getId();
        return ['text'=>'导出', 'attr' => ['class_abbr' => 'action-default-base', 'id'=>'exportBtn', 'onclick' => 'page.export(\''.$url.'\',\''.$exportAction.'\')']];
    }

    /**
     * 获取首页面包屑配置
     * @return array
     */
    public function getIndexMenuConfig()
    {
        return ['text'=>'首页', 'link' => '/'];
    }


    //显示JSON格式的错误信息
    public function returnError($msg,$code=1)
    {
        header("Content-Type: application/json;");
        echo json_encode(array("state"=>$code,"data"=>$msg));
        Mod::app()->end();
    }

    /**
     * 返回操作成功的JSON数据
     * @param mixed $msg
     * @param null $extra 额外返回的数据
     */
    public function returnSuccess($msg="操作成功！",$extra=null)
    {
        $data=array("state"=>0,"data"=>$msg);
        if(!empty($extra))
            $data["extra"]=$extra;
        if(Mod::app()->request->isAjaxRequest)
            header("Content-Type: application/json;");
        echo json_encode($data, JSON_PRETTY_PRINT);
        Mod::app()->end();
    }

    /**
     * 结束当前请求，并返回操作成功的json格式
     * @param mixed $data
     */
    public function endRequestSuccess($data="操作成功！")
    {
        header("Content-Type: application/json;");
        echo json_encode(array("state"=>0,"data"=>$data));
        fastcgi_finish_request();
    }

    /**
     * 结束当前请求，并返回操作错误的json格式
     * @param mixed $data
     */
    public function endRequestError($data="")
    {
        header("Content-Type: application/json;");
        echo json_encode(array("state"=>1,"data"=>$data));
        fastcgi_finish_request();
    }





    /**
     * 导出Excel，自动分解数据到多Sheet页，每个sheet最多60000行数据，这是Excel本身的限制，每一个最大65000多。
     *  第三个参数为字符串行的列名
     *  第四个参数是文件名称
     *  第五个参数是文件后缀，默认是xls表格
     * @param array $data 需要导出的数据
     * @param string $title
     * @param [] $stringColumns key=>value,默认为null
     * @param string $fileName 导出的文件名
     * @param string $fileType  文件类型，默认：xls
     * @throws Exception
     */
    public function exportExcel(array $data,$title="",$stringColumns=null,$fileName="",$fileType="xls")
    {
        $objectPHPExcel = new PHPExcel();
        $objectPHPExcel->setActiveSheetIndex(0);

        if($fileName=="")
            $fileName=$title;

        if(is_array($data) && count($data)>0)
        {
            $rowCount=count($data);
            $row=1;
            //每一个sheet最大行数
            $maxRowCount=60000;

            $item=$data[0];
            $colCount=count($item);

            //Sheet页的最大索引
            $sheetIndex=0;

            $j=$rowCount;
            $j=$j-$maxRowCount;
            while($j>0)
            {
                $objectPHPExcel->createSheet();
                $sheetIndex++;
                $j=$j-$maxRowCount;
            }

            if(isset($title) && $title!="")
            {
                //设置每一个Sheet页的标题
                for ($i = 0; $i <= $sheetIndex; $i++) {
                    $objectPHPExcel->setActiveSheetIndex($i);
                    $activeSheet = $objectPHPExcel->getActiveSheet();
                    $activeSheet->mergeCellsByColumnAndRow(0,$row,$colCount-1,$row);
                    $activeSheet->setCellValueByColumnAndRow(0,$row,$title);
                    $activeSheet->getStyle('A'.$row)->getFont()->setSize(24);
                    $activeSheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                }
                $row=2;
            }

            for ($i = 0; $i <= $sheetIndex; $i++) {
                //设置表头
                $colIndex=0;
                $objectPHPExcel->setActiveSheetIndex($i);
                $activeSheet = $objectPHPExcel->getActiveSheet();
                foreach($item as $k=>$v)
                {
                    $activeSheet ->setCellValueByColumnAndRow($colIndex,$row,$k);
                    $activeSheet ->getStyleByColumnAndRow($colIndex,$row)->getFont()->setBold( true);
                    $colIndex++;
                }
            }

            $row++;
            //设置每一个Sheet的数据起始行
            $startRow=$row;
            $i=0;
            $j=0;
            foreach($data as $e) {
                $colIndex=0;
                foreach($e as $k=>$v)
                {
                    if(isset($stringColumns) || is_array($stringColumns))
                    {
                        if(in_array($k,$stringColumns))
                            $objectPHPExcel->setActiveSheetIndex($i)->setCellValueExplicitByColumnAndRow($colIndex,$row,$v,PHPExcel_Cell_DataType::TYPE_STRING);
                        else
                            $objectPHPExcel->setActiveSheetIndex($i)->setCellValueByColumnAndRow($colIndex,$row,$v);
                    }
                    else
                        $objectPHPExcel->setActiveSheetIndex($i)->setCellValueByColumnAndRow($colIndex,$row,$v);

                    $colIndex++;
                }
                $j++;
                if($j>=$maxRowCount)
                {
                    $j=0;
                    $row=$startRow;
                    $i++;
                }
                else
                    $row++;
            }

            $objectPHPExcel->setActiveSheetIndex(0);
        }
        else
        {
            //报表头的输出
            $objectPHPExcel->getActiveSheet()->mergeCells('A1:G1');
            $objectPHPExcel->getActiveSheet()->setCellValue('A1',$title);
            $objectPHPExcel->getActiveSheet()->mergeCells('A2:G2');
            $objectPHPExcel->getActiveSheet()->setCellValue('A2',"暂无数据");
        }


        ob_end_clean();
        ob_start();

        header('Content-Type : application/vnd.ms-excel');
        header('Content-Disposition:attachment;filename="'.$fileName.date("Y年m月j日").'.'.$fileType.'"');
        $objWriter= PHPExcel_IOFactory::createWriter($objectPHPExcel,'Excel5');
        $objWriter->save('php://output');
    }

    public function renderWeb()
    {
        $url        = Mod::app()->params["oil_web_url"];
        $content    = file_get_contents($url);
        $this->layout = "empty";
        $this->render('/layouts/web', array('content'=>$content));
    }

    /**
     * 获取REST规范提交的数据
     * @return mixed
     */
    public function getRestParams(){
        return Mod::app()->request->getRestParams();
    }

    /**
     * 获取REST规范提交的数据
     * @param $name
     * @param null $defaultValue
     * @return mixed|null
     */
    public function getRestParam($name, $defaultValue=null){
        $data = $this->getRestParams();

        return is_array($data) && isset($data[$name]) ? $data[$name] : $defaultValue;
    }

}

