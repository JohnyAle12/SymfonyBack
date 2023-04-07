<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger,
        private UserPasswordHasherInterface $passwordHasher
    ){}

    /**
     * @Route("/users", name="users.list")
     */
    public function list(UserRepository $userRepository): JsonResponse
    {
        $response = new JsonResponse();
        $this->logger->info('List action was called');
        $users = array_map(function($user){
            return [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
            ];
        }, $userRepository->findAll(Query::HYDRATE_ARRAY));

        return $response->setData([
            'data' => $users
        ]);
    }

    /**
     * @Route("/users/create", name="users.create")
     */
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = $request->toArray();
        $user = new User;
        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setPassword($this->passwordHasher->hashPassword(
            $user, $data['password']
        ));
        $em->persist($user);
        $em->flush();

        $response = new JsonResponse();
        return $response->setData([
            'data' => [
                [
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                ]
            ]
        ]);
    }
}