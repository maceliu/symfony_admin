<?php


namespace SymfonyAdmin\Utils;


use SymfonyAdmin\Entity\Base\BaseEntity;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PaginatorResult
{
    /** @var int */
    private $pageNum = 1;

    /** @var int */
    private $rowsTotal = 0;

    /** @var int */
    private $pageSize = 10;

    /** @var int */
    private $totalPage = 0;

    /** @var BaseEntity[] */
    private $entityList = [];

    /** @var array */
    private $rowsList = [];

    /**
     * PaginatorResult constructor.
     * @param Paginator|null $paginator
     * @param int $pageNum
     * @param int $pageSize
     */
    public function __construct(Paginator $paginator, int $pageNum = 1, int $pageSize = 10)
    {
        $paginator->getQuery()
            ->setFirstResult($pageSize * ($pageNum - 1)) // Offset
            ->setMaxResults($pageSize); // Limit

        if ($paginator) {
            $this->setRowsTotal($paginator->count());
            $this->setTotalPage(ceil($paginator->count() / $pageSize));
            $this->setPageSize($pageSize);
            $this->setPageNum($pageNum);
            $this->setEntityList($paginator->getQuery()->getResult());
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'total' => $this->getRowsTotal(),
            'pageSize' => $this->getPageSize(),
            'totalPage' => $this->getTotalPage(),
            'pageNum' => $this->getPageNum(),
            'list' => $this->getRowsList(),
        ];
    }

    /**
     * @return int
     */
    public function getRowsTotal(): int
    {
        return $this->rowsTotal;
    }

    /**
     * @param int $rowsTotal
     */
    public function setRowsTotal(int $rowsTotal): void
    {
        $this->rowsTotal = $rowsTotal;
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
     */
    public function setPageSize(int $pageSize): void
    {
        $this->pageSize = $pageSize;
    }

    /**
     * @return int
     */
    public function getTotalPage(): int
    {
        return $this->totalPage;
    }

    /**
     * @param int $totalPage
     */
    public function setTotalPage(int $totalPage): void
    {
        $this->totalPage = $totalPage;
    }

    /**
     * @return array
     */
    public function getEntityList(): array
    {
        return $this->entityList;
    }

    /**
     * @param array $entityList
     */
    public function setEntityList(array $entityList): void
    {
        $this->entityList = $entityList;
    }

    /**
     * @return array
     */
    public function getRowsList(): array
    {
        return $this->rowsList;
    }

    /**
     * @param array $rowsList
     */
    public function setRowsList(array $rowsList): void
    {
        $this->rowsList = $rowsList;
    }

    /**
     * @return int
     */
    public function getPageNum(): int
    {
        return $this->pageNum;
    }

    /**
     * @param int $pageNum
     */
    public function setPageNum(int $pageNum): void
    {
        $this->pageNum = $pageNum;
    }
}
