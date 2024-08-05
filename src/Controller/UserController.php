<?php

namespace App\Controller;

use App\Entity\User;
use App\Exceptions\EmailUniqueViolationException;
use App\Repository\UserRepository;
use App\Requests\UserValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
    public function create(UserValidator $validator, Request $request, UserPasswordHasherInterface $passwordHasher): Response
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
        if ($data['password']) {
            $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['data' => $user]);
    }

    #[Route('/users/{id}', name: 'users_delete', methods: ['DELETE'])]
    public function delete(User $user): Response
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->json(['data' => 'User was deleted']);
    }

    #[Route('/users/{id}', name: 'user_get')]
    public function get(int $id)
    {
        return $this->json($this->userRepository->find($id));
    }

    #[Route('/users/{id}', 'users_update', methods: ['PATCH', 'PUT'])]
    public function update(User $user, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $data = $request->toArray();
        if (isset($data['password'])) {
            $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
        }
        $user->setEmail($data['email'] ?? $user->getEmail());
        $user->setName($data['name'] ?? $user->getName());
        $user->setLastName($data['last_name'] ?? $user->getLastName());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json($user);
    }

}
