<?php


namespace SymfonyAdmin\Controller;


use SymfonyAdmin\Controller\Base\AdminApiController;
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
     * @param LoggerInterface $errorLogger
     * @param AdminRoleService $adminRoleService
     * @return JsonResponse
     */
    public function getRoleList(LoggerInterface $errorLogger, AdminRoleService $adminRoleService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();

            # 获取用户菜单列表
            $menuList = $adminRoleService->getAllRoleList($adminAuth);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($menuList);
    }

    /**
     * @Route("/admin/role/listPage", name="roleListPage")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminRoleService $adminRoleService
     * @return JsonResponse
     */
    public function getRoleListWithPage(Request $request, LoggerInterface $errorLogger, AdminRoleService $adminRoleService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();

            $pageNum = intval($request->query->get('pageNum', 1));
            $pageSize = intval($request->query->get('pageSize', 10));
            # 获取用户菜单列表
            $menuList = $adminRoleService->getRoleListWithPage($adminAuth, $pageNum, $pageSize);
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
            $adminRoleRequest->setStatus(trim($data['status'] ?? ''));
            $adminRoleRequest->setParentRoleId(intval($data['roleId'] ?? 0));

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
            $adminRoleRequest->setRoleName(trim($data['roleName'] ?? ''));
            $adminRoleRequest->setRoleCode(trim($data['roleCode'] ?? ''));
            $adminRoleRequest->setStatus(trim($data['status'] ?? ''));
            $adminRoleRequest->setParentRoleId(intval($data['roleId'] ?? 0));

            # 更新用户组
            $menuList = $adminRoleService->update($adminAuth, $adminRoleRequest);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($menuList);
    }

    /**
     * @Route("/admin/role/updateStatus", name="updateRoleStatus")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminRoleService $adminRoleService
     * @return JsonResponse
     */
    public function updateStatus(Request $request, LoggerInterface $errorLogger, AdminRoleService $adminRoleService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();
            $data = $this->getJsonRequest($request);
            $adminRoleRequest = new AdminRoleRequest();
            $adminRoleRequest->setId(intval($data['id'] ?? 0));
            $adminRoleRequest->setStatus(trim($data['status'] ?? ''));

            # 更新用户组状态
            $menuList = $adminRoleService->updateStatus($adminAuth, $adminRoleRequest);
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
