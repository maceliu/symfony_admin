<?php

namespace SymfonyAdmin\Entity;

use SymfonyAdmin\Entity\Base\BaseEntity;
use SymfonyAdmin\Entity\Base\CommonTrait;
use SymfonyAdmin\Entity\Base\LogTrait;
use SymfonyAdmin\Exception\NotExistException;
use SymfonyAdmin\Repository\AdminMenuRepository;
use App\Utils\Enum\Menu\MenuTypeEnum;
use App\Utils\Enum\StatusEnum;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\ORMException;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use ReflectionException;

/**
 * AdminMenu
 *
 * @ORM\Table(name="admin_menu", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"path"})}, indexes={@ORM\Index(name="weigh", columns={"weight"})})
 * @ORM\Entity(repositoryClass="App\Repository\Admin\AdminMenuRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class AdminMenu extends BaseEntity
{
    use CommonTrait;
    use LogTrait;
    use SoftDeleteableEntity;

    protected $hiddenProperties = ['isPublic', 'updateTime', 'createTime'];

    /**
     * @var string|null
     *
     * @ORM\Column(name="icon", type="string", length=50, nullable=true, options={"comment"="图标"})
     */
    private $icon = '';

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=100, nullable=false, options={"comment"="菜单路径"})
     */
    private $path = '';

    /**
     * @var string
     *
     * @ORM\Column(name="menu_name", type="string", length=50, nullable=false, options={"comment"="菜单名称"})
     */
    private $menuName = '';

    /**
     * @var int
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=false, options={"unsigned"=true,"comment"="父ID"})
     */
    private $parentId = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="level", type="integer", nullable=false, options={"comment"="菜单级别"})
     */
    private $level = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="weight", type="integer", nullable=false, options={"comment"="权重"})
     */
    private $weight = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=0, nullable=false, options={"default"="node","comment"="menu为菜单,file为权限节点"})
     */
    private $type = MenuTypeEnum::MENU;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=30, nullable=false, options={"default"="on","comment"="状态"})
     */
    private $status = StatusEnum::ON;

    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="string", length=255, nullable=false, options={"comment"="备注"})
     */
    private $remark = '';

    /**
     * @var int
     *
     * @ORM\Column(name="is_public", type="integer", nullable=false)
     */
    private $isPublic = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="is_hidden", type="integer", nullable=false)
     */
    private $isHidden = 0;

    private $hasBrother = false;

    private $hasChild = false;

    /**
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @param string|null $icon
     */
    public function setIcon(?string $icon): void
    {
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @param bool $isWithPre
     * @return string
     */
    public function getMenuName(bool $isWithPre = false): string
    {
        if ($isWithPre) {
            $preString = '';
            if ($this->getLevel() > 1) {
                $preString = str_pad($this->isHasBrother() ? '├' : '└', $this->getLevel() * 4, ' ', STR_PAD_LEFT);
            }
            $preString = str_replace(' ', '&nbsp;', $preString);

            return $preString . $this->menuName;
        } else {
            return $this->menuName;
        }
    }

    /**
     * @param string $menuName
     */
    public function setMenuName(string $menuName): void
    {
        $this->menuName = $menuName;
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
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level)
    {
        $this->level = $level;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight(int $weight): void
    {
        $this->weight = $weight;
    }

    /**
     * @param bool $isTrans
     * @return string
     */
    public function getType(bool $isTrans = false): string
    {
        if ($isTrans) {
            return MenuTypeEnum::$menuTypeDict[$this->type];
        }
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
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
     * @return string
     */
    public function getRemark(): string
    {
        return $this->remark;
    }

    /**
     * @param string $remark
     */
    public function setRemark(string $remark): void
    {
        $this->remark = $remark;
    }

    /**
     * @return int
     */
    public function getIsPublic(): int
    {
        return $this->isPublic;
    }

    /**
     * @param int $isPublic
     */
    public function setIsPublic(int $isPublic): void
    {
        $this->isPublic = $isPublic;
    }

    /**
     * @return int
     */
    public function getIsHidden(): int
    {
        return $this->isHidden;
    }

    /**
     * @param int $isHidden
     */
    public function setIsHidden(int $isHidden): void
    {
        $this->isHidden = $isHidden;
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
     * @param bool $isFormatName
     * @param bool $isFormatType
     * @return array
     * @throws ReflectionException
     */
    public function getApiFormat(bool $isFormatName = false, bool $isFormatType = false): array
    {
        $r = $this->toArray();
        $r['menuName'] = $this->getMenuName($isFormatName);
        $r['type'] = $this->getType($isFormatType);
        return $r;
    }

    /**
     * @ORM\PreUpdate
     * @param PreUpdateEventArgs $eventArgs
     * @throws ORMException|NotExistException
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();

        /** @var AdminMenuRepository $adminMenuRepo */
        $adminMenuRepo = $em->getRepository(AdminMenu::class);

        # 自动根据parentId更正level等级
        if ($eventArgs->hasChangedField('parentId')) {
            if ($this->getParentId() == 0) {
                $this->setLevel(1);
            } else {
                $parentMenu = $adminMenuRepo->findOneById($this->getParentId());
                if (!$parentMenu) {
                    throw new NotExistException('未查询到父级菜单详情！');
                }
                $this->setLevel($parentMenu->getLevel() + 1);
            }
            $this->updateChildLevel($em);
            $em->persist($this);
        }

        # 自动更正子菜单级别
        if ($eventArgs->hasChangedField('level')) {
            $this->updateChildLevel($em);
        }
    }

    /**
     * @ORM\PrePersist
     * @param LifecycleEventArgs $eventArgs
     * @throws NotExistException
     */
    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        /** @var AdminMenuRepository $adminMenuRepo */
        $adminMenuRepo = $em->getRepository(AdminMenu::class);
        if ($this->getParentId() == 0) {
            $this->setLevel(1);
        } else {
            $parentMenu = $adminMenuRepo->findOneById($this->getParentId());
            if (!$parentMenu) {
                throw new NotExistException('未查询到父级菜单详情！');
            }
            $this->setLevel($parentMenu->getLevel() + 1);
        }
    }

    /**
     * @param EntityManager $em
     * @throws ORMException
     */
    private function updateChildLevel(EntityManager $em)
    {
        /** @var AdminMenuRepository $adminMenuRepo */
        $adminMenuRepo = $em->getRepository(AdminMenu::class);
        $childMenuList = $adminMenuRepo->findAllByParentId($this->getId());

        $childLevel = $this->getLevel() + 1;
        foreach ($childMenuList as $menu) {
            $menu->setLevel($childLevel);
            $em->persist($menu);
        }
    }

}
