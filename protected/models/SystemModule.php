<?php

/**
 * This is the model class for table "{{system_module}}".
 * The followings are the available columns in table '{{system_module}}':
 * @property integer     $id
 * @property string      $name
 * @property string      $code
 * @property string      $icon
 * @property integer     $system_id
 * @property integer     $parent_id
 * @property string      $parent_ids
 * @property string      $page_url
 * @property string      $actions
 * @property integer     $order_index
 * @property integer     $is_public
 * @property integer     $is_external
 * @property integer     $is_menu
 * @property integer     $status
 * @property string      $remark
 * @property integer     $create_user_id
 * @property string      $create_time
 * @property integer     $update_user_id
 * @property string      $update_time
 * @property  SystemUser create_user 创建用户
 * @property  SystemUser update_user 修改用户
 */
class SystemModule extends BaseActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SystemModule the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{system_module}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'system_id, parent_id, order_index, is_public, is_external, is_menu, status, create_user_id, update_user_id',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'name',
                'length',
                'max' => 64
            ),
            array(
                'code, icon, parent_ids, page_url',
                'length',
                'max' => 256
            ),
            array(
                'actions, remark',
                'length',
                'max' => 512
            ),
            array(
                'create_time, update_time',
                'safe'
            ),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'id, name, code, icon, system_id, parent_id, parent_ids, page_url, actions, order_index, is_public, is_external, is_menu, status, remark, create_user_id, create_time, update_user_id, update_time',
                'safe',
                'on' => 'search'
            ),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
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
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => '模块名称',
            'code' => 'Code',
            'icon' => 'Icon',
            'system_id' => 'System',
            'parent_id' => 'Parent',
            'parent_ids' => 'Parent Ids',
            'page_url' => 'Page Url',
            'actions' => 'Actions',
            'order_index' => 'Order Index',
            'is_public' => 'Is Public',
            'is_external' => 'Is External',
            'is_menu' => 'Is Menu',
            'status' => 'Status',
            'remark' => 'Remark',
            'create_user_id' => 'Create User',
            'create_time' => 'Create Time',
            'update_user_id' => 'Update User',
            'update_time' => 'Update Time',
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
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('code', $this->code, true);
        $criteria->compare('icon', $this->icon, true);
        $criteria->compare('system_id', $this->system_id);
        $criteria->compare('parent_id', $this->parent_id);
        $criteria->compare('parent_ids', $this->parent_ids, true);
        $criteria->compare('page_url', $this->page_url, true);
        $criteria->compare('actions', $this->actions, true);
        $criteria->compare('order_index', $this->order_index);
        $criteria->compare('is_public', $this->is_public);
        $criteria->compare('is_external', $this->is_external);
        $criteria->compare('is_menu', $this->is_menu);
        $criteria->compare('status', $this->status);
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
     * @return \app\ddd\Admin\Domain\Module\Action[]
     */
    public function getActions()
    {
        $actions = [];
        if (!empty($this->actions))
        {
            $actionsArray = [];
            $actionsArray = json_decode($this->actions, true);
            if(is_array($actionsArray))
            {
                foreach ($actionsArray as $item)
                {
                    $actions[] = new \app\ddd\Admin\Domain\Module\Action($item["name"], $item["code"]);
                }
            }
        }

        return $actions;
    }

    /**
     * @param array $actions
     */
    public function setActions($actions)
    {
        $this->actions = json_encode($actions);
    }

    protected function beforeSave()
    {
        $res= parent::beforeSave(); // TODO: Change the autogenerated stub
        if($res)
            $this->setParentIds();

        if(!$this->isNewRecord)
            $this->updateParentIds($this->getOldAttribute("parent_ids"),$this->parent_ids);

        return $res;
    }
    

    /**
     * 更新父路径
     * @param $oldParentIds
     * @param $parentIds
     */
    protected function updateParentIds($oldParentIds,$parentIds)
    {
        if($oldParentIds!=$parentIds)
        {
            $this->updateAll(["parent_ids"=>new CDbExpression("replace(parent_ids,'" . $oldParentIds . "', '" .$parentIds. "')")],
                             "parent_ids like '%,".$this->id.",%'");
        }
    }

    /**
     * 设置父路径
     */
    protected function setParentIds()
    {
        if($this->parent_id>0)
        {
            $parentModule=\SystemModule::model()->findByPk($this->parent_id);
            if(!empty($parentModule))
                $this->parent_ids=$parentModule->parent_ids.$parentModule->id.",";
        }
        else
            $this->parent_ids="0,";
    }

}
