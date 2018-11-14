<?php

namespace ddd\OilStation\DTO\OilPrice;


use ddd\Common\Application\BaseDTO;
use ddd\OilStation\Domain\OilPrice\OilPriceItem;

class OilPriceItemDTO extends BaseDTO{
    #region property

    public $item_id = 0;

    public $company_id = 0;

    public $station_id = 0;

    public $goods_id = 0;

    public $retail_price = 0;

    public $agreed_price = 0;

    public $discount_price = 0;

    public $remark;

    #endregion

    public function attributeLabels(){
        return [
            "discount_price" => "优惠价",
            "agreed_price"   => "协议价",
            "retail_price"   => "零售价",
        ];
    }

    public function rules(){
        return [
            ["discount_price", "required", "message" => "优惠价缺失！"],
            ["agreed_price", "required", "message" => "协议价缺失！"],
            ["retail_price", "required", "message" => "零售价缺失！"],
            ["company_id", "required", "message" => "优惠价缺失！"],
            ["station_id", "required", "message" => "优惠价缺失！"],
            ["goods_id", "required", "message" => "优惠价缺失！"],
            ["company_id", "numerical", "integerOnly" => true, "min" => 1, "tooSmall" => "信息异常，缺少必要参数企业id！"],
            ["station_id", "numerical", "integerOnly" => true, "min" => 1, "tooSmall" => "信息异常，缺少必要参数油站id！"],
            ["goods_id", "numerical", "integerOnly" => true, "min" => 1, "tooSmall" => "信息异常，缺少必要参数商品id！"],
            ["discount_price", "numerical", "min" => 0, "tooSmall" => "优惠价必须大于等于0！"],
            ["agreed_price", "numerical", "min" => 0, "tooSmall" => "协议价必须大于等于0！"],
            ["retail_price", "numerical",  "min" => 0, "tooSmall" => "零售价必须大于等于0！"],
            ["agreed_price","validatePrice","compared" => "discount_price"],
            ["discount_price","validatePrice","compared" => "retail_price"],
        ];
    }

    public function fromEntity(OilPriceItem $entity){
        $this->item_id = $entity->getItemId();
        $this->company_id = $entity->getCompanyId();
        $this->station_id = $entity->getStationId();
        $this->goods_id = $entity->getGoodsId();
        $this->retail_price = \MathUtility::div($entity->getRetailPrice(),100);
        $this->agreed_price = \MathUtility::div($entity->getAgreedPrice(),100);
        $this->discount_price = \MathUtility::div($entity->getDiscountPrice(),100);
        $this->remark = $entity->getRemark();
    }

    public function toEntity():OilPriceItem{
        $entity = new OilPriceItem();
        $entity->setItemId($this->item_id);
        $entity->setCompanyId($this->company_id);
        $entity->setStationId($this->station_id);
        $entity->setGoodsId($this->goods_id);
        $entity->setRetailPrice(\MathUtility::mul($this->retail_price,100));
        $entity->setAgreedPrice(\MathUtility::mul($this->agreed_price,100));
        $entity->setDiscountPrice(\MathUtility::mul($this->discount_price,100));
        $entity->setRemark($this->remark);

        return $entity;
    }

    /**
     * 价格校验
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function validatePrice($attribute, $params):bool {
        if(empty($params["compared"])){
            return true;
        }

        $compared = $params["compared"];

        if(\MathUtility::less($this->$attribute,0)){
            $this->addError($attribute, $this->getAttributeLabel($attribute)."不能小于0!");
            return false;
        }
        if(\MathUtility::less($this->$compared,0)){
            $this->addError($attribute, $this->getAttributeLabel($compared)."不能小于0!");
            return false;
        }

        if(\MathUtility::greater($this->$attribute,$this->$compared)){
            $this->addError($attribute, $this->getAttributeLabel($attribute)."不能大于".$this->getAttributeLabel($compared));
            return false;
        }

        return true;
    }
}