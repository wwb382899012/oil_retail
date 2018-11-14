<?php
/**
 * User: liyu
 * Date: 2018/9/13
 * Time: 16:03
 * Desc: OilPriceServiceTest.php
 */

use ddd\OilStation\Application\OilPriceService;
use PHPUnit\Framework\TestCase;

class OilPriceServiceTest extends TestCase
{

    protected $service;
    public $stationId;
    public $goodsId;

    public function setUp() {
        $this->service = new OilPriceService();
        $this->stationId = 1;
        $this->goodsId = 11;
    }

    public function testGetActivePriceByOilStationAndGoodsId() {
        $res=$this->service->getActivePriceByOilStationAndGoodsId($this->stationId, $this->goodsId);
        $this->assertNotEmpty($res);
    }
}
