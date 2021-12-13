<?php

namespace SymfonyAdmin\Entity\Base;


use Doctrine\ORM\Event\LifecycleEventArgs;
use ReflectionException;
use SymfonyAdmin\Entity\AdminLog;
use Doctrine\ORM\Mapping as ORM;
use SymfonyAdmin\Entity\AdminUser;
use SymfonyAdmin\Utils\Enum\EntityOperateTypeEnum;

trait LogTrait
{
    use CommonTrait;

    static $logMessageRules = [];

    private $logMessage = '';

    /**
     * @ORM\PostUpdate
     * @ORM\PostPersist
     * @param LifecycleEventArgs $args
     * @throws ReflectionException
     */
    public function addModifyLog(LifecycleEventArgs $args)
    {
        if ($this->getCreateTime()->getTimestamp() == $this->getUpdateTime()->getTimestamp()) {
            $this->entityModifyType = EntityOperateTypeEnum::CREATE;
        }

        if ($args->getObject() instanceof AdminUser) {
            /** @var AdminUser $adminUser */
            $adminUser = $args->getObject();
            # 登录场景
            if (!empty($adminUser->getLoginTime()) && $adminUser->getLoginTime()->getTimestamp() == $adminUser->getUpdateTime()->getTimestamp()) {
                $this->setLogMessage('登录系统');
                $this->entityModifyType = 'login';
            }
        }

        $adminLog = AdminLog::create(self::class, $this->getDataId(), $this->getEntityModifyType(), $this->toArray(false), $this->getLogMessage());
        $entityManager = $args->getObjectManager();
        $entityManager->persist($adminLog);
        $entityManager->flush();
    }

    /**
     * @return int
     */
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

    /**
     * @return string
     */
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
