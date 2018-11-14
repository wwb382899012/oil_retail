<?php

namespace ddd\OilStation\Application;

use app\components\PageData;
use app\ddd\Order\Domain\Goods\GoodsService;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ZException;
use ddd\OilStation\Domain\OilCompany\OilCompanyStatusEnum;
use ddd\OilStation\Domain\OilPrice\TraitOilPriceRepository;
use ddd\OilStation\Domain\OilStation\OilStation;
use ddd\OilStation\Domain\OilStation\OilStationEnum;
use ddd\OilStation\Domain\OilStation\TraitOilStationRepository;
use ddd\OilStation\DTO\OilStation\OilStationDetailDTO;
use ddd\OilStation\DTO\OilStation\OilStationDTO;
use ddd\Order\Domain\Order\OrderStatusEnum;

/**
 * 具备事务能力
 * Class OilStationService
 * @package ddd\OilStation\Application
 */
class OilStationService extends OilService
{

    /**
     * 地球半径系数
     */
    protected CONST EARTH_RADIUS = 6370.996;

    use TraitOilPriceRepository;

    use TraitOilStationRepository;

    public function assetDto(array $reqData): OilStationDTO {
        $dto = new OilStationDTO();
        $dto->setAttributes($reqData);
        $dto->files = $this->getFileDtos($reqData);

        return $dto;
    }

    /**
     * @param int $stationId
     * @return OilStationDTO
     * @throws \Exception
     */
    public function getDetailDto(int $stationId): OilStationDTO {
        $entity = $this->getEntityById($stationId);

        $dto = new OilStationDTO();
        $dto->fromEntity($entity);

        return $dto;
    }

    /**
     * @param int $stationId
     * @return OilStationDetailDTO
     * @throws \Exception
     */
    public function getDetailDtoForOut($params): OilStationDetailDTO {
        $stationId = $params['station_id'];
        $longitude = $params['longitude'];
        $latitude = $params['latitude'];
        $entity = $this->getEntityById($stationId);

//        $oilPriceEntities = $this->getOilPriceRepository()->findAllActivePriceByStationId($stationId);

        $oilGoodsEntities = GoodsService::service()->getOilStationAllCanSellGoods($stationId);

        $dto = new OilStationDetailDTO();
        $dto->fromEntity($entity, $oilGoodsEntities);
        $dto->distance = $this->getDistance([$longitude, $latitude], [$dto->longitude, $dto->latitude]);
        return $dto;
    }


    /**
     * 获取附件油站列表
     * @param array $search
     * @param int $currPage
     * @param int $pageSize
     * @return array
     */
    public function getUserNearbyOilStationList(array $search, int $currPage = 1, int $pageSize = 10): ?PageData {
        $longitude = $search['longitude'];
        $latitude = $search['latitude'];
        $max_distance = $search['max_distance'] ?? 100;

//        if(\MathUtility::less($longitude,-180) || \MathUtility::greater($longitude,180)){
//            throw new \ddd\Infrastructure\error\ZException("经度数值异常！");
//        }
//        if(\MathUtility::less($latitude,-180) || \MathUtility::greater($latitude,180)){
//            throw new \ddd\Infrastructure\error\ZException("维度数值异常！");
//        }

        $attr = array(
            'os.name*' => $search['name'],
            'os.status' => OilStationEnum::ENABLE,
            'oc.status' => OilCompanyStatusEnum::ENABLE,
        );
        $where = \DbUtility::getWhereSql($attr);


        $sub_where = ' tmp.distance <= ' . $max_distance;
        $distance_fields = $this->getDistanceField($search['longitude'], $search['latitude'], 'os', 'distance');
        $mostVisitStationId = $this->getMostVisitStation($search['user_id']);
        $sql = <<<SQL
SELECT
	{col}
FROM
	(
		SELECT
			os.station_id,
			os.name,
			os.status,
			os.company_id,
			os.province_id,
			os.city_id,
			os.address,
			os.longitude,
			os.latitude,
			os.remark,
			os.contact_person,
			os.contact_phone,
			oc.name AS company_name,
			ac.area_name AS province,
			acb.area_name AS city,
			{$distance_fields},
			2 AS closest,
			(IF(station_id={$mostVisitStationId},1,2)) AS most_visit
		FROM
			t_oil_station AS os
		LEFT JOIN t_oil_company AS oc ON os.company_id = oc.company_id
		LEFT JOIN t_area_code AS ac ON os.province_id = ac.area_code
		LEFT JOIN t_area_code AS acb ON os.city_id = acb.area_code 
        {$where} 
		ORDER BY
			distance ASC,
			most_visit ASC 
	) AS tmp
WHERE
	{$sub_where}
SQL;
        $data = \DbUtility::getTableData($sql, '*', $currPage, $pageSize);
        if (!empty($data)) {
            //TODO: 还没有处理完
            foreach ($data->data as $k => & $datum) {
                if ($k == 0) {
                    $datum['closest'] = 1; //最近的
                }
//                if ($k == 1) {
//                    $datum['most_visit'] = 1; //最常去的
//                }
            }
        }

        return $data;
    }

