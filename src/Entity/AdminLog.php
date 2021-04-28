<?php

namespace SymfonyAdmin\Entity;

use SymfonyAdmin\Service\AdminAuthService;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * AdminLog
 *
 * @ORM\Table(name="admin_log", indexes={@ORM\Index(name="idx_userid", columns={"user_id"})})
 * @ORM\Entity
 */
class AdminLog
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true,"comment"="ID"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="data_type", type="string", length=64, nullable=false)
     */
    private $dataType = '';

    /**
     * @var int
     *
     * @ORM\Column(name="data_id", type="integer", nullable=false)
     */
    private $dataId;

    /**
     * @var string
     *
     * @ORM\Column(name="operate_type", type="string", length=32, nullable=false)
     */
    private $operateType = '';

    /**
     * @var string
     *
     * @ORM\Column(name="log_message", type="string", length=64, nullable=false, options={"comment"="内容"})
     */
    private $logMessage = '';

    /**
     * @var string
     *
     * @ORM\Column(name="log_data", type="text", length=65535, nullable=false, options={"comment"="IP"})
     */
    private $logData;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false, options={"unsigned"=true,"comment"="管理员ID"})
     */
    private $userId = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="request_url", type="string", length=128, nullable=false)
     */
    private $requestUrl = '';

    /**
     * @var string
     *
     * @ORM\Column(name="client_ip", type="string", length=64, nullable=true)
     */
    private $clientIp = '';

    /**
     * @var DateTime
     *
     * @ORM\Column(name="create_time", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP","comment"="操作时间"})
     */
    private $createTime = 'CURRENT_TIMESTAMP';

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDataType(): string
    {
        return $this->dataType;
    }

    /**
     * @param string $dataType
     */
    public function setDataType(string $dataType): void
    {
        $this->dataType = $dataType;
    }

    /**
     * @return int
     */
    public function getDataId(): int
    {
        return $this->dataId;
    }

    /**
     * @param int $dataId
     */
    public function setDataId(int $dataId): void
    {
        $this->dataId = $dataId;
    }

    /**
     * @return string
     */
    public function getOperateType(): string
    {
        return $this->operateType;
    }

    /**
     * @param string $operateType
     */
    public function setOperateType(string $operateType): void
    {
        $this->operateType = $operateType;
    }

    /**
     * @return string
     */
    public function getLogMessage(): string
    {
        return $this->logMessage;
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
    public function getLogData(): string
    {
        return $this->logData;
    }

    /**
     * @param string $logData
     */
    public function setLogData(string $logData): void
    {
        $this->logData = $logData;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getRequestUrl(): string
    {
        return $this->requestUrl;
    }

    /**
     * @param string $requestUrl
     */
    public function setRequestUrl(string $requestUrl): void
    {
        $this->requestUrl = $requestUrl;
    }

    /**
     * @return DateTime
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * @param DateTime $createTime
     */
    public function setCreateTime(DateTime $createTime): void
    {
        $this->createTime = $createTime;
    }

    /**
     * @return string
     */
    public function getClientIp(): string
    {
        return $this->clientIp;
    }

    /**
     * @param string $clientIp
     */
    public function setClientIp(string $clientIp): void
    {
        $this->clientIp = $clientIp;
    }

    /**
     * @param $dataType
     * @param $dataId
     * @param $operateType
     * @param $logData
     * @param string $logMessage
     * @return AdminLog
     */
    public static function create($dataType, $dataId, $operateType, $logData, $logMessage = ''): AdminLog
    {
        $adminLog = new self();
        $adminLog->setCreateTime(new DateTime());
        $adminLog->setDataId($dataId);
        $adminLog->setDataType($dataType);
        $adminLog->setOperateType($operateType);
        $adminLog->setLogData(json_encode($logData));
        $adminLog->setLogMessage($logMessage);
        $adminLog->setUserId(intval(AdminAuthService::$loginUserId));
        $adminLog->setRequestUrl(($_SERVER['SERVER_NAME'] ?? '') . ($_SERVER['REQUEST_URI'] ?? ''));
        $adminLog->setClientIp($_SERVER['REMOTE_ADDR'] ?? '');
        return $adminLog;
    }
}
