<?php


namespace ddd\OilStation\Repository;


use app\ddd\Common\Domain\Value\Operator;
use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\DateTime;
use ddd\Infrastructure\error\ZModelNotExistsException;
use ddd\Infrastructure\error\ZModelSaveFalseException;
use ddd\OilStation\Domain\OilGoods\OilGoodsEnum;
use ddd\OilStation\Domain\OilPrice\IOilPriceRepository;
use ddd\OilStation\Domain\OilPrice\OilPrice;
use ddd\OilStation\Domain\Value\Company;
use ddd\OilStation\Domain\Value\Goods;
use ddd\OilStation\Domain\Value\Station;

class OilPriceRepository extends OilRepository implements IOilPriceRepository{

    public function init(){
        $this->with = ['company','station','goods','createUser','updateUser'];
    }

    /**
     * 获取新的实体对象
     * @return BaseEntity|OilPrice
     */
    public function getNewEntity(){
        return new OilPrice();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName(){
        return \OilPrice::class;
    }

    public function dataToEntity($model){
        return $this->toEntity($model);
    }

    public function toEntity(\OilPrice $model){
        $entity = new OilPrice();
        $entity->setId($model->price_id);
        $entity->setApplyId($model->apply_id);
        $entity->setCompany(new Company($model->company_id,$model->company->name));
        $entity->setStation(new Station($model->station_id,$model->station->name));
        $entity->setGoods(new Goods($model->goods_id,$model->goods->name));
        $entity->setRetailPrice($model->retail_price);
        $entity->setAgreedPrice($model->agreed_price);
        $entity->setDiscountPrice($model->discount_price);
        $entity->setEffectTime(new DateTime($model->effect_time));
        $entity->setEndTime(new DateTime($model->end_time));
        $entity->setRemark($model->remark);
        $entity->setCreateTime(new DateTime($model->create_time));
        $entity->setCreateUser(new Operator($model->create_user_id,$model->createUser->name));
        $entity->setUpdateUser(new Operator($model->update_user_id,$model->updateUser->name));
        $entity->setUpdateTime(new DateTime($model->update_time));
        $entity->setStatus(new Status($model->status,$model->status_time, \Map::getStatusName('oil_price_status',$model->status)));

        return $entity;
    }

    /**
     * @param $entity
     * @return OilPrice
     * @throws \Exception
     */
    public function store($entity){
        return $this->save($entity);
    }

    /**
     * @param OilPrice $entity
     * @return OilPrice
     * @throws ZModelSaveFalseException
     * @throws \CDbException
     */
    public function save(OilPrice $entity):OilPrice {
        $model = new \OilPrice();
        $model->apply_id = $entity->getApplyId();
        $model->item_id = $entity->getItemId();
        $model->company_id = $entity->getCompanyId();
        $model->station_id = $entity->getStationId();
        $model->goods_id = $entity->getGoodsId();
        $model->retail_price = $entity->getRetailPrice();
        $model->agreed_price = $entity->getAgreedPrice();
        $model->discount_price = $entity->getDiscountPrice();
        $model->effect_time = $entity->getEffectTime()->toDateTime();
        $model->end_time = $entity->getEndTime()->toDateTime();
        $model->remark = $entity->getRemark();
        $model->status = $entity->getStatusValue();
        $model->status_time = $entity->getStatusTime();

        if(!$model->save()){
            throw new ZModelSaveFalseException($model);
        }
        $entity->setId($model->getPrimaryKey());

        return $entity;
    }

    /**
     * @param $id
     * @return OilPrice|null
     * @throws \Exception
     */
    public function findById($id):?OilPrice{
        return parent::findById($id);
    }

    public function findAllByStationId(int $stationId):array{
        return $this->findAll('t.station_id = :station_id',[':station_id'=> $stationId]);
    }

    public function findAllByCompanyId(int $companyId):array{
        return $this->findAll('t.company_id = :company_id',[':company_id'=> $companyId]);
    }

    function findAllByStationIdAndGoodsId(int $stationId, int $goodsId): array {
        return $this->findAll('t.station_id = :station_id AND t.goods_id=:goods_id',[':station_id'=> $stationId,':goods_id'=>$goodsId]);
    }

    /**
     * 根据油站和商品查找生效的商品价格
     * @param int $stationId
     * @param int $goodsId
     * @return BaseEntity|OilPrice|null
     * @throws \Exception
     */
    public function findActivePriceByStationIdAndGoodsId(int $stationId, int $goodsId){
        return $this->find([
                                "condition"=>"t.station_id=".$stationId." and t.goods_id=".$goodsId." and goods.status=" . OilGoodsEnum::ENABLE . " and station.status=" . \OilStation::STATUS_ENABLE . " 
                                        and t.effect_time<=now() and t.end_time>=now()",
                               "order"=>"price_id desc"
                           ]);
    }

    /**
     * 根据油站和商品查找第二天要生效的商品价格
     * @param int $stationId
     * @param int $goodsId
     * @return BaseEntity|OilPrice|null
     * @throws \Exception
     */
    public function findPrepareEffectPriceByStationIdAndGoodsId(int $stationId, int $goodsId){
        return $this->find([
            "condition"=>"t.station_id=".$stationId." and t.goods_id=".$goodsId." and goods.status=" . OilGoodsEnum::ENABLE . " and station.status=" . \OilStation::STATUS_ENABLE . " 
                                        and t.effect_time <= (date_sub(now(),interval -1 day) ) and t.end_time>=now()",
            "order"=>"price_id desc"
        ]);
    }

    /**
     * @param OilPrice $oilPrice
     * @throws \Exception
     */
    public function saveEndTime(OilPrice $oilPrice){
        $model = \OilPrice::model()->findByPk($oilPrice->getId());
        if (empty($model))
        {
            throw new ZModelNotExistsException($oilPrice->getId(), \OilPrice::class);
        }
        $model->end_time=$oilPrice->getEndTime()->toDateTime();
        $model->update_time=new \CDbExpression("now");
        $model->update_user_id=\Utility::getNowUserId();
        $res= $model->update(array("end_time", "update_time","update_user_id"));
        if(!$res)
            throw new ZModelSaveFalseException($model);
    }


    /**
     * 根据油站查找生效的商品价格
     * @param int $stationId
     * @return array|OilPrice
     * @throws \CException
     */
    public function findAllActivePriceByStationId(int $stationId) {
        return $this->findAll([
            "condition"=>"t.station_id=".$stationId." and t.effect_time<=now() and t.end_time>=now() and goods.status=" . OilGoodsEnum::ENABLE . " and station.status=" . \OilStation::STATUS_ENABLE,
            "group"=>"t.goods_id"
        ]);
    }
}