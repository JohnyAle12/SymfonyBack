<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger
    ){}

    /**
     * @Route("/users", name="users")
     */
    public function list(): JsonResponse
    {
        $response = new JsonResponse();
        $this->logger->info('List action was called');
        return $response->setData([
            'data' => [
                [
                    'id' => 1,
                    'name' => 'Johnny'
                ],
                [
                    'id' => 2,
                    'name' => 'Alejandro'
                ]
            ]
        ]);
    }
}