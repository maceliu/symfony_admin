<?php


namespace SymfonyAdmin\Utils;


use SymfonyAdmin\Entity\AdminMenu;
use ReflectionException;

class MenuFormat
{
    private $menuFormat = [];

    private $menuLevelList;

    /**
     * MenuFormat constructor.
     * @param $menuLevelList
     * @throws ReflectionException
     */
    public function __construct($menuLevelList)
    {
        $this->menuLevelList = $menuLevelList;
        $this->getNextLevel();
    }

    /**
     * @param int $parentId
     * @param int $level
     * @return bool
     * @throws ReflectionException
     */
    public function getNextLevel($parentId = 0, $level = 1): bool
    {
        if (empty($this->menuLevelList[$level][$parentId])) {
            return false;
        }

        $count = count($this->menuLevelList[$level][$parentId]);
        $countTemp = 0;
        /** @var AdminMenu $parentMenu */
        foreach ($this->menuLevelList[$level][$parentId] as $parentMenu) {
            $hasBrother = true;
            $countTemp++;

            # 先占位
            $this->menuFormat[] = $parentMenu;
            $keyTemp = array_key_last($this->menuFormat);

            # 获取下级菜单列表
            $nextLevel = $level + 1;
            $hasChild = $this->getNextLevel($parentMenu->getId(), $nextLevel);
            $parentMenu->setHasChild($hasChild);

            # 设置是否有同辈后续菜单
            if ($countTemp >= $count) {
                $hasBrother = false;
            }
            $parentMenu->setHasBrother($hasBrother);

            # 菜单数据格式化
            $this->menuFormat[$keyTemp] = $parentMenu->getApiFormat(true, true);
        }

        return true;
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        return $this->menuFormat;
    }
}