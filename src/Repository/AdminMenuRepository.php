<?php


namespace SymfonyAdmin\Repository;


use SymfonyAdmin\Entity\AdminMenu;
use SymfonyAdmin\Utils\Enum\Menu\MenuTypeEnum;
use SymfonyAdmin\Utils\Enum\StatusEnum;
use SymfonyAdmin\Utils\MenuFormat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ReflectionException;

class AdminMenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminMenu::class);
    }

    /**
     * @param array $menuIds
     * @return AdminMenu[]
     */
    public function findAllAuthMenu(array $menuIds): array
    {
        $qb = $this->createQueryBuilder('m');
        $qb->where('m.status = :status')
            ->andWhere('m.type = :type')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->in('m.id', $menuIds),
                $qb->expr()->eq('m.isPublic', 1)
            ))
            ->setParameter('status', StatusEnum::ON)
            ->setParameter('type', MenuTypeEnum::MENU);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $menuIds
     * @return AdminMenu[]
     */
    public function findAllByMenuIds(array $menuIds): array
    {
        if (empty($menuIds)) {
            return [];
        }

        $qb = $this->createQueryBuilder('m');
        $qb->where($qb->expr()->orX(
            $qb->expr()->in('m.id', $menuIds)
        ))
            ->orderBy('m.level', 'asc')
            ->orderBy('m.weight', 'desc');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $menuList
     * @return AdminMenu[]
     * @throws ReflectionException
     */
    public function findAllByMenuIdsWithFormat(array $menuList): array
    {
        # 将菜单预分级
        $menuLevelList = [];
        foreach ($menuList as $menu) {
            $menuLevelList[$menu->getLevel()][$menu->getParentId()][] = $menu;
        }

        # 菜单格式化展示
        $menuFormat = new MenuFormat($menuLevelList);
        return $menuFormat->getResult();
    }

    /**
     * @param string $path
     * @return object|AdminMenu
     */
    public function findOneByPath(string $path)
    {
        return $this->findOneBy(['path' => $path]);
    }

    /**
     * @param int $id
     * @return object|AdminMenu
     */
    public function findOneById(int $id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * @param int $parentId
     * @return AdminMenu[]
     */
    public function findAllByParentId(int $parentId): array
    {
        return $this->findBy(['parentId' => $parentId]);
    }

    /**
     * @param AdminMenu $adminMenu
     * @return array
     */
    public function findMultiAllByParentMenu(AdminMenu $adminMenu): array
    {
        $childMenus = [];
        $doLoopMenus = [$adminMenu];

        while (!empty($doLoopMenus)) {
            $nextLoopMenus = [];

            foreach ($doLoopMenus as $adminMenu) {
                $childMenuList = $this->findAllByParentId($adminMenu->getId());
                if (empty($childMenuList)) {
                    continue;
                }

                foreach ($childMenuList as $childMenu) {
                    # 如果子用户组id已经存在，则说明存在死循环配置，退出查询
                    if (key_exists($childMenu->getId(), $childMenus) || $childMenu->getId() == $adminMenu->getId()) {
                        break;
                    }
                    $nextLoopMenus[] = $childMenu;
                    $childMenus[$childMenu->getId()] = $childMenu->getId();
                }
            }

            $doLoopMenus = $nextLoopMenus;
        }

        return $childMenus;
    }
}
