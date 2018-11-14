<?php

namespace ddd\OilStation\Application;

use ddd\Common\Application\TransactionService;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ZEntityNotExistsException;
use ddd\Infrastructure\error\ZException;
use ddd\Infrastructure\IDService;
use ddd\OilStation\Domain\Attachment;
use ddd\OilStation\Domain\OilCompany\TraitOilCompanyRepository;
use ddd\OilStation\Domain\OilGoods\TraitOilGoodsRepository;
use ddd\OilStation\Domain\OilPrice\OilPriceApply;
use ddd\OilStation\Domain\OilPrice\TraitOilPriceApplyAttachmentRepository;
use ddd\OilStation\Domain\OilPrice\TraitOilPriceApplyRepository;
use ddd\OilStation\Domain\OilStation\TraitOilStationRepository;
use ddd\OilStation\DTO\AttachmentDTO;
use ddd\OilStation\DTO\OilPrice\OilPriceApplyDTO;
use ddd\OilStation\DTO\OilPrice\OilPriceItemDTO;

/**
 * 具备事务能力
 * Class OilPriceApplyService
 * @package ddd\OilPrice\Application
 */
class OilPriceApplyService extends TransactionService{

    use TraitOilPriceApplyAttachmentRepository;
    use TraitOilCompanyRepository;
    use TraitOilStationRepository;
    use TraitOilGoodsRepository;
    use TraitOilPriceApplyRepository;

    /**
     * @param $applyId
     * @return OilPriceApply
     * @throws ZException
     */
    public function getEntityById($applyId):OilPriceApply{
        $entity = $this->getOilPriceApplyRepository()->findById($applyId);
        if(empty($entity)){
            throw new ZEntityNotExistsException($applyId, OilPriceApply::class);
        }

        return $entity;
    }

    /**
     * @param int $fileId
     * @return array
     * @throws ZException
     */
    public function getOilPriceListByExcelFile(int $fileId):array{
        list($isCanSubmit,$data) = $this->getOilPriceListByExcelFileSub($fileId);
        return [
            'is_can_submit' => $isCanSubmit,
            'data'          => $data,
        ];
    }

    /**
     * @param int $fileId
     * @return array
     * @throws ZException
     */
    protected function getOilPriceListByExcelFileSub(int $fileId):array {
        $fileEntity = $this->getAttachmentRepository()->findById($fileId);
        if(empty($fileEntity)){
            throw new ZEntityNotExistsException($fileId,Attachment::class);
        }

        $fields = [
            'station_name'   => '油站名称',
            'company_name'   => '油企名称',
            'goods_name'     => '油品名称',
            'retail_price'   => '零售价/元',
            'agreed_price'  => '协议价/元',
            'discount_price' => '优惠价/元',
        ];

        $excelData = \FileUtility::getExcelData($fields, $fileEntity->getPath());
        if(\CheckUtility::isEmpty($excelData)){
            throw new ZException(BusinessError::Oil_Price_Excel_Data_Is_Empty);
        }

        $companyIdNames = $this->getOilCompanyRepository()->getAllActiveCompanyIdNames();
        $stationIdNames = $this->getOilStationRepository()->getAllActiveStationIdNames();
        $goodsIdNames = $this->getOilGoodsRepository()->getAllActiveGoodsIdNames();
        $companyNameIds = array_flip($companyIdNames);
        $stationNameIds = array_flip($stationIdNames);
        $goodsNameIds = array_flip($goodsIdNames);

        $data = [];
        $isCanSubmit = true;
        $checkData = [];
        foreach($excelData as $key => & $datum){
            if(\CheckUtility::checkArrayAllValueIsEmpty($datum)){
                continue;
            }

            $status = true;
            $remark = [];

            $datum['company_id'] = $companyNameIds[$datum['company_name']] ?? 0;
            $datum['station_id'] = $stationNameIds[$datum['station_name']] ?? 0;
            $datum['goods_id'] = $goodsNameIds[$datum['goods_name']] ?? 0;

            if(empty($datum['company_name'])){
                $status = false;
                $remark[] = '油企名称缺失';
            }else{
                if(0 == $datum['company_id']){
                    $status = false;
                    $remark[] = '油企不存在';
                }
            }
            if(empty($datum['station_name'])){
                $status = false;
                $remark[] = '油站名称缺失';
            }else{
                if(0 == $datum['station_id']){
                    $status = false;
                    $remark[] = '油站不存在';
                }
            }
            if(empty($datum['goods_name'])){
                $status = false;
                $remark[] = '油品名称缺失';
            }else{
                if(0 == $datum['goods_id']){
                    $status = false;
                    $remark[] = '油品不存在';
                }
            }

            $uniqueKey = $datum['company_name'].$datum['station_name'].$datum['goods_name'];
            if(!empty($uniqueKey) && isset($checkData[$uniqueKey])){
                $status = false;
                $remark[] = '同一油站+油企+油品存在多条数据';
            }
            $checkData[$uniqueKey] = $uniqueKey;

            if(!isset($datum['retail_price'])){
                $status = false;
                $remark[] = '零售价缺失';
            }
            if(!isset($datum['agreed_price'])){
                $status = false;
                $remark[] = '协议价缺失';
            }
            if(!isset($datum['discount_price'])){
                $status = false;
                $remark[] = '优惠价缺失';
            }
            if(\MathUtility::less($datum['retail_price'], 0)){
                $status = false;
                $remark[] = '零售价必须大于等于0';
            }
            if(\MathUtility::less($datum['agreed_price'], 0)){
                $status = false;
                $remark[] = '协议价必须大于等于0';
            }
            if(\MathUtility::less($datum['discount_price'], 0)){
                $status = false;
                $remark[] = '优惠价必须大于等于0';
            }
            if(\MathUtility::getScale($datum['retail_price']) > 2){
                $status = false;
                $remark[] = '零售价只支持到分位';
            }
            if(\MathUtility::getScale($datum['agreed_price']) > 2){
                $status = false;
                $remark[] = '协议价只支持到分位';
            }
            if(\MathUtility::getScale($datum['discount_price']) > 2){
                $status = false;
                $remark[] = '优惠价只支持到分位';
            }

            if(isset($datum['discount_price']) && isset($datum['retail_price']) && \MathUtility::greater($datum['discount_price'], $datum['retail_price'])){
                $status = false;
                $remark[] = '优惠价＞零售价';
            }
            if(isset($datum['agreed_price']) && isset($datum['discount_price']) && \MathUtility::greater($datum['agreed_price'], $datum['discount_price'])){
                $status = false;
                $remark[] = '协议价＞优惠价';
            }
            $datum['remark'] = $status ? '成功' : implode('；', $remark);
            $datum['status'] = $status;
            if(!$status){
                $isCanSubmit = false;
            }
            $data[$key] = $datum;
        }

        return [
            $isCanSubmit,
            $data,
        ];
    }

