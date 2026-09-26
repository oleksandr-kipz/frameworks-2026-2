<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class AuthController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/api/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if (empty($requestData['email']) || empty($requestData['password'])) {
            return $this->json("Email and password are required", Response::HTTP_BAD_REQUEST);
        }

        /** @var User $existingUser */
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $requestData['email']]);

        if ($existingUser) {
            return $this->json("User already exists", Response::HTTP_CONFLICT);
        }

        $newUser = new User();

        $newUser->setEmail($requestData['email']);
        $newUser->setRoles(['ROLE_USER']);
        $newUser->setPassword($this->passwordHasher->hashPassword($newUser, $requestData['password']));

        $this->entityManager->persist($newUser);
        $this->entityManager->flush();

        return $this->json($newUser, Response::HTTP_CREATED);
    }

    #[Route('/api/login', name: 'app_login', methods: ['POST'])]
    public function login(): void
    {
        // Handled by the json_login authenticator on the "login" firewall
        throw new \LogicException('This code should never be reached.');
    }

    #[Route('/api/me', name: 'app_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        return $this->json($this->getUser());
    }

}
