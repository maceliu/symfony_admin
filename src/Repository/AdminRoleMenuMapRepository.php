<?php


namespace SymfonyAdmin\Repository;


use SymfonyAdmin\Entity\AdminRoleMenuMap;
use SymfonyAdmin\Repository\Base\BaseRepository;
use SymfonyAdmin\Utils\Enum\StatusEnum;

class AdminRoleMenuMapRepository extends BaseRepository
{
    protected $entityClass = AdminRoleMenuMap::class;

    /**
     * @param int $roleId
     * @return AdminRoleMenuMap[]
     */
    public function findOnAllByRoleId(int $roleId): array
    {
        return $this->findBy(['roleId' => $roleId, 'status' => StatusEnum::ON]);
    }

    /**
     * @param int $roleId
     * @param int $menuId
     * @return object|AdminRoleMenuMap|NULL
     */
    public function findOnOneByRoleIdAndMenuId(int $roleId, int $menuId): ?AdminRoleMenuMap
    {
        return $this->findOneBy(['roleId' => $roleId, 'menuId' => $menuId, 'status' => StatusEnum::ON]);
    }

    /**
     * @param int $roleId
     * @param int $menuId
     * @return object|AdminRoleMenuMap|NULL
     */
    public function findOneByRoleIdAndMenuId(int $roleId, int $menuId): ?AdminRoleMenuMap
    {
        return $this->findOneBy(['roleId' => $roleId, 'menuId' => $menuId]);
    }

    /**
     * @param int $roleId
     * @return AdminRoleMenuMap[]
     */
    public function findAllByRoleId(int $roleId): array
    {
        return $this->findBy(['roleId' => $roleId]);
    }

}
