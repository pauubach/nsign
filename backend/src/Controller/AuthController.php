<?php


namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthController extends ApiController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/api/register", name="register", methods={"POST"})
     * @param Request $request
     * @param UserPasswordHasherInterface $encoder
     * @return JsonResponse
     */
    public function register(Request $request, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $request = $this->transformJsonBody($request);
        $email = $request->get('email');
        $password = $request->get('password');

        if (empty($email) || empty($password)) {
            return $this->respondValidationError("Email o Contraseña inválidos");
        }

        $user = new User($email);
        $user->setPassword($hasher->hashPassword($user, $password));
        $user->setEmail($email);
        $user->setRoles([]);
        $this->em->persist($user);
        $this->em->flush();
        return $this->respondWithSuccess(sprintf('User %s successfully created', $user->getEmail()));
    }

    /**
     * @Route("/api/login", name="login", methods={"POST"})
     * @param Request $request
     * @param UserPasswordHasherInterface $encoder
     * @return JsonResponse
     */
    public function login(Request $request, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $request = $this->transformJsonBody($request);
        $email = $request->get('email');
        $password = $request->get('password');

        if (empty($email) || empty($password)) {
            return $this->respondValidationError("Email o Contraseña inválidos");
        }

        $user = $this->em->getRepository(User::class)->findOneByEmail($email);
        return $this->respondWithSuccess(sprintf('User %s successfully created', $user->getEmail()));
    }

    /**
     * @Route("/api/login_check", name="login-check", methods={"POST"})
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
     */
    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        return new JsonResponse(['token' => $JWTManager->create($user)]);
    }
}
