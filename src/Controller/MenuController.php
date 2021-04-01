<?php


namespace SymfonyAdmin\Controller;


use SymfonyAdmin\Controller\Base\AdminApiController;
use SymfonyAdmin\Request\AdminMenuRequest;
use SymfonyAdmin\Service\AdminMenuService;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use SymfonyAdmin\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AdminApiController
{

    /**
     * 获取当前登录用户所有可管辖菜单列表，并格式化展示
     * @Route("/admin/menu/listFormat", name="menuListFormat")
     * @param LoggerInterface $errorLogger
     * @param AdminMenuService $adminMenuService
     * @return JsonResponse
     */
    public function getMenuListFormat(LoggerInterface $errorLogger, AdminMenuService $adminMenuService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();
            # 获取用户菜单列表
            $menuList = $adminMenuService->getMenuListWithFormat($adminAuth);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($menuList);
    }

    /**
     * 获取指定用户所有可管辖菜单列表示
     * @Route("/admin/menu/list", name="menuList")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminMenuService $adminMenuService
     * @return JsonResponse
     */
    public function getUserMenuList(Request $request, LoggerInterface $errorLogger, AdminMenuService $adminMenuService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();
            $roleId = intval($request->get('roleId', 0));
            # 获取用户菜单列表
            $menuList = $adminMenuService->getMenuList($adminAuth, $roleId);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($menuList);
    }


    /**
     * 获取指定菜单详情
     * @Route("/admin/menu/get", name="menuGet")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminMenuService $adminMenuService
     * @return JsonResponse
     */
    public function getOneMenu(Request $request, LoggerInterface $errorLogger, AdminMenuService $adminMenuService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();
            $adminMenuRequest = new AdminMenuRequest();
            $adminMenuRequest->setId(intval($request->query->get('menuId', 0)));
            # 获取用户菜单列表
            $menuList = $adminMenuService->getOneMenu($adminAuth, $adminMenuRequest);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($menuList);
    }

    /**
     * 更新菜单
     * @Route("/admin/menu/update", name="menuUpdate")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminMenuService $adminMenuService
     * @return JsonResponse
     */
    public function update(Request $request, LoggerInterface $errorLogger, AdminMenuService $adminMenuService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();

            $data = $this->getJsonRequest($request);
            $adminMenuRequest = new AdminMenuRequest();
            $adminMenuRequest->setId(intval($data['id'] ?? 0));
            $adminMenuRequest->setParentId(intval($data['parentId'] ?? 0));

            # 获取用户菜单列表
            $menuList = $adminMenuService->update($adminAuth, $adminMenuRequest, $data);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($menuList);
    }

    /**
     * 新增菜单
     * @Route("/admin/menu/create", name="menuCreate")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminMenuService $adminMenuService
     * @return JsonResponse
     */
    public function create(Request $request, LoggerInterface $errorLogger, AdminMenuService $adminMenuService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();

            $data = $this->getJsonRequest($request);
            $adminMenuRequest = new AdminMenuRequest();
            $adminMenuRequest->setIcon(trim($data['icon'] ?? ''));
            $adminMenuRequest->setMenuName(trim($data['menuName'] ?? ''));
            $adminMenuRequest->setParentId(intval($data['parentId'] ?? 0));
            $adminMenuRequest->setPath(trim($data['path'] ?? ''));
            $adminMenuRequest->setStatus(trim($data['status'] ?? ''));
            $adminMenuRequest->setType(trim($data['type'] ?? ''));
            $adminMenuRequest->setWeight(intval($data['weight'] ?? 0));
            $adminMenuRequest->setIsHidden(intval($data['isHidden'] ?? 0));

            # 获取用户菜单列表
            $menuList = $adminMenuService->create($adminAuth, $adminMenuRequest);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($menuList);
    }

    /**
     * 更新菜单
     * @Route("/admin/menu/delete", name="menuDelete")
     * @param Request $request
     * @param LoggerInterface $errorLogger
     * @param AdminMenuService $adminMenuService
     * @return JsonResponse
     */
    public function delete(Request $request, LoggerInterface $errorLogger, AdminMenuService $adminMenuService): JsonResponse
    {
        try {
            $adminAuth = $this->adminAuthService->getLoginAuthInfo();

            $data = $this->getJsonRequest($request);
            $adminMenuRequest = new AdminMenuRequest();
            $adminMenuRequest->setId(intval($data['id'] ?? 0));

            # 获取用户菜单列表
            $menuList = $adminMenuService->delete($adminAuth, $adminMenuRequest);
        } catch (Exception $e) {
            return ApiResponse::exception($e, $errorLogger);
        }

        return ApiResponse::success($menuList);
    }
}
