<?php


namespace SymfonyAdmin\Request;

use SymfonyAdmin\Exception\InvalidParamsException;
use SymfonyAdmin\Request\Base\BaseRequest;
use App\Utils\CommonUtils;
use App\Utils\Enum\StatusEnum;

class AdminUserRequest extends BaseRequest
{

    /** @var int $id */
    protected $id = 0;

    /** @var string $username */
    protected $username = '';

    /** @var string $password */
    protected $password = '';

    /** @var string $email */
    protected $email = '';

    /** @var int $roleId */
    protected $roleId = 0;

    /** @var string $avatar */
    protected $avatar = '';

    /** @var string $trueName */
    private $trueName = '';

    private $status = StatusEnum::OFF;

    /** @var string */
    private $remark = '';

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @throws InvalidParamsException
     */
    public function setId(int $id): void
    {
        if (empty($id)) {
            throw new InvalidParamsException('传入参数错！用户ID不能为空！');
        }
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @throws InvalidParamsException
     */
    public function setEmail(string $email): void
    {
        if (!CommonUtils::checkIsEmail($email)) {
            throw new InvalidParamsException('传入参数错！邮箱格式错误！' . $email);
        }
        $this->email = $email;
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
     * @return string
     */
    public function getAvatar(): string
    {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     */
    public function setAvatar(string $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @throws InvalidParamsException
     */
    public function setUsername(string $username): void
    {
        if (empty($username) || !preg_match('/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u', $username)) {
            throw new InvalidParamsException('传入参数错误！用户名由2-16位数字或字母、汉字、下划线组成！');
        }
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @throws InvalidParamsException
     */
    public function setPassword(string $password): void
    {
        if (empty($password) || !preg_match("/^[0-9a-zA-Z]{6,16}$/i", $password)) {
            throw new InvalidParamsException('传入参数错误！密码必须由6-16位数字字母组成！');
        }
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getTrueName(): string
    {
        return $this->trueName;
    }

    /**
     * @param string $trueName
     */
    public function setTrueName(string $trueName): void
    {
        $this->trueName = $trueName;
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
     * @throws InvalidParamsException
     */
    public function setStatus(string $status): void
    {
        if (!in_array($status, [StatusEnum::ON, StatusEnum::OFF])) {
            throw new InvalidParamsException('传入参数错误！菜单状态字段错误！');
        }
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getRemark(): ?string
    {
        return $this->remark ?? '';
    }

    /**
     * @param string|null $remark
     */
    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }


}
