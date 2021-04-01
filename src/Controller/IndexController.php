<?php

namespace SymfonyAdmin\Controller;

use SymfonyAdmin\Controller\Base\AdminApiController;
use Exception;
use SymfonyAdmin\Request\AdminUserRequest;
use SymfonyAdmin\Response\ApiResponse;
use SymfonyAdmin\Service\AdminLoginService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AdminApiController
{
    /**
     * @Route("/admin/index/login", name="adminIndexLogin")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminLoginService $adminLoginService
     * @return JsonResponse
     */
    public function login(Request $request, LoggerInterface $errorLogger, AdminLoginService $adminLoginService): JsonResponse
    {
        try {
            $requestData = $this->getJsonRequest($request);

            $userRequest = new AdminUserRequest();
            $userRequest->setUsername(trim($requestData['username'] ?? ''));
            $userRequest->setPassword(trim($requestData['password'] ?? ''));

            # 验证用户登录状态
            $userInfo = $adminLoginService->login($userRequest);

        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($userInfo);
    }

    /**
     * @Route("/admin/index/findPass", name="adminIndexFindPass")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminLoginService $adminLoginService
     * @return JsonResponse
     */
    public function findMyPassword(Request $request, LoggerInterface $errorLogger, AdminLoginService $adminLoginService): JsonResponse
    {
        try {
            $requestData = $this->getJsonRequest($request);
            $userRequest = new AdminUserRequest();
            $userRequest->setUsername(trim($requestData['username'] ?? ''));
            # 发送找回密码
            $r = $adminLoginService->findMyPassword($userRequest);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success([], $r);
    }

    /**
     * @Route("/admin/index/resetPass", name="adminIndexResetPass")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminLoginService $adminLoginService
     * @return JsonResponse
     */
    public function resetPass(Request $request, LoggerInterface $errorLogger, AdminLoginService $adminLoginService): JsonResponse
    {
        try {
            $requestData = $this->getJsonRequest($request);
            $userRequest = new AdminUserRequest();
            $userRequest->setUsername(trim($requestData['username'] ?? ''));
            $userRequest->setPassword(trim($requestData['password'] ?? ''));
            $userRequest->setCheckCode(intval($requestData['checkCode'] ?? 0));
            # 发送找回密码
            $r = $adminLoginService->resetPass($userRequest);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success();
    }
}