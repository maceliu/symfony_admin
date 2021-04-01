<?php


namespace SymfonyAdmin\Service\Base;


use SymfonyAdmin\Entity\AdminAuth;
use SymfonyAdmin\Entity\Base\BaseEntity;
use ReflectionException;

trait CurdTrait
{

    /**
     * @param AdminAuth $adminAuth
     * @param $request
     * @return array
     * @throws ReflectionException
     */
    public function getOne(AdminAuth $adminAuth, $request): array
    {
        /** @var BaseEntity $entity */
        $entity = $this->getChildOne($adminAuth, $request);
        return $entity->toArray();
    }

    /**
     * @param AdminAuth $adminAuth
     * @param $request
     * @param array $data
     * @return array
     * @throws ReflectionException
     */
    public function update(AdminAuth $adminAuth, $request, array $data): array
    {
        /** @var BaseEntity $entity */
        $entity = $this->getChildOne($adminAuth, $request);

        $entity->updateFromRequest($data, $request);
        $em = $this->doctrine->getManager();
        $em->persist($entity);
        $em->flush();

        return $entity->toArray();
    }

    /**
     * @param AdminAuth $adminAuth
     * @param $request
     * @return string
     */
    public function delete(AdminAuth $adminAuth, $request): string
    {
        /** @var BaseEntity $entity */
        $entity = $this->getChildOne($adminAuth, $request);

        $em = $this->doctrine->getManager();
        $em->remove($entity);
        $em->flush();

        return 'success';
    }

}