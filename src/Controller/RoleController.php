<?php


namespace SymfonyAdmin\Controller;


use SymfonyAdmin\Controller\Base\AdminApiController;
use SymfonyAdmin\Repository\AdminRoleRepository;
use SymfonyAdmin\Request\AdminRoleRequest;
use SymfonyAdmin\Service\AdminMenuService;
use SymfonyAdmin\Service\AdminRoleService;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use SymfonyAdmin\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RoleController extends AdminApiController
{

    /**
     * @Route("/admin/role/list", name="roleList")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminRoleService $adminRoleService
     * @return JsonResponse
     */
    public function getRoleList(Request $request, LoggerInterface $errorLogger, AdminRoleService $adminRoleService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();

            $pageNum = intval($request->query->get('pageNum', 1));
            $pageSize = intval($request->query->get('pageSize', 10));

            $conditions = [];
            foreach (AdminRoleRepository::$searchMap as $searchKey => $type) {
                if ($request->query->get($searchKey)) {
                    $conditions[$searchKey] = trim($request->query->get($searchKey));
                }
            }

            # 获取用户菜单列表
            $menuList = $adminRoleService->getRoleList($adminAuth, $pageNum, $pageSize, $conditions);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($menuList);
    }

    /**
     * @Route("/admin/role/get", name="getRole")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminRoleService $adminRoleService
     * @return JsonResponse
     */
    public function getOne(Request $request, LoggerInterface $errorLogger, AdminRoleService $adminRoleService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();
            $adminRoleRequest = new AdminRoleRequest();
            $adminRoleRequest->setId(intval($request->get('id', 0)));

            # 获取一个用户组信息
            $menuList = $adminRoleService->getOne($adminAuth, $adminRoleRequest);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($menuList);
    }

    /**
     * @Route("/admin/role/create", name="createRole")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminRoleService $adminRoleService
     * @return JsonResponse
     */
    public function create(Request $request, LoggerInterface $errorLogger, AdminRoleService $adminRoleService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();
            $data = $this->getJsonRequest($request);
            $adminRoleRequest = new AdminRoleRequest();
            $adminRoleRequest->setRoleName(trim($data['roleName'] ?? ''));
            $adminRoleRequest->setRoleCode(trim($data['roleCode'] ?? ''));

            # 添加用户组
            $menuList = $adminRoleService->create($adminAuth, $adminRoleRequest);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($menuList);
    }


    /**
     * @Route("/admin/role/update", name="updateRole")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminRoleService $adminRoleService
     * @return JsonResponse
     */
    public function update(Request $request, LoggerInterface $errorLogger, AdminRoleService $adminRoleService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();
            $data = $this->getJsonRequest($request);
            $adminRoleRequest = new AdminRoleRequest();
            $adminRoleRequest->setId(intval($data['id'] ?? 0));

            # 更新用户组
            $menuList = $adminRoleService->update($adminAuth, $adminRoleRequest, $data);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($menuList);
    }

    /**
     * @Route("/admin/role/delete", name="deleteRole")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminRoleService $adminRoleService
     * @return JsonResponse
     */
    public function delete(Request $request, LoggerInterface $errorLogger, AdminRoleService $adminRoleService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();
            $data = $this->getJsonRequest($request);
            $adminRoleRequest = new AdminRoleRequest();
            $adminRoleRequest->setId(intval($data['id'] ?? 0));

            # 删除用户组
            $menuList = $adminRoleService->delete($adminAuth, $adminRoleRequest);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($menuList);
    }


    /**
     * @Route("/admin/role/updateMenu", name="updateRoleMenu")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminMenuService $adminMenuService
     * @return JsonResponse
     */
    public function updateRoleMenu(Request $request, LoggerInterface $errorLogger, AdminMenuService $adminMenuService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();
            $data = $this->getJsonRequest($request);
            $roleId = intval($data['roleId'] ?? 0);
            $menuIds = array_map('intval', $data['menuIds'] ?? []);
            # 获取用户菜单列表
            $menuList = $adminMenuService->updateRoleMenus($adminAuth, $roleId, $menuIds);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($menuList);
    }
}
