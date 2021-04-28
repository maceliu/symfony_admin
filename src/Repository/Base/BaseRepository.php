<?php


namespace SymfonyAdmin\Repository\Base;


use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\QueryException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
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

    protected $entityClass;

    protected $alias = 'b';

    protected $findAllCriteria = [];

    /** @var Request */
    protected $request;

    public function __construct(ManagerRegistry $registry, RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
        parent::__construct($registry, $this->entityClass);
    }

    /**
     * @param int $pageNum
     * @param int $pageSize
     * @param array $conditions
     * @param array $orderBy
     * @return PaginatorResult
     * @throws QueryException
     */
    public function findAllWithPage(int $pageNum, int $pageSize, array $conditions = [], array $orderBy = []): PaginatorResult
    {
        $qb = $this->createQueryBuilder($this->alias);
        $qb = $this->createQueryBuilderByConditions($qb, $conditions);
        # 如果不传入排序数组，则使用默认查询条件
        $qb->addCriteria(Criteria::create()->orderBy(
            empty($orderBy) ? ["{$this->alias}.createTime" => Criteria::DESC] : $orderBy
        ));
        return new PaginatorResult(new Paginator($qb), $pageNum, $pageSize);
    }

    /**
     * @return BaseEntity[]
     */
    public function findAllByCriteria(): array
    {
        return $this->findBy($this->findAllCriteria);
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
     * @param array $conditions
     * @return QueryBuilder
     */
    public function createQueryBuilderByConditions(QueryBuilder $qb, array $conditions = []): QueryBuilder
    {
        foreach ($this->searchMap as $searchKey => $type) {
            if ($this->request->query->get($searchKey)) {
                $conditions[$searchKey] = trim($this->request->query->get($searchKey));
            }
        }

        $keyStr = '';
        if (!empty($this->searchMap)) {
            foreach ($conditions as $key => $value) {
                if (empty($this->searchMap[$key])) {
                    continue;
                }
                if ($this->alias != "") {
                    $keyStr = "{$this->alias}" . '.' . "{$key}";
                }
                switch ($this->searchMap[$key]) {
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