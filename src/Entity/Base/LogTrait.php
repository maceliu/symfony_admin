<?php

namespace SymfonyAdmin\Entity\Base;


use SymfonyAdmin\Entity\AdminLog;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;

trait LogTrait
{
    static $logMessageRules = [];

    /**
     * @ORM\PostPersist
     * @ORM\PostUpdate
     * @param LifecycleEventArgs $args
     */
    public function addModifyLog(LifecycleEventArgs $args)
    {
        $adminLog = AdminLog::create(self::class, $this->getDataId(), $this->getOperateType(), $this->toArray(false), $this->getLogMessage());
        $entityManager = $args->getObjectManager();
        $entityManager->persist($adminLog);
        $entityManager->flush();
    }

    /**
     * @ORM\PreRemove
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $adminLog = AdminLog::create(self::class, $this->getDataId(), 'delete', $this->toArray(false), $this->getLogMessage());
        $entityManager = $args->getObjectManager();
        $entityManager->persist($adminLog);
        $entityManager->flush();
    }

    protected function getOperateType(): string
    {
        //todo 登录时更新用户最后登录时间也记录了日志，需要优化
        # 创建时间和更新时间是否一致 一致为创建  不一致为更新
        if ($this->getCreateTime()->getTimestamp() == $this->getUpdateTime()->getTimestamp()) {
            return 'create';
        } else {
            return 'update';
        }
    }

    protected function getDataId(): int
    {
        if (method_exists($this, 'getId')) {
            $methodName = 'getId';
        } else {
            return 0;
        }

        return $this->$methodName();
    }

    protected function getLogMessage(): string
    {
        $logMessage = '';
        $methodName = empty(self::$logMessageRules[self::class]) ? '' : self::$logMessageRules[self::class];
        if ($methodName && method_exists($this, $methodName)) {
            return $logMessage . $methodName . ' : ' . $this->$methodName();
        } else {
            return $logMessage;
        }
    }

}
