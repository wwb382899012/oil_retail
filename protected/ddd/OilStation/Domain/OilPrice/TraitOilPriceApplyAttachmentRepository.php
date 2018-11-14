<?php

namespace ddd\OilStation\Domain\OilPrice;


use ddd\Infrastructure\DIService;

trait TraitOilPriceApplyAttachmentRepository{

    private $attachmentRepository;

    /**
     * @return IOilPriceApplyAttachmentRepository
     * @throws \Exception
     */
    protected function getAttachmentRepository():IOilPriceApplyAttachmentRepository{
        if(empty($this->attachmentRepository)){
            $this->attachmentRepository = DIService::getRepository(IOilPriceApplyAttachmentRepository::class);
        }
        return $this->attachmentRepository;
    }
}