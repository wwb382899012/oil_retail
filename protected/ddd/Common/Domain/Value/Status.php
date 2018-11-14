<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/5 0005
 * Time: 11:17
 */

namespace app\ddd\Common\Domain\Value;


use ddd\Common\Domain\BaseValue;
use ddd\Common\Domain\IValue;
use ddd\Infrastructure\Utility;

class Status extends BaseValue{

    /**
     * @var int
     */
    public $status = 0;

    public $name = '';

    public $status_time = '';


    public function equals(IValue $value){
        return $this->status == $value->status;
    }

    public function __construct($status = 0, $time = '', $name = ''){
        $this->status = $status;
        $this->status_time = empty($time) ? Utility::getDateTime() : $time;
        $this->name = $name;
        parent::__construct();
    }
}