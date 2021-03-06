<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 16:20
 * Describe：
 */

namespace ddd\Common;

use ddd\Common\Domain\BaseEvent;
use ddd\Infrastructure\EventSubscribeService;

class BaseModel extends \CModel
{
    private static $_names=array();
    private static $_publicPropertyNames=array();

    //private $_attributes=array();				// attribute name => attribute value
    private $_attributeNames;

    /**
     * 事件
     * @var array
     */
    protected $_events=[];

    /**
     * BaseModel constructor.
     * @param null|array $params 初始化属性值，null或array(attribute name => attribute value)
     *  如果子类重写构造函数，保留最后一个参数为可变参数$params
     */
    public function __construct($params=null)
    {
        $scenario="";
        if(!empty($params["scenario"]))
            $scenario=$params["scenario"];
        $this->setScenario($scenario);
        $this->init();
        $this->initConfigs($params);
        $this->attachBehaviors($this->behaviors());
        $this->initEvents();
        $this->afterConstruct();
    }

    /**
     * 根据构造函数传入初始化参数
     * @param $params
     */
    protected function initConfigs($params)
    {
        if(is_array($params) && count($params)>0)
        {
            foreach ($params as $name=>$value)
            {
                if(property_exists($this,$name) && $value!==null)
                {
                    $this->$name=$value;
                }
            }
        }
    }

    /**
     * Initializes this model.
     * This method is invoked in the constructor right after {@link scenario} is set.
     * You may override this method to provide code that is needed to initialize the model (e.g. setting
     * initial property values.)
     */
    public function init()
    {

    }


    #region Events

    /**
     * 事件配置，事件名必须以on开头，否则无效
     * @return array
     */
    protected function events()
    {
        return [];
    }

    /**
     * 初始化事件
     */
    protected function initEvents()
    {
        $events=$this->events();
        if(is_array($events))
        {
            foreach ($events as $e)
            {
                $e=strtolower($e);
                if(strncasecmp($e,'on',2)===0)
                    $this->_events[$e]=$e;
            }
        }
    }

    /**
     * 是否有事件定义
     * @param string $name
     * @return bool
     */
    public function hasEvent($name)
    {
        $name=strtolower($name);
        return !strncasecmp($name,'on',2) && (in_array($name,$this->_events) || method_exists($this,$name));
    }

    /**
     * 发布事件
     * @param $eventName
     * @param \ddd\Common\Domain\BaseEvent $event
     * @throws \Exception
     */
    public function publishEvent($eventName,BaseEvent $event)
    {
        $eventKey=get_class($event);
        EventSubscribeService::bind($this,$eventName,$eventKey);
        if($this->hasEventHandler($eventName))
            $this->raiseEvent($eventName, $event);

    }

    #endregion

    #region 属性值相关

    /**
     * 自定义属性值列表，子类可以继承，主要是一些非public但有get方法的属性
     * @return array
     */
    public function customAttributeNames()
    {
        return [];
    }

    /**
     * Returns the list of attribute names.
     * By default, this method returns all public properties of the class.
     * You may override this method to change the default.
     * @return array list of attribute names. Defaults to all public properties of the class.
     * @throws \Exception
     */
    public function attributeNames()
    {
        if(!empty($this->_attributeNames))
            return $this->_attributeNames;
        $className = get_class($this);
        if (!isset(self::$_names[$className]))
        {
            $names = $this->customAttributeNames();
            $propertyNames=$this->getPublicPropertyNames();
            $names=array_merge($names,$propertyNames);
            $names = array_unique($names);

            //$names=$this->getPublicPropertyNames();
            self::$_names[$className] = $names;
        }
        return $this->_attributeNames = self::$_names[$className];
    }

    /**
     *  获取public属性名数组
     * @return array
     * @throws \Exception
     */
    public function getPublicPropertyNames()
    {
        $className = get_class($this);
        if (!isset(self::$_publicPropertyNames[$className]))
        {
            $class = new \ReflectionClass($className);
            $names = array();
            foreach ($class->getProperties() as $property)
            {
                $name = $property->getName();
                if ($property->isPublic() && !$property->isStatic())
                    $names[] = $name;
            }
            self::$_publicPropertyNames[$className]= $names;
        }
        return self::$_publicPropertyNames[$className];
    }

