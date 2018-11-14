<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/4 0004
 * Time: 14:32
 */

namespace app\ddd\Common\Domain\Value;


use ddd\Common\Domain\BaseValue;

class Attachment extends BaseValue
{
    #region property

    /**
     * 标识或id
     * @var   string
     */
    public $id;

    /**
     * 文件地址
     * @var   string
     */
    public $url;

    #endregion

    public function __construct($id = "", $url = "", array $params = null)
    {
        parent::__construct($params);
        if (!empty($id))
        {
            $this->id = $id;
        }
        if (!empty($url))
        {
            $this->url = $url;
        }
    }
}