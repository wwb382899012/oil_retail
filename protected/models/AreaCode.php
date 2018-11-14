<?php

/**
 * This is the model class for table "{{area_code}}".
 * The followings are the available columns in table '{{area_code}}':
 * @property integer $area_code
 * @property string  $area_name
 * @property integer $p_area_code
 * @property integer $level
 */
class AreaCode extends CActiveRecord{
    /**
     * @return string the associated database table name
     */
    public function tableName(){
        return '{{area_code}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules(){
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'area_code, p_area_code, level',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'area_name',
                'length',
                'max' => 50
            ),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'area_code, area_name, p_area_code, level',
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
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
        return array(
            'area_code' => 'Area Code',
            'area_name' => 'Area Name',
            'p_area_code' => 'P Area Code',
            'level' => 'Level',
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

        $criteria->compare('area_code', $this->area_code);
        $criteria->compare('area_name', $this->area_name, true);
        $criteria->compare('p_area_code', $this->p_area_code);
        $criteria->compare('level', $this->level);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AreaCode the static model class
     */
    public static function model($className = __CLASS__){
        return parent::model($className);
    }
}
