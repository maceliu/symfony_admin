<?php


namespace SymfonyAdmin\Request;


use SymfonyAdmin\Exception\InvalidParamsException;
use SymfonyAdmin\Request\Base\BaseRequest;
use SymfonyAdmin\Utils\Enum\Menu\MenuTypeEnum;
use SymfonyAdmin\Utils\Enum\StatusEnum;

class AdminMenuRequest extends BaseRequest
{
    private $id = 0;

    private $path = '';

    private $parentId = 0;

    private $menuName = '';

    private $status = StatusEnum::OFF;

    private $type = MenuTypeEnum::MENU;

    private $icon = '';

    private $weight = 0;

    private $isHidden = 0;

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
            throw new InvalidParamsException('传入参数错误！菜单ID不能为空！');
        }
        $this->id = $id;
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
     * @throws InvalidParamsException
     */
    public function setPath(string $path): void
    {
        if (empty($path)) {
            throw new InvalidParamsException('传入参数错误！菜单路径不能为空！');
        }
        $this->path = $path;
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
        if (!is_numeric($parentId)) {
            throw new InvalidParamsException('传入参数错误！父级菜单id必须为数字！');
        }
        if (!empty($this->getId()) && $parentId == $this->getId()) {
            throw new InvalidParamsException('父级ID不能为自身ID');
        }

        $this->parentId = $parentId;
    }

    /**
     * @return string
     */
    public function getMenuName(): string
    {
        return $this->menuName;
    }

    /**
     * @param string $menuName
     * @throws InvalidParamsException
     */
    public function setMenuName(string $menuName): void
    {
        if (empty($menuName)) {
            throw new InvalidParamsException('传入参数错误！菜单名称不能为空！');
        }
        $this->menuName = $menuName;
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @throws InvalidParamsException
     */
    public function setType(string $type): void
    {
        if (!in_array($type, [MenuTypeEnum::MENU, MenuTypeEnum::NODE])) {
            throw new InvalidParamsException('传入参数错误！菜单类型字段错误！');
        }
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @throws InvalidParamsException
     */
    public function setIcon(string $icon): void
    {
        if (empty($icon)) {
            throw new InvalidParamsException('传入参数错误！菜单图标不能为空！');
        }
        $this->icon = $icon;
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
     * @throws InvalidParamsException
     */
    public function setWeight(int $weight): void
    {
        if (!is_numeric($weight)) {
            throw new InvalidParamsException('传入参数错误！菜单权重值必须为数字！');
        }
        $this->weight = $weight;
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
     * @throws InvalidParamsException
     */
    public function setIsHidden(int $isHidden): void
    {
        if (!in_array($isHidden, [0, 1])) {
            throw new InvalidParamsException('传入参数错误！是否隐藏字段格式错误！');
        }
        $this->isHidden = $isHidden;
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



}