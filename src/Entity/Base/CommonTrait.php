<?php


namespace SymfonyAdmin\Entity\Base;


use DateTime;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use SymfonyAdmin\Utils\Enum\OperateEnum;

trait CommonTrait
{
    /** @var string */
    private $operateStatus = OperateEnum::NONE;

    /**
     * @return string
     */
    public function getOperateStatus(): string
    {
        return $this->operateStatus;
    }

    /**
     * @param string $operateStatus
     */
    public function setOperateStatus(string $operateStatus): void
    {
        $this->operateStatus = $operateStatus;
    }

    /**
     * @param PreUpdateEventArgs $event
     * @ORM\PreUpdate
     */
    public function preUpdate(PreUpdateEventArgs $event)
    {
        $this->setUpdateTime(new DateTime());
        if (!empty($event->getEntityChangeSet())) {
            $this->operateStatus = OperateEnum::UPDATE;
        }
    }

    /**
     * @ORM\PostPersist
     */
    public function postPersist()
    {
        if ($this->getCreateTime()->getTimestamp() == $this->getUpdateTime()->getTimestamp()) {
            $this->operateStatus = OperateEnum::CREATE;
        }
    }
}
