<?php


namespace SymfonyAdmin\Service\Base;


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
    protected $entity;

    /** @var int */
    protected $id = 0;

    /** @var int */
    protected $pageNum = 1;

    /** @var int */
    protected $pageSize = 10;

    /** @var Request */
    protected $request;

    public function __construct(ManagerRegistry $doctrine, RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->pageNum = intval($this->request->get('pageNum', 1));
        $this->pageSize = intval($this->request->get('pageSize', 10));
        $this->id = intval($this->request->get('id', 0));
        parent::__construct($doctrine);
    }

    /**
     * @return array
     * @throws NotExistException|ReflectionException
     */
    public function getOne(): array
    {
        /** @var BaseEntity $entity */
        $entity = $this->doctrine->getRepository($this->entity)->findOneById($this->id);
        if (!$entity) {
            throw new NotExistException('查询的数据不存在！');
        }
        return $entity->toArray();
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public function getList(): array
    {
        $robotPaginator = $this->doctrine->getRepository($this->entity)->findAllWithPage($this->pageNum, $this->pageSize);
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
        /** @var BaseEntity $entity */
        $entity = null;
        if (!empty($this->id)) {
            $entity = $this->doctrine->getRepository($this->entity)->findOneById($this->id);
        }

        if (!$entity) {
            $entity = new $this->entity($request);
        }

        $entity->createOrUpdate($request);
        $this->doctrine->getManager()->persist($entity);
        $this->doctrine->getManager()->flush();

        return $entity->toArray();
    }

}