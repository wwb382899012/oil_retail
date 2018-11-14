<?php

/**
 * This is the model class for table "t_system_user".
 * The followings are the available columns in table 't_system_user':
 * @property integer      $user_id
 * @property string       $user_name
 * @property string       $password
 * @property string       $role_ids
 * @property string       $right_codes
 * @property string       $login_key
 * @property integer      $login_count
 * @property string       $login_time
 * @property string       $identity
 * @property integer      $main_role_id
 * @property string       $weixin
 * @property string       $phone
 * @property string       $email
 * @property integer      $status
 * @property integer      $is_right_role
 * @property string       $corp_ids
 * @property string       $name
 * @property string       $remark
 * @property integer      $create_user_id
 * @property string       $create_time
 * @property integer      $update_user_id
 * @property string       $update_time
 * @property SystemRole[] $roles
 */
class SystemUser extends CActiveRecord{
    const LoginSecretKey = '030da3d79hf3b03fc6cf14c0b1bd309d5';

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SystemUser the static model class
     */
    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName(){
        return 't_system_user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules(){
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'login_count, main_role_id, status, is_right_role, create_user_id, update_user_id',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'user_name, password, weixin',
                'length',
                'max' => 64
            ),
            array(
                'role_ids, corp_ids',
                'length',
                'max' => 1024
            ),
            array(
                'login_key, identity, email, remark',
                'length',
                'max' => 256
            ),
            array(
                'phone, name',
                'length',
                'max' => 32
            ),
            array(
                'right_codes, login_time, create_time, update_time',
                'safe'
            ),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'user_id, user_name, password, role_ids, right_codes, login_key, login_count, login_time, identity, main_role_id, weixin, phone, email, status, is_right_role, corp_ids, name, remark, create_user_id, create_time, update_user_id, update_time',
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
            "roles" => [self::MANY_MANY,SystemRole::class, "t_user_role_relation(user_id, role_id)"],
            "mainRole" => [self::BELONGS_TO,SystemRole::class,"main_role_id"],
            'rightCode'=> [self::BELONGS_TO,SystemUserRight::class,'user_id'],
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
        return array(
            'user_id'        => 'User',
            'user_name'      => 'User Name',
            'password'       => 'Password',
            'role_ids'       => 'Role Ids',
            'right_codes'    => 'Right Codes',
            'login_key'      => 'Login Key',
            'login_count'    => 'Login Count',
            'login_time'     => 'Login Time',
            'identity'       => 'Identity',
            'main_role_id'   => 'Main Role',
            'weixin'         => 'Weixin',
            'phone'          => 'Phone',
            'email'          => 'Email',
            'status'         => 'Status',
            'is_right_role'  => 'Is Right Role',
            'corp_ids'       => 'Corp Ids',
            'name'           => 'Name',
            'remark'         => 'Remark',
            'create_user_id' => 'Create User',
            'create_time'    => 'Create Time',
            'update_user_id' => 'Update User',
            'update_time'    => 'Update Time',
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

        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('user_name', $this->user_name, true);
        $criteria->compare('password', $this->password, true);
        $criteria->compare('role_ids', $this->role_ids, true);
        $criteria->compare('right_codes', $this->right_codes, true);
        $criteria->compare('login_key', $this->login_key, true);
        $criteria->compare('login_count', $this->login_count);
        $criteria->compare('login_time', $this->login_time, true);
        $criteria->compare('identity', $this->identity, true);
        $criteria->compare('main_role_id', $this->main_role_id);
        $criteria->compare('weixin', $this->weixin, true);
        $criteria->compare('phone', $this->phone, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('is_right_role', $this->is_right_role);
        $criteria->compare('corp_ids', $this->corp_ids, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('remark', $this->remark, true);
        $criteria->compare('create_user_id', $this->create_user_id);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('update_user_id', $this->update_user_id);
        $criteria->compare('update_time', $this->update_time, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}
