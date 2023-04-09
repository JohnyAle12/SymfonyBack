<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\{Delete, Get, Post, Put, Patch};
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\View as ViewAttribute;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractFOSRestController
{
    public function __construct(
        private LoggerInterface $logger,
        private UserPasswordHasherInterface $passwordHasher
    ){}

    #[Get(path: "/users")]
    #[ViewAttribute(serializerGroups: ['users'], serializerEnableMaxDepthChecks: true)]
    public function list(
        UserRepository $userRepository
    ): array
    {
        return $userRepository->findAll();
    }

    #[Post(path: "/users/create")]
    #[ViewAttribute(serializerGroups: ['users'], serializerEnableMaxDepthChecks: true)]
    public function store(
        Request $request,
        EntityManagerInterface $em
    ): User
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

        return $user;
    }
}