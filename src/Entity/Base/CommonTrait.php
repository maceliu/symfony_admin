<?php


namespace SymfonyAdmin\Entity\Base;


use DateTime;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use SymfonyAdmin\Utils\Enum\EntityOperateTypeEnum;

trait CommonTrait
{
    /** @var string */
    private $entityModifyType = EntityOperateTypeEnum::NONE;

    /**
     * @return string
     */
    public function getEntityModifyType(): string
    {
        return $this->entityModifyType;
    }

    /**
     * @param string $entityModifyType
     */
    public function setEntityModifyType(string $entityModifyType): void
    {
        $this->entityModifyType = $entityModifyType;
    }

    /**
     * @param PreUpdateEventArgs $event
     * @ORM\PreUpdate
     */
    public function preUpdate(PreUpdateEventArgs $event)
    {
        $this->setUpdateTime(new DateTime());
        if (!empty($event->getEntityChangeSet())) {
            $this->entityModifyType = EntityOperateTypeEnum::UPDATE;
        }
    }

    /**
     * @ORM\PostPersist
     */
    public function postPersist()
    {
        if ($this->getCreateTime()->getTimestamp() == $this->getUpdateTime()->getTimestamp()) {
            $this->entityModifyType = EntityOperateTypeEnum::CREATE;
        }
    }

    /**
     * @ORM\PreRemove
     */
    public function preRemove()
    {
        $this->entityModifyType = EntityOperateTypeEnum::DELETE;
    }
}
