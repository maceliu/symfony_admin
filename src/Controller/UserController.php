<?php


namespace SymfonyAdmin\Controller;


use SymfonyAdmin\Controller\Base\AdminApiController;
use SymfonyAdmin\Repository\AdminUserRepository;
use SymfonyAdmin\Request\AdminUserRequest;
use SymfonyAdmin\Service\AdminUserService;
use SymfonyAdmin\Utils\Enum\StatusEnum;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use SymfonyAdmin\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AdminApiController
{

    /**
     * @Route("/admin/user/list", name="userList")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminUserService $adminUserService
     * @return JsonResponse
     */
    public function getUserList(Request $request, LoggerInterface $errorLogger, AdminUserService $adminUserService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();
            $pageNum = intval($request->query->get('pageNum', 1));
            $pageSize = intval($request->query->get('pageSize', 10));

            $conditions = [];
            foreach (AdminUserRepository::$searchMap as $searchKey => $type) {
                if ($request->query->get($searchKey)) {
                    $conditions[$searchKey] = trim($request->query->get($searchKey));
                }
            }

            # 获取用户菜单列表
            $menuList = $adminUserService->getListByPage($adminAuth, $pageNum, $pageSize, $conditions);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($menuList);
    }

    /**
     * @Route("/admin/user/get", name="userGet")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminUserService $adminUserService
     * @return JsonResponse
     */
    public function getOne(Request $request, LoggerInterface $errorLogger, AdminUserService $adminUserService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();

            $adminUserRequest = new AdminUserRequest();
            $adminUserRequest->setId(intval($request->get('id', $adminAuth->getAdminUser()->getId())));

            $adminUser = $adminUserService->getOne($adminAuth, $adminUserRequest);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($adminUser);
    }

    /**
     * @Route("/admin/user/create", name="userCreate")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminUserService $adminUserService
     * @return JsonResponse
     */
    public function create(Request $request, LoggerInterface $errorLogger, AdminUserService $adminUserService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();

            $data = $this->getJsonRequest($request);
            $adminUserRequest = new AdminUserRequest();
            $adminUserRequest->setUsername(trim($data['username'] ?? ''));
            $adminUserRequest->setTrueName(trim($data['trueName'] ?? ''));
            $adminUserRequest->setEmail(trim($data['email'] ?? ''));
            $adminUserRequest->setPassword(trim($data['password'] ?? ''));
            $adminUserRequest->setRemark(trim($data['remark'] ?? ''));
            $adminUserRequest->setAvatar(trim($data['avatar'] ?? ''));
            $adminUserRequest->setStatus(trim($data['status']) ?? StatusEnum::ON);
            # 获取用户菜单列表
            $adminUser = $adminUserService->create($adminAuth, $adminUserRequest);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($adminUser);
    }

    /**
     * @Route("/admin/user/update", name="userUpdate")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminUserService $adminUserService
     * @return JsonResponse
     */
    public function update(Request $request, LoggerInterface $errorLogger, AdminUserService $adminUserService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();

            $data = $this->getJsonRequest($request);
            $adminUserRequest = new AdminUserRequest();
            $adminUserRequest->setId(intval($data['id'] ?? 0));

            $adminUser = $adminUserService->update($adminAuth, $adminUserRequest, $data);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($adminUser);
    }

    /**
     * @Route("/admin/user/delete", name="userDelete")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminUserService $adminUserService
     * @return JsonResponse
     */
    public function delete(Request $request, LoggerInterface $errorLogger, AdminUserService $adminUserService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();

            $data = $this->getJsonRequest($request);
            $adminUserRequest = new AdminUserRequest();
            $adminUserRequest->setId(intval($data['id'] ?? 0));

            $adminUser = $adminUserService->delete($adminAuth, $adminUserRequest);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($adminUser);
    }

    /**
     * @Route("/admin/user/roleUpdate", name="userRoleUpdate")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminUserService $adminUserService
     * @return JsonResponse
     */
    public function updateUserRole(Request $request, LoggerInterface $errorLogger, AdminUserService $adminUserService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();

            $data = $this->getJsonRequest($request);
            $adminUserRequest = new AdminUserRequest();
            $adminUserRequest->setId(intval($data['userId'] ?? 0));
            $adminUserRequest->setRoleId(intval($data['roleId'] ?? 0));

            # 获取用户菜单列表
            $adminUser = $adminUserService->updateUserRole($adminAuth, $adminUserRequest);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($adminUser);
    }
}