    /**
     * @param float $longitude
     * @param float $latitude
     * @param string $tablePrefix
     * @param string $fieldName
     * @return string
     */
    private function getDistanceField(float $longitude, float $latitude, $tablePrefix = 't', $fieldName = 'distance'): string {
        $tablePrefix .= '.';
        $fieldName = empty($fieldName) ? 'distance' : $fieldName;
        $earthRadius = static::EARTH_RADIUS; // 地球半径系数

        $field = <<<FILED
ROUND(
	{$earthRadius} * 2 * ASIN(
		SQRT(
			POW(
				SIN(
					(
						{$latitude} * PI() / 180 - {$tablePrefix}latitude * PI() / 180
					) / 2
				),
				2
			) + COS({$latitude} * PI() / 180) * COS({$tablePrefix}latitude * PI() / 180) * POW(
				SIN(
					(
						{$longitude} * PI() / 180 - {$tablePrefix}longitude * PI() / 180
					) / 2
				),
				2
			)
		)
	),1) AS {$fieldName}
FILED;

        return $field;
    }

    protected function getMostVisitStation($customerId) {
        $sql = 'SELECT COUNT(1) as num,station_id FROM `t_order` WHERE status=' . OrderStatusEnum::Status_Effected . ' AND customer_id=' . $customerId . '  GROUP BY station_id ORDER BY num desc ;';
        $data = \Utility::query($sql);
        $stationId = 0;
        if (\Utility::isNotEmpty($data)) {
            $stationId = $data[0]['station_id'];
        }
        return $stationId;
    }


    /**
     * 根据起点坐标和终点坐标测距离
     * @param  [array]   $from    [起点坐标(经纬度),例如:array(118.012951,36.810024)]
     * @param  [array]   $to    [终点坐标(经纬度)]
     * @param  [bool]    $km        是否以公里为单位 false:米 true:公里(千米)
     * @param  [int]     $decimal   精度 保留小数位数
     * @return [string]  距离数值
     */
    public function getDistance($from, $to, $km = true, $decimal = 1) {
        sort($from);
        sort($to);
        $earth_radius = static::EARTH_RADIUS; // 地球半径系数

        $distance = $earth_radius * 2 * asin(sqrt(pow(sin(($from[0] * pi() / 180 - $to[0] * pi() / 180) / 2), 2) + cos($from[0] * pi() / 180) * cos($to[0] * pi() / 180) * pow(sin(($from[1] * pi() / 180 - $to[1] * pi() / 180) / 2), 2))) * 1000;

        if ($km) {
            $distance = $distance / 1000;
        }

        return round($distance, $decimal);
    }

    /**
     * @param array $search
     * @param int $currPage
     * @param int $pageSize
     * @return PageData|null
     */
    public function getListData(array $search, int $currPage = 1, int $pageSize = 10): ?PageData {
        $attr = array(
            'os.station_id' => $search['station_id'],
            'os.name*' => $search['name'],
            'oc.name*' => $search['company_name'],
            'oc.company_id' => $search['company_id'],
            'os.corporate*' => $search['corporate'],
            'os.address*' => $search['address'],
            'os.province_id' => $search['province_id'],
            'os.city_id' => $search['city_id'],
            'os.status' => $search['status'],
            'oc.status' => $search['company_status']
        );
        $where = \DbUtility::getWhereSql($attr);

        $sql = <<<SQL
SELECT {col} FROM t_oil_station AS os 
LEFT JOIN t_oil_company AS oc ON os.company_id = oc.company_id 
LEFT JOIN t_area_code AS ac ON os.province_id = ac.area_code 
LEFT JOIN t_area_code AS acb ON os.city_id = acb.area_code 
$where ORDER BY os.update_time DESC
SQL;

        $fields = [
            'os.station_id,os.name,os.company_id,os.province_id,os.city_id,os.address,os.longitude,os.latitude,os.remark',
            'os.contact_person,os.contact_phone,os.status,oc.name AS company_name,ac.area_name AS province,acb.area_name AS city',
        ];

        return \DbUtility::getTableData($sql, implode(',', $fields), $currPage, $pageSize);
    }

    /**
     * @param $stationId
     * @return OilStation
     * @throws \Exception
     */
    public function getEntityById($stationId): OilStation {
        $entity = $this->getOilStationRepository()->findById($stationId);
        if (empty($entity)) {
            throw new ZException(BusinessError::ENTITY_NOT_EXISTS);
        }

        return $entity;
    }

    /**
     * @param OilStation $entity
     * @return OilStation
     * @throws \Exception
     */
    public function save(OilStation $entity): OilStation {
        try {
            $this->beginTransaction();

            $entity->save();

            $this->commitTransaction();

            return $entity;
        } catch (\Exception $e) {
            $this->rollbackTransaction();

            throw $e;
        }
    }

    /**
     * 设为启用,禁用
     * @param int $stationId
     * @param bool $state
     * @return OilStation
     * @throws \Exception
     */
    public function setOnOff(int $stationId, bool $state) {
        try {
            $this->beginTransaction();

            $entity = $this->getEntityById($stationId);

            $entity->setOnOff($state);

            $this->commitTransaction();

            \AMQPService::publishOilStationToFinanceSystem($stationId);

            return $entity;
        } catch (\Exception $e) {
            $this->rollbackTransaction();

            throw $e;
        }
    }

}