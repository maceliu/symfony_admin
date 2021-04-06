<?php


namespace SymfonyAdmin\Repository;


use SymfonyAdmin\Entity\AdminRole;
use SymfonyAdmin\Service\Base\QueryTrait;
use SymfonyAdmin\Utils\Enum\SearchTypeEnum;
use SymfonyAdmin\Utils\PaginatorResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

class AdminRoleRepository extends ServiceEntityRepository
{
    use QueryTrait;

    static $searchMap = [
        'roleName' => SearchTypeEnum::FUZZY,
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminRole::class);
    }

    /**
     * @param string $roleName
     * @return AdminRole|object|NULL
     */
    public function findOneByName(string $roleName): ?AdminRole
    {
        return $this->findOneBy(['roleName' => $roleName]);
    }

    /**
     * @param int $id
     * @return AdminRole|object|NULL
     */
    public function findOneById(int $id): ?AdminRole
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * @param string $roleCode
     * @return AdminRole|object|NULL
     */
    public function findOneByRoleCode(string $roleCode): ?AdminRole
    {
        return $this->findOneBy(['roleCode' => $roleCode]);
    }

    /**
     * @param int $parentId
     * @return AdminRole[]
     */
    public function findAllByParentId(int $parentId): array
    {
        return $this->findBy(['parentId' => $parentId]);
    }

    /**
     * @param int $parentId
     * @return array
     */
    public function findMultiAllIdsByParentId(int $parentId): array
    {
        $childRoleIds = [];
        $doLoopRoleIds = [$parentId];
        while (!empty($doLoopRoleIds)) {
            $nextLoopRoleIds = [];
            foreach ($doLoopRoleIds as $roleId) {
                $childRoleList = $this->findAllByParentId($roleId);
                if (empty($childRoleList)) {
                    continue;
                }

                foreach ($childRoleList as $childRole) {
                    # 如果子用户组id已经存在，则说明存在死循环配置，退出查询
                    if (in_array($childRole->getId(), $childRoleIds) || $childRole->getId() == $parentId) {
                        break;
                    }

                    $nextLoopRoleIds[] = $childRole->getId();
                    $childRoleIds[] = $childRole->getId();
                }
            }
            $doLoopRoleIds = $nextLoopRoleIds;
        }

        return $childRoleIds;
    }

    /**
     * @param AdminRole $adminRole
     * @param bool $isOnlyId
     * @return array
     */
    public function findMultiAllByParentRole(AdminRole $adminRole, $isOnlyId = true): array
    {
        $childRoles = [];
        $adminRole->setLevel(0);
        $doLoopRoles = [$adminRole];

        while (!empty($doLoopRoles)) {
            $nextLoopRoles = [];

            foreach ($doLoopRoles as $adminRole) {
                $childRoleList = $this->findAllByParentId($adminRole->getId());
                if (empty($childRoleList)) {
                    continue;
                }
                $adminRole->setHasChild(true);

                $count = count($childRoleList);
                $countTemp = 0;
                $hasBrother = true;

                foreach ($childRoleList as $childRole) {
                    $countTemp++;
                    # 如果子用户组id已经存在，则说明存在死循环配置，退出查询
                    if (key_exists($childRole->getId(), $childRoles) || $childRole->getId() == $adminRole->getId()) {
                        break;
                    }
                    $nextLoopRoles[] = $childRole;

                    if ($countTemp >= $count) {
                        $hasBrother = false;
                    }
                    $childRole->setHasBrother($hasBrother);
                    $childRole->setLevel($adminRole->getLevel() + 1);
                    $childRoles[$childRole->getId()] = $isOnlyId ? $childRole->getId() : $childRole->getApiFormat(true);
                }
            }

            $doLoopRoles = $nextLoopRoles;
        }

        return $childRoles;
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
        $qb = $this->createQueryBuilder('r');
        $qb->where($qb->expr()->in('r.id', $ids))
            ->orderBy('r.id', 'desc');

        $qb = $this->findAllByConditionsWithPage($qb, self::$searchMap, 'r', $conditions);

        return new PaginatorResult(new Paginator($qb), $pageNum, $pageSize);
    }

    /**
     * @param string $roleName
     * @param string $roleCode
     * @param int|null $id
     * @return AdminRole|NULL
     */
    public function findConflictOneOnyByNameOrCode(string $roleName, string $roleCode, int $id = null): ?AdminRole
    {
        $qb = $this->createQueryBuilder('r');
        $qb->where($qb->expr()->orX(
            $qb->expr()->eq('r.roleName', $roleName),
            $qb->expr()->eq('r.roleCode', $roleCode)
        ));

        if (!empty($id)) {
            $qb->andWhere($qb->expr()->neq('r.id', $id));
        }

        $qb->setMaxResults(1);

        return $qb->getQuery()->getResult();
    }
}
