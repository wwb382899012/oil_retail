<?php

namespace ddd\OilStation\Domain\OilStation;


use ddd\Common\BaseEnum;

class OilStationApplyEnum extends BaseEnum{
   const STATUS_SAVE = 0;//已保存
   const STATUS_BACK = 3; //已驳回
   const STATUS_SUBMIT = 5;//已提交
   const STATUS_PASSED = 7; //已通过
}