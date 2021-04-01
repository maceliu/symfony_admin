<?php

namespace SymfonyAdmin\Entity;

use SymfonyAdmin\Entity\Base\BaseEntity;
use SymfonyAdmin\Entity\Base\CommonTrait;
use SymfonyAdmin\Entity\Base\LogTrait;
use App\Utils\Enum\StatusEnum;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

/**
 * AdminRole
 *
 * @ORM\Table(name="admin_role")
 * @ORM\Entity(repositoryClass="App\Repository\Admin\AdminRoleRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class AdminRole extends BaseEntity
{
    use CommonTrait;
    use LogTrait;
    use SoftDeleteableEntity;

    const ADMIN_ROLE_CODE = 'admin';

    protected $hiddenProperties = ['adminUsers', 'updateTime', 'deletedAt'];

    protected $noUpdateProperties = ['roleId'];

    /**
     * @var string
     *
     * @ORM\Column(name="role_name", type="string", length=64, nullable=false, options={"comment"="用户组名称"})
     */
    private $roleName = '';

    /**
     * @var string
     *
     * @ORM\Column(name="role_code", type="string", length=32, nullable=false, options={"comment"="用户组标识"})
     */
    private $roleCode = '';

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=32, nullable=false, options={"default"="on","comment"="用户组状态on正常 off未生效"})
     */
    private $status = StatusEnum::ON;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $parentId;

    /**
     * @var AdminUser[]
     * @ORM\OneToMany(targetEntity=AdminUser::class, mappedBy="adminRole")
     */
    private $adminUsers;


    private $hasBrother = false;

    private $hasChild = false;

    private $level = 1;

    /**
     * @param bool $isWithPre
     * @return string
     */
    public function getRoleName(bool $isWithPre = false): string
    {
        if ($isWithPre) {
            $preString = '';
            if ($this->getLevel() > 1) {
                $preString = str_pad($this->isHasBrother() ? '├' : '└', $this->getLevel() * 4, ' ', STR_PAD_LEFT);
            }
            $preString = str_replace(' ', '&nbsp;', $preString);

            return $preString . $this->roleName;
        } else {
            return $this->roleName;
        }
    }

    /**
     * @param string $roleName
     */
    public function setRoleName(string $roleName): void
    {
        $this->roleName = $roleName;
    }

    /**
     * @return string
     */
    public function getRoleCode(): string
    {
        return $this->roleCode;
    }

    /**
     * @param string $roleCode
     */
    public function setRoleCode(string $roleCode): void
    {
        $this->roleCode = $roleCode;
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

    /**
     * @return AdminUser[]|AdminUser
     */
    public function getAdminUsers()
    {
        return $this->adminUsers;
    }

    /**
     * @param AdminUser[] $adminUsers
     */
    public function setAdminUsers(array $adminUsers): void
    {
        $this->adminUsers = $adminUsers;
    }

    /**
     * @return int
     */
    public function getParentId(): int
    {
        return $this->parentId;
    }

    /**
     * @param int $parentId
     */
    public function setParentId(int $parentId): void
    {
        $this->parentId = $parentId;
    }

    /**
     * @return bool
     */
    public function isHasBrother(): bool
    {
        return $this->hasBrother;
    }

    /**
     * @param bool $hasBrother
     */
    public function setHasBrother(bool $hasBrother): void
    {
        $this->hasBrother = $hasBrother;
    }

    /**
     * @return bool
     */
    public function isHasChild(): bool
    {
        return $this->hasChild;
    }

    /**
     * @param bool $hasChild
     */
    public function setHasChild(bool $hasChild): void
    {
        $this->hasChild = $hasChild;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    /**
     * @param bool $isWithPre
     * @return array
     */
    public function getApiFormat(bool $isWithPre = false): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getRoleName($isWithPre),
            'createTime' => $this->getCreateTime()->format('Y-m-d H:i:s'),
            'description' => $this->getRoleCode(),
            'status' => $this->getStatus(),
            'sort' => 0,
            'adminCount' => 0,
        ];
    }

}
