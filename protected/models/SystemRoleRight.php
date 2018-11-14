<?php

use app\ddd\Admin\Domain\Module\ModuleAction;

/**
 * This is the model class for table "{{system_role_right}}".
 * The followings are the available columns in table '{{system_role_right}}':
 * @property integer $role_id
 * @property string  $right_codes
 * @property string  $remark
 * @property integer $create_user_id
 * @property string  $create_time
 * @property integer $update_user_id
 * @property string  $update_time
 */
class SystemRoleRight extends BaseActiveRecord{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SystemRoleRight the static model class
     */
    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName(){
        return '{{system_role_right}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules(){
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'create_user_id, update_user_id',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'remark',
                'length',
                'max' => 256
            ),
            array(
                'right_codes, create_time, update_time',
                'safe'
            ),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'role_id, right_codes, remark, create_user_id, create_time, update_user_id, update_time',
                'safe',
                'on' => 'search'
            ),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations(){
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            "create_user" => [
                self::BELONGS_TO,
                "SystemUser",
                ['create_user_id' => 'user_id']
            ],
            // 创建用户
            "update_user" => [
                self::BELONGS_TO,
                "SystemUser",
                ['update_user_id' => 'user_id']
            ],
            // 更新用户
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
        return array(
            'role_id'        => 'id',
            'right_codes'    => '权限码',
            'remark'         => '备注',
            'create_user_id' => '创建用户',
            'create_time'    => '创建时间',
            'update_user_id' => '更新用户',
            'update_time'    => '更新时间',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search(){
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('role_id', $this->role_id);
        $criteria->compare('right_codes', $this->right_codes, true);
        $criteria->compare('remark', $this->remark, true);
        $criteria->compare('create_user_id', $this->create_user_id);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('update_user_id', $this->update_user_id);
        $criteria->compare('update_time', $this->update_time, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
    /**
     * 获取Action数组
     * @return Action[]
     */
    public function getRightCodes() {
        $modules = [];
        $actionsArray = [];
        if (!empty($this->right_codes))
            $actionsArray = json_decode($this->right_codes, true);
        foreach ($actionsArray as $item) {
            $module = new ModuleAction();
            $module->id=$item['id'];
            $module->name=$item['name'];
            $module->code=$item['code'];
            $module->parent_id=$item['parent_id'];
            $module->addActions($item['actions']);
            $modules[] = $module;
        }
        return $modules;
    }

    /**
     * @param array $actions
     */
    public function setRightCodes($actions) {
        $this->right_codes = json_encode($actions);
        /*if(is_array($actions))
            $this->actions=json_encode($actions);
        else
            $this->actions=$actions;*/
        /*if(is_array($actions))
        {
            foreach ($actions as $action)
            {
                if(is_a($action,\app\ddd\Admin\Domain\Module\Action::class))

            }
        }*/
    }

}
