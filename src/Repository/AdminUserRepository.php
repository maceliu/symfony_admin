<?php


namespace SymfonyAdmin\Repository;


use SymfonyAdmin\Entity\AdminUser;
use App\Service\Base\QueryTrait;
use App\Utils\Enum\SearchTypeEnum;
use App\Utils\PaginatorResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class AdminUserRepository extends ServiceEntityRepository
{
    use QueryTrait;

    static $searchMap = [
        'id' => SearchTypeEnum::PRECISE,
        'username' => SearchTypeEnum::FUZZY,
        'trueName' => SearchTypeEnum::FUZZY,
        'status' => SearchTypeEnum::PRECISE,
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminUser::class);
    }

    /**
     * @param int $userId
     * @return AdminUser|object
     */
    public function findOneById(int $userId)
    {
        return $this->findOneBy(['id' => $userId]);
    }

    /**
     * @param string $username
     * @return AdminUser|object
     */
    public function findOneByUsername(string $username)
    {
        return $this->findOneBy(['username' => $username]);
    }

    /**
     * @param array $ids
     * @param int $pageNum
     * @param int $pageSize
     * @param array $conditions
     * @return PaginatorResult
     */
    public function findAllByIdsWithPage(array $ids, int $pageNum = 1, int $pageSize = 10, array $conditions = []): PaginatorResult
    {
        $qb = $this->createQueryBuilder('u');
        $qb->where($qb->expr()->in('u.roleId', $ids))
            ->orderBy('u.id', 'desc');
        $qb = $this->findAllByConditionsWithPage($qb, self::$searchMap, 'u', $conditions);

        return new PaginatorResult(new Paginator($qb), $pageNum, $pageSize);
    }

}
