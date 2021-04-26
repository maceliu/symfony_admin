<?php


namespace SymfonyAdmin\Service\Base;


use DateTime;
use SymfonyAdmin\Entity\Base\BaseEntity;
use SymfonyAdmin\Exception\NotExistException;
use SymfonyAdmin\Request\Base\BaseRequest;
use Doctrine\Persistence\ManagerRegistry;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AdminBaseService extends BaseService
{

    /** @var string */
    protected $entityClass;

    /** @var int */
    protected $id = 0;

    /** @var int */
    protected $pageNum = 1;

    /** @var int */
    protected $pageSize = 10;

    /** @var Request */
    protected $request;

    /** @var BaseEntity */
    protected $entity = null;

    public function __construct(ManagerRegistry $doctrine, RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
        if ($this->request) {
            $this->pageNum = intval($this->request->get('pageNum', 1));
            $this->pageSize = intval($this->request->get('pageSize', 10));
            $this->id = intval($this->request->get('id', 0));
        }

        if (!empty($this->id)) {
            $this->entity = $doctrine->getRepository($this->entityClass)->findOneById($this->id);
        }

        parent::__construct($doctrine);
    }

    /**
     * @return array
     * @throws NotExistException|ReflectionException
     */
    public function getOne(): array
    {
        if (!$this->entity) {
            throw new NotExistException('查询的数据不存在！');
        }
        return $this->entity->toArray();
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public function getList(): array
    {
        # 搜索条件获取
        $searchConditions = [];
        foreach ($this->doctrine->getRepository($this->entityClass)->getSearchMap() as $searchKey => $type) {
            if ($this->request->query->get($searchKey)) {
                $searchConditions[$searchKey] = trim($this->request->query->get($searchKey));
            }
        }

        # 执行分页查询
        $robotPaginator = $this->doctrine->getRepository($this->entityClass)->findAllWithPage($this->pageNum, $this->pageSize, $searchConditions);
        $robotList = [];
        foreach ($robotPaginator->getEntityList() as $entity) {
            /** @var  BaseEntity $entity */
            $robotList[] = $entity->toArray();
        }
        $robotPaginator->setRowsList($robotList);
        return $robotPaginator->toArray();
    }

    /**
     * @param BaseRequest $request
     * @return array
     * @throws ReflectionException
     */
    public function createOrUpdate(BaseRequest $request): array
    {
        if (!$this->entity) {
            $this->entity = new $this->entityClass($request);
        }

        $this->entity->setFields($request);
        $this->doctrine->getManager()->persist($this->entity);
        $this->doctrine->getManager()->flush();

        return $this->entity->toArray();
    }

    /**
     * @return bool
     * @throws NotExistException
     */
    public function deleteOne(): bool
    {
        if (!$this->entity) {
            throw new NotExistException('查询的数据不存在！' . $this->id);
        }

        if (method_exists($this->entity, 'setDeletedAt')) {
            $this->entity->setDeletedAt(new DateTime());
            $this->doctrine->getManager()->persist($this->entity);
        } else {
            $this->doctrine->getManager()->remove($this->entity);
        }
        $this->doctrine->getManager()->flush();
        return true;
    }

}