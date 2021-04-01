<?php


namespace SymfonyAdmin\Request\CommonTrait;


use SymfonyAdmin\Exception\InvalidParamsException;

trait PageTrait
{
    /** @var int */
    protected $pageNum;

    /** @var int */
    protected $pageSize;

    /**
     * @return int
     */
    public function getPageNum(): int
    {
        return $this->pageNum;
    }

    /**
     * @param int $pageNum
     * @throws InvalidParamsException
     */
    public function setPageNum(int $pageNum): void
    {
        if (empty($pageNum)) {
            throw new InvalidParamsException('页数错误');
        }
        $this->pageNum = $pageNum;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @param int $pageSize
     * @throws InvalidParamsException
     */
    public function setPageSize(int $pageSize): void
    {
        if (empty($pageSize)) {
            throw new InvalidParamsException('每页数量错误');
        }
        $this->pageSize = $pageSize;
    }
}