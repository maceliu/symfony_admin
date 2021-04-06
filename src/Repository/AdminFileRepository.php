<?php


namespace SymfonyAdmin\Repository;


use SymfonyAdmin\Entity\AdminFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AdminFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminFile::class);
    }

    /**
     * @param string $fileHash
     * @return object|AdminFile
     */
    public function findOneByFileHash(string $fileHash): ?AdminFile
    {
        return $this->findOneBy(['fileHash' => $fileHash]);
    }

    /**
     * @param int $id
     * @return object|AdminFile
     */
    public function findOneByFileId(int $id): ?AdminFile
    {
        return $this->findOneBy(['id' => $id]);
    }

}
