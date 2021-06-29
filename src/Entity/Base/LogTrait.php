<?php

namespace SymfonyAdmin\Entity\Base;


use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\ORMException;
use SymfonyAdmin\Entity\AdminLog;
use Doctrine\ORM\Mapping as ORM;

trait LogTrait
{
    use CommonTrait;

    static $logMessageRules = [];

    private $logMessage = '';

    /**
     * @ORM\PreFlush
     * @param PreFlushEventArgs $args
     * @throws ORMException
     */
    public function addModifyLog(PreFlushEventArgs $args)
    {
        $adminLog = AdminLog::create(self::class, $this->getDataId(), $this->getEntityModifyType(), $this->toArray(false), $this->getLogMessage());
        $entityManager = $args->getEntityManager();
        $entityManager->persist($adminLog);
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
