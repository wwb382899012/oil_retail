<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/24 11:59
 * Describe：
 *      包含创建和修改用户及日期的通用业务类的基类
 */

class BaseBusinessActiveRecord extends BaseActiveRecord
{

    public function beforeSave()
    {
        if ($this->isNewRecord)
        {
            if (empty($this->create_time))
                $this->create_time = new CDbExpression("now()");
            if (empty($this->create_user_id))
                $this->create_user_id= Utility::getNowUserId();
        }
        if ($this->update_time == $this->getOldAttribute("update_time"))
        {
            $this->update_time = new CDbExpression("now()");
            $this->update_user_id = Utility::getNowUserId();
        }
        return parent::beforeSave(); // TODO: Change the autogenerated stub
    }

}