<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Repository\ApiTokenRepository;
use App\Repository\UserRepository;
use App\Requests\LoginValidator;
use App\Utils\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/api/v1')]
class AuthController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private ApiTokenRepository $apiTokenRepository
    )
    {
    }

    #[Route('/auth/login', name: 'app_auth', methods: ['POST'])]
    public function login(
        LoginValidator $validator,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        TokenGenerator $tokenGenerator
    ): Response
    {
        $validator->validate();

        $data = $request->toArray();
        $user = $this->userRepository->findByEmail($data['email']);

        if (! $user || !$passwordHasher->isPasswordValid($user, $data['password'])) {
            return $this->json(['data' => $data]);
        }

        $tokenApi = new ApiToken();
        $tokenApi->setToken($tokenGenerator());
        $tokenApi->setUser($user);
        $this->apiTokenRepository->add($tokenApi);

        return $this->json(['data' => [
            'token' => $tokenApi->getToken()
        ]]);
    }

    #[Route('/auth/current-user', name: 'app_current_user', methods: ['POST'])]
    public function currentUser(Request $request)
    {
        $token = $request->headers->get('x-api-token');
        if (! $token) {
            throw new \Exception("UnAuthorization");
        }
        $user = $this->userRepository->findByToken($token);

        if (! $user) {
            throw new \Exception("UnAuthorization");
        }

        return $this->json($user);
    }
}
