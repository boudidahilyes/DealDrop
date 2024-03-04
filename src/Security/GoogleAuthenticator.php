<?php
namespace App\Security;


use App\Entity\Users; // your user entity
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;


class GoogleAuthenticator  extends SocialAuthenticator
{
    private $clientRegistry;
    private $em;
    private $router;
    


    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
        

    }

    public function supports(Request $request)
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function getCredentials(Request $request)
    {
        // this method is only called if supports() returns true

        // For Symfony lower than 3.4 the supports method need to be called manually here:
        // if (!$this->supports($request)) {
        //     return null;
        // }

        return $this->fetchAccessToken($this->getGoogleClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        
        $googleUser = $this->getGoogleClient()
            ->fetchUserFromToken($credentials);


        $email = $googleUser->getEmail();

        // 1) have they logged in with Facebook before? Easy!
        $user = $this->em->getRepository(Users::class)
            ->findOneBy(['email' => $email]);
        if (!$user) {
            $user=new Users();
            $user->setEmail($googleUser->getEmail());
            $user->setFirstName($googleUser->getName());
            $user->setPassword('google_authenticated'); 
            $user->setBirthDate(new \DateTimeImmutable());
            $user->setJoiningDate(new \DateTimeImmutable());
            $user->setRoles(['ROLE_USER']);
            $this->em->persist($user);
            $this->em->flush();

        }

        return $user;
    }

   
    private function getGoogleClient(): GoogleClient
    {
        return $this->clientRegistry

            ->getClient('google');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetUrl = $this->router->generate('app_home');
        return new RedirectResponse($targetUrl);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
       /* return new RedirectResponse(
            '/connect/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );*/
        return new RedirectResponse('/login');
    }
}