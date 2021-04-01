<?php

namespace SymfonyAdmin\Controller\Base;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class BaseController extends AbstractController
{
    /**
     * @param Request $request
     * @return array
     */
    public function getJsonRequest(Request $request): array
    {
        $data = [];
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
        }
        return $data;
    }
}
