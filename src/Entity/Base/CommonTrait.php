<?php


namespace SymfonyAdmin\Entity\Base;


use SymfonyAdmin\Request\Base\BaseRequest;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait CommonTrait
{
    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function postUpdate()
    {
        $this->setUpdateTime(new DateTime());
    }
}
