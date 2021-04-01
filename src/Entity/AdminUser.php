<?php


namespace SymfonyAdmin\Entity;


use SymfonyAdmin\Entity\Base\BaseEntity;
use SymfonyAdmin\Entity\Base\CommonTrait;
use SymfonyAdmin\Entity\Base\LogTrait;
use SymfonyAdmin\Service\AdminUserService;
use SymfonyAdmin\Utils\Enum\StatusEnum;
use DateTime;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\ORMException;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

/**
 * Goods
 *
 * @ORM\Table(name="admin_user")
 * @ORM\Entity(repositoryClass="SymfonyAdmin\Repository\AdminUserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class AdminUser extends BaseEntity
{
    use CommonTrait;
    use LogTrait;
    use SoftDeleteableEntity;

    protected $hiddenProperties = ['password', 'updateTime', 'passwordTime', 'adminRole', 'deletedAt'];

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $username;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string")
     */
    private $trueName;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string")
     */
    private $mobile;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $avatar = null;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $roleId;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $passwordTime;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $loginTime;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=32, nullable=false, options={"default"="on","comment"="用户组状态on正常 off未生效"})
     */
    private $status = StatusEnum::ON;

    /** @var string */
    private $accessToken = '';

    /**
     * @var AdminRole
     * A
     * @ORM\ManyToOne(targetEntity=AdminRole::class, inversedBy="adminUsers")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     */
    protected $adminRole;

    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="string")
     */
    protected $remark = '';

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getTrueName(): ?string
    {
        return $this->trueName;
    }

    /**
     * @param string|null $trueName
     */
    public function setTrueName(?string $trueName): void
    {
        $this->trueName = $trueName;
    }

    /**
     * @return string|null
     */
    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    /**
     * @param string|null $mobile
     */
    public function setMobile(?string $mobile): void
    {
        $this->mobile = $mobile;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * @param string|null $avatar
     */
    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @return DateTime
     */
    public function getPasswordTime(): ?DateTime
    {
        return $this->passwordTime;
    }

    /**
     * @param DateTime $passwordTime
     */
    public function setPasswordTime(DateTime $passwordTime): void
    {
        $this->passwordTime = $passwordTime;
    }

    /**
     * @return DateTime
     */
    public function getLoginTime(): ?DateTime
    {
        return $this->loginTime;
    }

    /**
     * @param DateTime $loginTime
     */
    public function setLoginTime(DateTime $loginTime): void
    {
        $this->loginTime = $loginTime;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

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
     * @return AdminRole
     */
    public function getAdminRole(): ?AdminRole
    {
        return $this->adminRole;
    }

    /**
     * @param AdminRole $adminRole
     */
    public function setAdminRole(AdminRole $adminRole): void
    {
        $this->adminRole = $adminRole;
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
    public function getRemark(): ?string
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
     * @ORM\PreUpdate
     * @param PreUpdateEventArgs $eventArgs
     * @throws ORMException
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        # 自动生成用户密码
        if ($eventArgs->hasChangedField('username') || $eventArgs->hasChangedField('password')) {
            $this->setPassword(AdminUserService::makeUserPassword($this->password, $this->username));
            $em = $eventArgs->getEntityManager();
            $em->persist($this);
        }
    }
}
