<?php

namespace App\Controller;

use App\Form\ForgotPasswordType;
use App\Form\PasswordUpdateType;
use App\Repository\UsersRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils ): Response
    {
        if ($this->getUser()) {
            $user = $this->getUser();
            if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                return $this->redirectToRoute('app_home_admin');

            }

            
            return $this->redirectToRoute('app_home');
        }


        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    #[Route(path: '/admin/login', name: 'app_admin_login')]
    public function loginAdmin(AuthenticationUtils $authenticationUtils ): Response
    {
        if ($this->getUser()) {
            $user = $this->getUser();

            
            if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                return $this->redirectToRoute('app_home_admin');
            }

            return $this->redirectToRoute('app_home');
        }


        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('back/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
        

    }

    #[Route(path: '/logout', name: 'app_logout')]
     public function logout(Request $req): Response
    {
        
        //return $this->render('security/logout.html.twig');
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    
    #[Route(path: '/resetPassword', name: 'app_reset')]
    public function resetPassword(Request $request, UsersRepository $userRepository,MailerInterface  $mailer,TokenGeneratorInterface $token)
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $user = $userRepository->findOneBy(['email' => $data['email']]);
            if (!$user){
                $this->addFlash('danger', 'Email Inconnu');
                return $this->redirectToRoute('app_resetPassword');
            }
            $resetToken = $token->generateToken();
            try {
                $user->setResetToken($resetToken);
                $userRepository->save($user, true);
            }catch (\Exception $e){
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('app_resetPassword');
            }
            $url = $this->generateUrl('app_resetPassword', ['token' => $resetToken], UrlGeneratorInterface::ABSOLUTE_URL);
            $email = (new TemplatedEmail())
                ->from('blooperrr@outlook.com')
                ->to(new Address($user->getEmail()))
                ->subject('Mot de passe oublié')
                ->text('Voici votre lien pour réinitialiser votre mot de passe : '.$url);

            $mailer->send($email);
            $this->addFlash('success', 'Mail envoyé');
        }
        return $this->render('security/resetPassword.html.twig', [
            'form' => $form->createView(),
            'token' => $request->get('token')
        ]);
    }


    #[Route(path: '/reset', name: 'app_resetPassword')]
    public function resetredir(Request $request,UsersRepository $userRepository): Response
    {
    $form = $this->createForm(PasswordUpdateType::class);
      $form->handleRequest($request);

      if($form->isSubmitted() && $form->isValid()){
        
          $data = $form->get('password')->getData();
          $user = $userRepository->findOneBy(['resetToken' => $request->get('token')] );
          if (!$user){
              $this->addFlash('danger', 'Token Inconnu');
              return $this->redirectToRoute('app_resetPassword');
          }
          $user->setPassword($this->passwordEncoder->encodePassword($user, $data));
          $user->setResetToken(null);
          $userRepository->save($user, true);
          $this->addFlash('success', 'Mot de passe mis à jour');
          return $this->redirectToRoute('app_login');
      }
    return $this->render('security/passwordUpdate.html.twig',[
        'form' => $form->createView(),
        'token' => $request->get('token'),
    ]);
    }

}
