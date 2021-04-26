<?php


namespace SymfonyAdmin\Repository;


use SymfonyAdmin\Entity\AdminFile;
use SymfonyAdmin\Repository\Base\BaseRepository;

class AdminFileRepository extends BaseRepository
{
    protected $entityClass = AdminFile::class;

    /**
     * @param string $fileHash
     * @return object|AdminFile
     */
    public function findOneByFileHash(string $fileHash): ?AdminFile
    {
        return $this->findOneBy(['fileHash' => $fileHash]);
    }

}
