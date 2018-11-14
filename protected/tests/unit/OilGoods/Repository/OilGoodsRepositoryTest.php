<?php
/**
 * User: liyu
 * Date: 2018/9/14
 * Time: 15:06
 * Desc: OilGoodsRepositoryTest.php
 */

use ddd\OilStation\Repository\OilGoodsRepository;
use PHPUnit\Framework\TestCase;

class OilGoodsRepositoryTest extends TestCase
{

    use \ddd\OilStation\Domain\OilGoods\TraitOilGoodsRepository;

    public function testGetAllActiveGoods() {
        $service=$this->getOilGoodsRepository()->GetAllActiveGoods();
    }
}
