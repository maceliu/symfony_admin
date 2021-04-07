<?php

namespace SymfonyAdmin\Entity\Base;


use SymfonyAdmin\Entity\AdminLog;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use SymfonyAdmin\Entity\AdminUser;

trait LogTrait
{
    static $logMessageRules = [];

    public $logMessage = '';

    private $operateType = '';

    /**
     * @ORM\PostPersist
     * @ORM\PostUpdate
     * @param LifecycleEventArgs $args
     */
    public function addModifyLog(LifecycleEventArgs $args)
    {
        if ($args->getObject() instanceof AdminUser) {
            /** @var AdminUser $adminUser */
            $adminUser = $args->getObject();
            # 登录场景
            if (!empty($adminUser->getLoginTime()) && $adminUser->getLoginTime()->getTimestamp() == $adminUser->getUpdateTime()->getTimestamp()) {
                $this->setLogMessage('登录系统');
                $this->operateType = 'login';
            }
        }

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
        # 创建时间和更新时间是否一致 一致为创建  不一致为更新
        if (!empty($this->operateType)) {
            return $this->operateType;
        } elseif ($this->getCreateTime()->getTimestamp() == $this->getUpdateTime()->getTimestamp()) {
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

    /**
     * @param string $logMessage
     */
    public function setLogMessage(string $logMessage): void
    {
        $this->logMessage = $logMessage;
    }

    protected function getLogMessage(): string
    {
        $methodName = empty(self::$logMessageRules[self::class]) ? '' : self::$logMessageRules[self::class];
        if ($methodName && method_exists($this, $methodName)) {
            return $this->logMessage . $methodName . ' : ' . $this->$methodName();
        } else {
            return $this->logMessage;
        }
    }

}
