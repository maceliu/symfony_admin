<?php

namespace SymfonyAdmin\Controller;

use SymfonyAdmin\Controller\Base\AdminApiController;
use Exception;
use SymfonyAdmin\Request\AdminUserRequest;
use SymfonyAdmin\Response\ApiResponse;
use SymfonyAdmin\Service\AdminUserService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AdminApiController
{
    /**
     * @Route("/maceliu/index", name="maceliuIndex")
     * @param Request $request
     * @param LoggerInterface $logger
     * @return JsonResponse
     */
    public function index(Request $request, LoggerInterface $logger): JsonResponse
    {
        var_dump('hello world!');
        var_dump($request->get('test', 1));
        exit;
    }

    /**
     * @Route("/admin/index/login", name="adminIndexLogin")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminUserService $adminUserService
     * @return JsonResponse
     */
    public function login(Request $request, LoggerInterface $errorLogger, AdminUserService $adminUserService): JsonResponse
    {
        try {
            $requestData = $this->getJsonRequest($request);

            $userRequest = new AdminUserRequest();
            $userRequest->setUsername(trim($requestData['username'] ?? ''));
            $userRequest->setPassword(trim($requestData['password'] ?? ''));

            # 验证用户登录状态
            $userInfo = $adminUserService->login($userRequest);

        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($userInfo);
    }
}