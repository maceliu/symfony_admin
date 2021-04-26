<?php


namespace SymfonyAdmin\Repository;


use SymfonyAdmin\Entity\AdminUser;
use SymfonyAdmin\Repository\Base\BaseRepository;
use SymfonyAdmin\Utils\Enum\SearchTypeEnum;
use SymfonyAdmin\Utils\PaginatorResult;
use Doctrine\ORM\Tools\Pagination\Paginator;

class AdminUserRepository extends BaseRepository
{
    protected $entity = AdminUser::class;

    static $searchMap = [
        'id' => SearchTypeEnum::PRECISE,
        'username' => SearchTypeEnum::FUZZY,
        'trueName' => SearchTypeEnum::FUZZY,
        'status' => SearchTypeEnum::PRECISE,
    ];

    /**
     * @param string $username
     * @return AdminUser|object|NULL
     */
    public function findOneByUsername(string $username): ?AdminUser
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
