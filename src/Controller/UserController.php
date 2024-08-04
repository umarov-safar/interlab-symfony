<?php

namespace App\Controller;

use App\Entity\User;
use App\Exceptions\EmailUniqueViolationException;
use App\Repository\UserRepository;
use App\Requests\UserValidator;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1')]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
    )
    {
    }

    #[Route('/users', name: 'users_create')]
    public function create(UserValidator $validator, Request $request): Response
    {
        $validator->validate();
        $user = new User();
        $data = $request->toArray();

        if ($this->userRepository->findByEmail($data['email'])) {
            throw new EmailUniqueViolationException('Email already exists');
        }

        $user->setName($data['name']);
        $user->setLastName($data['last_name'] ?? null);
        $user->setEmail($data['email']);
        $user->setPassword($data['password']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse($user);
    }
}
