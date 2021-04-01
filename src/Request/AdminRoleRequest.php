<?php


namespace SymfonyAdmin\Request;


use SymfonyAdmin\Exception\InvalidParamsException;
use SymfonyAdmin\Request\Base\BaseRequest;
use SymfonyAdmin\Utils\Enum\StatusEnum;

class AdminRoleRequest extends BaseRequest
{
    private $id = 0;

    private $roleName = '';

    private $roleCode = '';

    private $status = StatusEnum::OFF;

    private $parentId = 0;

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
            throw new InvalidParamsException('传入参数错误！角色ID不能为空！');
        }
        $this->id = $id;
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
    public function getRoleName(): string
    {
        return $this->roleName;
    }

    /**
     * @param string $roleName
     * @throws InvalidParamsException
     */
    public function setRoleName(string $roleName): void
    {
        if (empty($roleName)) {
            throw new InvalidParamsException('传入参数错误！角色名称不能为空！');
        }
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
     * @throws InvalidParamsException
     */
    public function setRoleCode(string $roleCode): void
    {
        if (empty($roleCode)) {
            throw new InvalidParamsException('传入参数错误！角色标识不能为空！');
        }
        $this->roleCode = $roleCode;
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
     * @throws InvalidParamsException
     */
    public function setParentId(int $parentId): void
    {
        if (empty($parentId)) {
            throw new InvalidParamsException('传入参数错误！父级角色ID不能为空！');
        }
        $this->parentId = $parentId;
    }


}