<?php

namespace SymfonyAdmin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdminFile
 *
 * @ORM\Table(name="admin_file")
 * @ORM\Entity(repositoryClass="SymfonyAdmin\Repository\AdminFileRepository")
 */
class AdminFile
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true,"comment"="文件id"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file_type", type="string", length=32, nullable=true, options={"comment"="文件类型"})
     */
    private $fileType;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file_path", type="string", length=128, nullable=true, options={"comment"="文件路径"})
     */
    private $filePath;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file_ext", type="string", length=16, nullable=true, options={"comment"="扩展名"})
     */
    private $fileExt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="file_size", type="integer", nullable=true, options={"comment"="文件尺寸"})
     */
    private $fileSize;

    /**
     * @var int|null
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true, options={"comment"="上传用户Id"})
     */
    private $userId;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=64)
     */
    private $fileHash;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="create_time", type="datetime", nullable=true, options={"comment"="上传时间"})
     */
    private $createTime;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true, options={"comment"="删除时间"})
     */
    private $deletedAt;

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
     * @return string|null
     */
    public function getFileType(): ?string
    {
        return $this->fileType;
    }

    /**
     * @param string|null $fileType
     */
    public function setFileType(?string $fileType): void
    {
        $this->fileType = $fileType;
    }

    /**
     * @return string|null
     */
    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    /**
     * @param string|null $filePath
     */
    public function setFilePath(?string $filePath): void
    {
        $this->filePath = $filePath;
    }

    /**
     * @return string|null
     */
    public function getFileExt(): ?string
    {
        return $this->fileExt;
    }

    /**
     * @param string|null $fileExt
     */
    public function setFileExt(?string $fileExt): void
    {
        $this->fileExt = $fileExt;
    }

    /**
     * @return int|null
     */
    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    /**
     * @param int|null $fileSize
     */
    public function setFileSize(?int $fileSize): void
    {
        $this->fileSize = $fileSize;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param int|null $userId
     */
    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreateTime(): ?\DateTime
    {
        return $this->createTime;
    }

    /**
     * @param \DateTime|null $createTime
     */
    public function setCreateTime(?\DateTime $createTime): void
    {
        $this->createTime = $createTime;
    }

    /**
     * @return \DateTime|null
     */
    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTime|null $deletedAt
     */
    public function setDeletedAt(?\DateTime $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return string|null
     */
    public function getFileHash(): ?string
    {
        return $this->fileHash;
    }

    /**
     * @param string|null $fileHash
     */
    public function setFileHash(?string $fileHash): void
    {
        $this->fileHash = $fileHash;
    }

}
