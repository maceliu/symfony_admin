<?php


namespace SymfonyAdmin\Repository\Base;


use SymfonyAdmin\Entity\AdminFile;
use SymfonyAdmin\Entity\Base\BaseEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use SymfonyAdmin\Utils\Enum\SearchTypeEnum;
use SymfonyAdmin\Utils\PaginatorResult;

class BaseRepository extends ServiceEntityRepository
{
    public $searchMap = [];

    protected $entity;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, $this->entity);
    }

    /**
     * @param int $pageNum
     * @param int $pageSize
     * @param array $conditions
     * @return PaginatorResult
     */
    public function findAllWithPage(int $pageNum, int $pageSize, array $conditions = []): PaginatorResult
    {
        $qb = $this->createQueryBuilder('b');
        $qb = $this->findAllByConditionsWithPage($qb, $this->searchMap, 'b', $conditions);
        $qb->orderBy('b.createTime', 'desc');

        return new PaginatorResult(new Paginator($qb), $pageNum, $pageSize);
    }

    /**
     * @param int $id
     * @return BaseEntity|AdminFile
     */
    public function findOneById(int $id)
    {
        return $this->findOneBy(['id' => $id]);
    }

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

    /**
     * @return array
     */
    public function getSearchMap(): array
    {
        return $this->searchMap;
    }
}