    /**
     * @param int $fileId
     * @return OilPriceApplyDTO
     * @throws ZException
     */
    public function getOilPriceApplyDTO(int $fileId):OilPriceApplyDTO{
        list($isCanSubmit,$excelData) = $this->getOilPriceListByExcelFileSub($fileId);
        if(\CheckUtility::isEmpty($excelData)){
            throw new ZException(BusinessError::Oil_Price_Excel_Data_Is_Empty);
        }
        if(!$isCanSubmit){
            foreach($excelData as $key => $datum){
                if(!$datum['status']){
                    throw new ZException(BusinessError::Oil_Price_Excel_Data_Error,['num'=> $key + 1, 'error'=>$datum['remark']]);
                }
            }
        }

        $dto = new OilPriceApplyDTO();
        $dto->apply_id = 0;
        $dto->code = IDService::getOilPriceApplyCode();

        //
        $dto->items = [];
        foreach($excelData as & $datum){
            $itemDto = new OilPriceItemDTO();
            $itemDto->setAttributes($datum);
            $itemDto->remark = '';
            $dto->items[] = $itemDto;
        }

        $fileDto = new AttachmentDTO();
        $fileDto->setAttributes([
            'id'=> $fileId,
            'name'=> '',
            'url'=>'',
        ]);
        $dto->files[] = $fileDto;

        return $dto;
    }

    /**
     * @param OilPriceApply $entity
     * @return OilPriceApply
     * @throws \Exception
     */
    public function submit(OilPriceApply $entity):OilPriceApply{
        try{
            $this->beginTransaction();

            $entity->submit();

            $this->commitTransaction();

            return $entity;
        }catch(\Exception $e){
            $this->rollbackTransaction();

            throw $e;
        }
    }

    /**
     * @param OilPriceApply $entity
     * @return OilPriceApply
     * @throws \Exception
     */
    public function checkPassed(OilPriceApply $entity):OilPriceApply{
        try{
            $this->beginTransaction();

            $entity->checkPass();

            $this->commitTransaction();

            return $entity;
        }catch(\Exception $e){
            $this->rollbackTransaction();

            throw $e;
        }
    }
}