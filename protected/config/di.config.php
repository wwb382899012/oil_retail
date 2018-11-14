<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/8 15:08
 * Describe：
 *  依赖注入的配置项，主要是接口到实际类的配置，支持两种方式，一是key=>value，key为别名，value为实际的类名，二是直接数组配置
 */

return [
    ['class'=>'inc','definition'=>'ddd\Infrastructure\Utility','params'=>[],'singleton'=>1],

    //额度
    ['class'=> 'ddd\Quota\Domain\LogisticsQuota\ILogisticsQuotaRepository', 'definition'=> 'ddd\Quota\Repository\LogisticsQuota\LogisticsQuotaRepository', 'params'=>[], 'singleton'=>1],
    ['class'=> 'ddd\Quota\Domain\LogisticsQuota\ILogisticsQuotaLogRepository', 'definition'=> 'ddd\Quota\Repository\LogisticsQuota\LogisticsQuotaLogRepository', 'params'=>[], 'singleton'=>1],
    ['class'=> 'ddd\Quota\Domain\LogisticsQuota\ILogisticsDailyQuotaRepository', 'definition'=> 'ddd\Quota\Repository\LogisticsQuota\LogisticsDailyQuotaRepository', 'params'=>[], 'singleton'=>1],
    ['class'=> 'ddd\Quota\Domain\LogisticsQuota\ILogisticsDailyQuotaLogRepository', 'definition'=> 'ddd\Quota\Repository\LogisticsQuota\LogisticsDailyQuotaLogRepository', 'params'=>[], 'singleton'=>1],
    ['class'=> 'ddd\Quota\Domain\VehicleQuota\IVehicleDailyQuotaRepository', 'definition'=> 'ddd\Quota\Repository\VehicleQuota\VehicleDailyQuotaRepository', 'params'=>[], 'singleton'=>1],
    ['class'=> 'ddd\Quota\Domain\VehicleQuota\IVehicleDailyQuotaLogRepository', 'definition'=> 'ddd\Quota\Repository\VehicleQuota\VehicleDailyQuotaLogRepository', 'params'=>[], 'singleton'=>1],
    ['class' => 'ddd\Quota\Domain\LogisticsQuotaLimit\ILogisticsQuotaLimitRepository', 'definition' => 'ddd\Quota\Repository\LogisticsQuotaLimit\LogisticsQuotaLimitRepository', 'params' =>[], 'singleton' =>1],
    ['class'=> 'ddd\Quota\Domain\VehicleQuotaLimit\IVehicleQuotaLimitRepository', 'definition'=> 'ddd\Quota\Repository\VehicleQuotaLimit\VehicleQuotaLimitRepository', 'params'=>[], 'singleton'=>1],

    //系统管理
    ['class'=> 'app\ddd\Admin\Domain\User\ISystemUserRepository', 'definition'=> 'app\ddd\Admin\Repository\User\SystemUserRepository', 'params'=>[], 'singleton'=>1],
    ['class'=> 'app\ddd\Admin\Domain\Menu\IUserMenuRepository', 'definition'=> 'app\ddd\Admin\Repository\Menu\UserMenuRepository', 'params'=>[], 'singleton'=>1],
    ['class'=> 'app\ddd\Admin\Domain\Right\IUserRightRepository', 'definition'=> 'app\ddd\Admin\Repository\Right\UserRightRepository', 'params'=>[], 'singleton'=>1],
    ['class'=> 'app\ddd\Admin\Domain\Right\IRoleRightRepository', 'definition'=> 'app\ddd\Admin\Repository\Right\RoleRightRepository', 'params'=>[], 'singleton'=>1],
    ['class'=> 'app\ddd\Admin\Domain\Module\ISystemModuleRepository', 'definition'=> 'app\ddd\Admin\Repository\Module\SystemModuleRepository', 'params'=>[], 'singleton'=>1],
    ['class'=> 'app\ddd\Admin\Domain\Module\IModuleActionTreeRepository', 'definition'=> 'app\ddd\Admin\Repository\Module\ModuleActionTreeRepository', 'params'=>[], 'singleton'=>1],
    ['class'=> 'app\ddd\Admin\Domain\Role\ISystemRoleRepository', 'definition'=> 'app\ddd\Admin\Repository\Role\SystemRoleRepository', 'params'=>[], 'singleton'=>1],

    //油站管理
    ['class' => 'ddd\OilStation\Domain\IAreaDictTreeRepository', 'definition' => 'ddd\OilStation\Repository\AreaDictTreeTreeRepository', 'params' =>[], 'singleton' =>1],
    ['class'=> 'ddd\OilStation\Domain\OilCompany\IOilCompanyRepository', 'definition'=> 'ddd\OilStation\Repository\OilCompanyRepository', 'params'=>[], 'singleton'=>1],
    ['class'=> 'ddd\OilStation\Domain\OilStation\IOilStationApplyRepository', 'definition'=> 'ddd\OilStation\Repository\OilStationApplyRepository', 'params'=>[], 'singleton'=>1],
    ['class'=> 'ddd\OilStation\Domain\OilStation\IOilStationRepository', 'definition'=> 'ddd\OilStation\Repository\OilStationRepository', 'params'=>[], 'singleton'=>1],
    ['class'=> 'ddd\OilStation\Domain\OilGoods\IOilGoodsRepository', 'definition'=> 'ddd\OilStation\Repository\OilGoodsRepository', 'params'=>[], 'singleton'=>1],
    ['class'=> 'ddd\OilStation\Domain\OilPrice\IOilPriceApplyRepository', 'definition'=> 'ddd\OilStation\Repository\OilPriceApplyRepository', 'params'=>[], 'singleton'=>1],
    ['class'=> 'ddd\OilStation\Domain\OilPrice\IOilPriceRepository', 'definition'=> 'ddd\OilStation\Repository\OilPriceRepository', 'params'=>[], 'singleton'=>1],
    ['class'=> 'ddd\OilStation\Domain\OilPrice\IOilPriceItemRepository', 'definition'=> 'ddd\OilStation\Repository\OilPriceItemRepository', 'params'=>[], 'singleton'=>1],
    ['class' => 'ddd\OilStation\Domain\OilPrice\IOilPriceApplyAttachmentRepository', 'definition' => 'ddd\OilStation\Repository\OilPriceApplyAttachmentRepository', 'params' =>[], 'singleton' =>1],
    ['class'=> 'ddd\OilStation\Domain\OilPhone\IOilPhoneRepository', 'definition'=> 'ddd\OilStation\Repository\OilPhoneRepository', 'params'=>[], 'singleton'=>1],

    //物流
    ['class'=> 'ddd\Logistics\Domain\LogisticsCompany\ILogisticsCompanyRepository', 'definition'=> 'ddd\Logistics\Repository\LogisticsCompanyRepository', 'params'=>[], 'singleton'=>1],
    ['class'=> 'ddd\Logistics\Domain\Driver\IDriverRepository', 'definition'=> 'ddd\Logistics\Repository\DriverRepository', 'params'=>[], 'singleton'=>1],
    ['class'=> 'ddd\Logistics\Domain\Vehicle\IVehicleRepository', 'definition'=> 'ddd\Logistics\Repository\VehicleRepository', 'params'=>[], 'singleton'=>1],

    //客户
    ['class'=> 'ddd\Customer\Domain\ICustomerRepository', 'definition'=> 'ddd\Customer\Repository\CustomerRepository', 'params'=>[], 'singleton'=>1],

    //订单
    ['class'=> 'ddd\Order\Domain\Order\IOrderRepository', 'definition'=> 'ddd\Order\Repository\Order\OrderRepository', 'params'=>[], 'singleton'=>1],
];