<?php

namespace MaceLiu\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController
{
    /**
     * @Route("/maceliu/index", name="maceliuIndex")
     * @param Request $request
     * @param LoggerInterface $logger
     * @return JsonResponse
     */
    public function login(Request $request, LoggerInterface $logger): JsonResponse
    {
        var_dump('hello world!');
        var_dump($request->get('test', 1));
        exit;
    }
}