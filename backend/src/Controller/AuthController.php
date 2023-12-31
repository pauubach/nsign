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
     *
     * @return JsonResponse
     */
    public function register(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): JsonResponse
    {
        $request = $this->transformJsonBody($request);
        $email = $request->get('email');
        $password = $request->get('password');

        if (empty($email) || empty($password)) {
            return $this->respondValidationError("Email o Contraseña inválidos");
        }

        if ($em->getRepository(User::class)->findByEmail($email)) {
            return $this->respondValidationError("No se ha podido crear el usuario.");
        }

        $user = new User($email);
        $user->setPassword($hasher->hashPassword($user, $password));
        $user->setEmail($email);

        $roles = $em->getRepository(User::class)->findAll() ? ['ROLE_USER'] : ['ROLE_ADMIN', 'ROLE_USER'];
        $user->setRoles($roles);
        $this->em->persist($user);
        $this->em->flush();
        return $this->respondWithSuccess(sprintf('Usuario %s creado', $user->getEmail()));
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

    /**
     * @Route("/api/user", name="user", methods={"POST"})
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
     */
    public function getUserInfo(Request $request): JsonResponse
    {
        $user = $this->getUser();

        return new JsonResponse([
            'email' => $user->getUserIdentifier(),
            'roles' => $user->getRoles()
        ]);
    }
}