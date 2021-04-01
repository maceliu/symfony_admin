<?php


namespace SymfonyAdmin\Service;


use SymfonyAdmin\Entity\AdminAuth;
use SymfonyAdmin\Entity\AdminFile;
use SymfonyAdmin\Service\Base\BaseService;
use DateTime;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class AdminFileService extends BaseService
{
    /**
     * @param AdminAuth $adminAuth
     * @param Request $request
     * @return array
     */
    public function upload(AdminAuth $adminAuth, Request $request): array
    {
        $fileType = trim($request->get('fileType', 'default'));
        $basePath = '../public';
        if (!is_dir($basePath . '/uploads/')) {
            mkdir($basePath . '/uploads/');
        }

        $queryFilePath = '/uploads/' . date('Y-m') . '/';
        if (!is_dir($basePath . $queryFilePath)) {
            mkdir($basePath . $queryFilePath);
        }

        $em = $this->doctrine->getManager();
        $fileList = [];
        /** @var UploadedFile $file */
        foreach ($request->files as $file) {
            # 文件大小需要在上传前获取
            $fileSize = $file->getSize();

            # 检查文件是否已经上传过
            $fileHash = hash_file('md5', $file->getFileInfo());
            $oldFile = $this->getAdminFileRepo()->findOneByFileHash($fileHash);
            if ($oldFile) {
                $fileList[] = $oldFile;
                continue;
            }

            # 将文件存储在本地路径
            $explode = explode('.', $file->getClientOriginalName());
            $extName = array_pop($explode) ?? '';
            $uploadFileFullPath = $basePath . $queryFilePath . $fileHash . '.' . $extName;
            move_uploaded_file($file->getFileInfo(), $uploadFileFullPath);

            # 写入数据库记录
            $adminFile = new AdminFile();
            $adminFile->setFilePath($queryFilePath . $fileHash . '.' . $extName);
            $adminFile->setFileSize($fileSize);
            $adminFile->setFileHash($fileHash);
            $adminFile->setFileType($fileType);
            $adminFile->setUserId($adminAuth->getAdminUser()->getId());
            $adminFile->setFileExt($extName);
            $adminFile->setCreateTime(new DateTime());

            $fileList[] = $adminFile;
            $em->persist($adminFile);
        }

        $em->flush();

        # 返回数据格式化展示
        $r = [];
        foreach ($fileList as $file) {
            $r[] = [
                'id' => $file->getId(),
                'filePath' => $request->getScheme() . '://' . $request->getHost() . $file->getFilePath(),
            ];
        }

        return $r;
    }

}
