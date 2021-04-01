<?php


namespace SymfonyAdmin\Request\CommonTrait;


use SymfonyAdmin\Exception\InvalidParamsException;

trait UserIdTrait
{
    /** @var int */
    protected $userId;

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @throws InvalidParamsException
     */
    public function setUserId(int $userId): void
    {
        if (empty($userId)) {
            throw new InvalidParamsException('获取登录用户信息失败！');
        }
        $this->userId = $userId;
    }

}