    /**
     * 获取属性值数组
     * @param null $names
     * @return array
     * @throws \Exception
     */
    public function getAttributes($names = null)
    {
        $values=array();
        $publicProperties=$this->getPublicPropertyNames();
        foreach($this->attributeNames() as $name)
        {
            if(!in_array($name,$publicProperties))
            {

                $getter='get'.$name;
                if(method_exists($this,$getter))
                    $value= $this->$getter();
            }
            else
                $value=$this->$name;

            if(is_array($value))
            {
                $items=[];
                foreach ($value as $k=>$obj)
                {
                    if(is_a($obj,\CModel::class))
                        $items[$k]=$obj->getAttributes();
                    else
                        $items[$k]=$obj;
                }
                $values[$name]=$items;
            }
            elseif(is_a($value,\CModel::class))
                $values[$name]=$value->getAttributes();
            else
                $values[$name]=$value;
        }


        if(is_array($names))
        {
            $values2=array();
            foreach($names as $name)
                $values2[$name]=isset($values[$name]) ? $values[$name] : null;
            return $values2;
        }
        else
            return $values;
    }

    /**
     * Sets the attribute values in a massive way.
     * @param array $values attribute values (name=>value) to be set.
     * @param boolean $safeOnly whether the assignments should only be done to the safe attributes.
     * A safe attribute is one that is associated with a validation rule in the current {@link scenario}.
     * @see getSafeAttributeNames
     * @see attributeNames
     * @throws \Exception
     */
    public function setAttributes($values, $safeOnly = false)
    {
        if(!is_array($values))
            return;
        $publicProperties=$this->getPublicPropertyNames();
        $attributes=array_flip($safeOnly ? $this->getSafeAttributeNames() : $this->attributeNames());
        foreach($values as $name=>$value)
        {
            if(isset($attributes[$name]))
            {
                if(!in_array($name,$publicProperties))
                {
                    $setter='set'.$name;
                    if(method_exists($this,$setter))
                        $this->$setter($value);
                }
                else
                    $this->$name=$value;
            }
            elseif($safeOnly)
                $this->onUnsafeAttribute($name,$value);
        }
        //return parent::setAttributes($values, $safeOnly); // TODO: Change the autogenerated stub
    }

    /**
     * PHP getter magic method.
     * This method is overridden so that AR attributes can be accessed like properties.
     * @param string $name property name
     * @return mixed property value
     * @see getAttribute
     */
    public function __get($name)
    {
        /*if(isset($this->_attributes[$name]))
            return $this->_attributes[$name];
        else*/
            return parent::__get($name);
    }

    /**
     * PHP setter magic method.
     * This method is overridden so that AR attributes can be accessed like properties.
     * @param string $name property name
     * @param mixed $value property value
     * @throws \Exception
     */
    public function __set($name,$value)
    {
        if($this->setAttribute($name,$value)===false)
        {
            if(strncasecmp($name,'on',2)===0 && $this->hasEvent($name))
            {
                $this->attachEventHandler($name,$value);
            }
            else
                parent::__set($name,$value);
        }
    }

    /**
     * Sets the named attribute value.
     * You may also use ddd\$this->AttributeName to set the attribute value.
     * @param string $name the attribute name
     * @param mixed $value the attribute value.
     * @return boolean whether the attribute exists and the assignment is conducted successfully
     * @see hasAttribute
     */
    public function setAttribute($name,$value)
    {
        if(property_exists($this,$name))
            $this->$name=$value;
        else
            return false;
        return true;
    }

    /**
     * Checks if a property value is null.
     * This method overrides the parent implementation by checking
     * if the named attribute is null or not.
     * @param string $name the property name or the event name
     * @return boolean whether the property value is null
     */
    public function __isset($name)
    {
        /*if(isset($this->_attributes[$name]))
            return true;
        else*/
            return parent::__isset($name);
    }

    /**
     * Sets a component property to be null.
     * This method overrides the parent implementation by clearing
     * the specified attribute value.
     * @param string $name the property name or the event name
     * @throws \Exception
     */
    public function __unset($name)
    {
        /*if(isset($this->_attributes[$name]))
            unset($this->_attributes[$name]);
        else*/
            parent::__unset($name);
    }

    #endregion

}