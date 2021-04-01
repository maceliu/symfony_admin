<?php


namespace SymfonyAdmin\Utils\Enum\Menu;


class MenuTypeEnum
{
    const MENU = 'menu';

    const NODE = 'node';

    static $menuTypeDict = [
        MenuTypeEnum::MENU => '前端页面',
        MenuTypeEnum::NODE => '接口',
    ];
}
