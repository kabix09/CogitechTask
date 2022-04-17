<?php

namespace App\Security;

use App\Entity\Manager;
use App\Repository\ManagerRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class LoginFormAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var ManagerRepository
     */
    private $managerRepository;

    /**
     * LoginFormAuthenticator constructor.
     * @param ManagerRepository $managerRepository
     * @param RouterInterface $router
     */
    public function __construct(ManagerRepository $managerRepository, RouterInterface $router)
    {
        $this->router = $router;
        $this->managerRepository = $managerRepository;
    }

    public function supports(Request $request): ?bool
    {
        return ($request->getPathInfo() === '/login' && $request->isMethod('POST'));
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('login')['email'];
        $password = $request->request->get('login')['password'];

        return new Passport(
            new UserBadge($email, function($userIdentifier) {
                $user = $this->managerRepository->findOneBy(['email' => $userIdentifier]);

                if (!$user) {
                    throw new UserNotFoundException();
                }

                return $user;
            }),
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse(
            $this->router->generate('app_post_list')
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        return new RedirectResponse(
            $this->router->generate('app_login')
        );
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        /*
         * If you would like this class to control what happens when an anonymous user accesses a
         * protected page (e.g. redirect to /login), uncomment this method and make this class
         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
         *
         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
         */
        return new RedirectResponse(
            $this->router->generate('app_login')
        );
    }
}
