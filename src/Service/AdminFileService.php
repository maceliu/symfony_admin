<?php


namespace SymfonyAdmin\Service;


use OSS\Core\OssException;
use SymfonyAdmin\Entity\AdminAuth;
use SymfonyAdmin\Entity\AdminFile;
use SymfonyAdmin\Service\Base\BaseService;
use DateTime;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use SymfonyAdmin\Utils\RemoteService\AliOssRemoteService;

class AdminFileService extends BaseService
{
    /**
     * @param AdminAuth $adminAuth
     * @param Request $request
     * @return AdminFile[]
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
            $adminFile->setFileHost($request->getScheme() . '://' . $request->getHost());
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
        return $fileList;
    }


    /**
     * @param AdminAuth $adminAuth
     * @param Request $request
     * @return AdminFile[]
     * @throws OssException
     */
    public function uploadOss(AdminAuth $adminAuth, Request $request): array
    {
        $fileType = trim($request->get('fileType', 'default'));

        $fileList = [];
        $em = $this->doctrine->getManager();
        foreach ($request->files as $key => $file) {
            $fileSize = $file->getSize();
            $fileName = explode('.', $file->getClientOriginalName());
            $extName = array_pop($fileName) ?? '';
            $fileHash = hash_file('md5', $file->getFileInfo());
            $filePath = implode('/', ['upload/image', date('Ymd'), $fileHash . '.' . $extName]);
            $oldFile = $this->getAdminFileRepo()->findOneByFileHash($fileHash);
            if ($oldFile) {
                $fileList[] = $oldFile;
                continue;
            }

            $adminFile = new AdminFile();
            $ossResponseArr = AliOssRemoteService::uploadImgFile($filePath, $file);
            if (!empty($ossResponseArr['info']['url'])) {
                $urlArr = parse_url(trim($ossResponseArr['info']['url']));
                $urlArr['scheme'] .= strpos($urlArr['scheme'], 's') ? '' : 's';
                $adminFile->setFileHost($urlArr['scheme'] . '://' . $urlArr['host']);
                $adminFile->setFilePath($urlArr['path']);
                $adminFile->setFileSize($fileSize);
                $adminFile->setFileHash($fileHash);
                $adminFile->setFileType($fileType);
                $adminFile->setUserId($adminAuth->getAdminUser()->getId());
                $adminFile->setFileExt($extName);
                $adminFile->setCreateTime(new DateTime());
            }
            $fileList[] = $adminFile;
            $em->persist($adminFile);
        }
        $em->flush();
        return $fileList;
    }

}
