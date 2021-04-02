<?php


namespace SymfonyAdmin\Controller;


use SymfonyAdmin\Controller\Base\AdminApiController;
use SymfonyAdmin\Request\AdminUserRequest;
use SymfonyAdmin\Service\AdminFileService;
use SymfonyAdmin\Service\AdminMenuService;
use SymfonyAdmin\Service\AdminUserService;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use SymfonyAdmin\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AdminApiController
{

    /**
     * 获取当前登录用户可用菜单列表
     * @Route("/admin/home/menu", name="homeMenu")
     * @param LoggerInterface $errorLogger
     * @param AdminMenuService $adminMenuService
     * @return JsonResponse
     */
    public function getUserMenu(LoggerInterface $errorLogger, AdminMenuService $adminMenuService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();
            # 获取用户菜单列表
            $menuList = $adminMenuService->getUserMenu($adminAuth);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($menuList);
    }

    /**
     * 获取当前登录用户信息
     * @Route("/admin/home/user", name="homeUser")
     * @param LoggerInterface $errorLogger
     * @return JsonResponse
     */
    public function getUserInfo(LoggerInterface $errorLogger): JsonResponse
    {
        try {
            $userInfo = $this->adminAuthService->getLoginAuthInfo()->getAdminUser()->toArray();
            $userInfo['roleName'] = $this->adminAuthService->getLoginAuthInfo()->getAdminRole()->getRoleName();
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($userInfo);
    }

    /**
     * 更新自身用户资料信息
     * @Route("/admin/home/userUpdate", name="homeUserUpdate")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminUserService $adminUserService
     * @return JsonResponse
     */
    public function userUpdate(Request $request, LoggerInterface $errorLogger, AdminUserService $adminUserService): JsonResponse
    {
        try {
            $data = $this->getJsonRequest($request);
            $adminUserRequest = new AdminUserRequest();
            $adminUserRequest->setTrueName(trim($data['trueName'] ?? ''));
            $adminUserRequest->setEmail(trim($data['email'] ?? ''));
            $adminUserRequest->setAvatar(trim($data['avatar'] ?? ''));
            if (!empty($data['password'])) {
                $adminUserRequest->setPassword(trim($data['password'] ?? ''));
            }
            $r = $adminUserService->selfUpdate($this->adminAuthService->getLoginAuthInfo(), $adminUserRequest);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($r);
    }

    /**
     * 文件上传
     * @Route("/admin/home/fileUpload", name="fileUpload")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminFileService $adminFileService
     * @return JsonResponse
     */
    public function fileUpload(Request $request, LoggerInterface $errorLogger, AdminFileService $adminFileService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();
            $adminFileList = $adminFileService->upload($adminAuth, $request);
            $r = [];
            foreach ($adminFileList as $adminFile) {
                $r[] = [
                    'id' => $adminFile->getId(),
                    'filePath' => $adminFile->getFullPath(),
                ];
            }
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($r);
    }

}
