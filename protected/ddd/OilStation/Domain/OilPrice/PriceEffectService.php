<?php
/**
 * Created by youyi000.
 * DateTime: 2018/9/14 16:14
 * Describe：
 */

namespace app\ddd\OilStation\Domain\OilPrice;


use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Domain\BaseService;
use ddd\Common\Domain\Value\DateTime;
use ddd\OilStation\Domain\OilPrice\OilPrice;
use ddd\OilStation\Domain\OilPrice\OilPriceApply;
use ddd\OilStation\Domain\OilPrice\OilPriceEnum;
use ddd\OilStation\Domain\OilPrice\OilPriceItem;
use ddd\OilStation\Domain\OilPrice\TraitOilPriceRepository;
use ddd\OilStation\Domain\Value\Company;
use ddd\OilStation\Domain\Value\Goods;
use ddd\OilStation\Domain\Value\Station;

class PriceEffectService extends BaseService{

    use TraitOilPriceRepository;

    /**
     * 价格申请生效
     * @param OilPriceApply $entity
     * @throws \Exception
     */
    public function effect(OilPriceApply $entity):void {
        $items = $entity->getItems();
        if(\CheckUtility::isEmpty($items)){
            return;
        }

        foreach($items as $item){
            $this->toEffect($entity->getId(),$item);
        }
    }

    /**
     * @param int          $applyId
     * @param OilPriceItem $entity
     * @throws \Exception
     */
    protected function toEffect(int $applyId,OilPriceItem $entity):void {
        $price = $this->getOilPriceRepository()->findActivePriceByStationIdAndGoodsId($entity->getStationId(), $entity->getGoodsId());
        if(!empty($price)){
            //设置失效日期
            $price->setInvalidDate();
        }

        $lastPrice = $this->getOilPriceRepository()->findPrepareEffectPriceByStationIdAndGoodsId($entity->getStationId(), $entity->getGoodsId());
        if(!empty($lastPrice)){
            //设置失效日期
            $lastPrice->setInvalidDate();
        }

        $newPrice = new OilPrice();
        //完成赋值
        $newPrice->setApplyId($applyId);
        $newPrice->setItemId($entity->getItemId());
        $newPrice->setAgreedPrice($entity->getAgreedPrice());
        $newPrice->setDiscountPrice($entity->getDiscountPrice());
        $newPrice->setRetailPrice($entity->getRetailPrice());
        $newPrice->setCompany(new Company($entity->getCompanyId()));
        $newPrice->setStation(new Station($entity->getStationId()));
        $newPrice->setGoods(new Goods($entity->getGoodsId()));
        $newPrice->setStatus(new Status(OilPriceEnum::STATUS_ACTIVE));
        $newPrice->setRemark($entity->getRemark());

        //设置新的生效时间
        $newPrice->setEffectTime(new DateTime(\Utility::getDate("+1 days")." 00:00:00"));
        $newPrice->setEndTime(new DateTime("9999-12-31 23:59:59"));
        $newPrice->save();
    }

}