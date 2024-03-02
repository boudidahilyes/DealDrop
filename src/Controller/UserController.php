<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UpdateUserFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\IsNull;

class UserController extends AbstractController
{

    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
         $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('user/login.html.twig', [
            'last_username' => $lastUsername,
             'error'         => $error,
        ]);
    }
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $user = $this->getUser();
        $form = $this->createForm(UpdateUserFormType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            if(!is_null($form->get('password')->getData()))
            {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
        return $this->render('user/profile.html.twig', [
            'userForm' => $form->createView()
        ]);
    }

    
    
}
