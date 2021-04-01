<?php

namespace SymfonyAdmin\Service\Base;

use SymfonyAdmin\Utils\Enum\SearchTypeEnum;
use Doctrine\ORM\QueryBuilder;

trait QueryTrait
{
    /**
     * @param QueryBuilder $qb
     * @param array $searchMap
     * @param string $alias
     * @param array $conditions
     * @return QueryBuilder
     */
    public function findAllByConditionsWithPage(QueryBuilder $qb, array $searchMap = [], string $alias = '', array $conditions = []): QueryBuilder
    {
        $keyStr = '';
        if (!empty($searchMap)) {
            foreach ($conditions as $key => $value) {
                if (empty($searchMap[$key])) {
                    continue;
                }
                if ($alias != "") {
                    $keyStr = "{$alias}" . '.' . "{$key}";
                }
                switch ($searchMap[$key]) {
                    case SearchTypeEnum::FUZZY:
                        $qb->andWhere("{$keyStr} LIKE :{$key}")
                            ->setParameter($key, "%{$value}%");
                        break;
                    case SearchTypeEnum::PRECISE:
                        $qb->andWhere("{$keyStr} = :{$key}")
                            ->setParameter($key, "{$value}");
                        break;
                }
            }
        }
        return $qb;

    }
}