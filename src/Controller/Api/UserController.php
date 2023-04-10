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
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UserController extends AbstractFOSRestController
{
    public function __construct(
        private LoggerInterface $logger,
        private ValidatorInterface $validator,
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
    )
    {
        try {
            $data = $request->toArray();
            $user = new User;
            $user->setName($data['name']);
            $user->setEmail($data['email']);
            $user->setPassword($this->passwordHasher->hashPassword(
                $user, $data['password']
            ));

            $errors = $this->validateRequest($user);
            if(count($errors)){
                return new JsonResponse([
                    'errors' => $errors
                ]);
            }

            $em->persist($user);
            $em->flush();

            return $user;
        } catch (\Throwable $th) {
            return new JsonResponse([
                'error' => $th->getMessage()
            ]);
        }
    }

    private function validateRequest(
        User $user,
    ): array
    {
        $messages = [];
        $errors = $this->validator->validate($user);
        if(count($errors) > 0){
            foreach ($errors as $violation) {
                $messages[] = $violation->getMessage();
            }
        }
        return $messages;
    }
}