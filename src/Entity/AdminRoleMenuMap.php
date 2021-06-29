<?php

namespace SymfonyAdmin\Entity;

use SymfonyAdmin\Entity\Base\BaseEntity;
use SymfonyAdmin\Entity\Base\LogTrait;
use SymfonyAdmin\Utils\Enum\StatusEnum;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

/**
 * AdminRoleMenuMap
 *
 * @ORM\Table(name="admin_role_menu_map")
 * @ORM\Entity(repositoryClass="SymfonyAdmin\Repository\AdminRoleMenuMapRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class AdminRoleMenuMap extends BaseEntity
{
    use LogTrait;
    use SoftDeleteableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="role_id", type="integer", nullable=false, options={"comment"="关联用户组id"})
     */
    private $roleId = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="menu_id", type="integer", nullable=false, options={"comment"="关联菜单id"})
     */
    private $menuId = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=32, nullable=false, options={"default"="on","comment"="关联状态on生效 off失效"})
     */
    private $status = StatusEnum::ON;

    /**
     * @return int
     */
    public function getRoleId(): int
    {
        return $this->roleId;
    }

    /**
     * @param int $roleId
     */
    public function setRoleId(int $roleId): void
    {
        $this->roleId = $roleId;
    }

    /**
     * @return int
     */
    public function getMenuId(): int
    {
        return $this->menuId;
    }

    /**
     * @param int $menuId
     */
    public function setMenuId(int $menuId): void
    {
        $this->menuId = $menuId;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

}
