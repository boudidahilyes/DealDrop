<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\PasswordUpdateType;
use App\Form\UpdateUserFormType;
use App\Repository\MemberRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\IsNull;

class UserController extends AbstractController
{

    private $entityManager;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $user = $this->getUser();
            if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                return $this->redirectToRoute('app_home_admin');
            }


            return $this->redirectToRoute('app_home');
        }
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
            if (!is_null($form->get('password')->getData())) {
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

    #[Route(path: '/resetPassword', name: 'app_reset')]
    public function resetPassword(Request $request, UserRepository $userRepository, MailerInterface  $mailer, TokenGeneratorInterface $token)
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $userRepository->findOneBy(['email' => $data['email']]);
            if (!$user) {
                $this->addFlash('danger', 'Email Inconnu');
                return $this->redirectToRoute('app_reset');
            }
            $resetToken = $token->generateToken();
            try {
                $user->setResetToken($resetToken);
                $userRepository->save($user, true);
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('app_reset');
            }
            $url = $this->generateUrl('app_resetPassword', ['token' => $resetToken], UrlGeneratorInterface::ABSOLUTE_URL);
            $email = (new TemplatedEmail())
                ->from('dealdrop.pidev@outlook.com')
                ->to(new Address($user->getEmail()))
                ->subject('Mot de passe oublié')
                ->text('Voici votre lien pour réinitialiser votre mot de passe : ' . $url);

            $mailer->send($email);
            $this->addFlash('success', 'Mail envoyé');
        }
        return $this->render('user/reset_password.html.twig', [
            'form' => $form->createView(),
            'token' => $request->get('token')
        ]);
    }

    #[Route(path: '/reset', name: 'app_resetPassword')]
    public function resetredir(Request $request, UserRepository $userRepository): Response
    {
        $form = $this->createForm(PasswordUpdateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->get('password')->getData();
            $user = $userRepository->findOneBy(['resetToken' => $request->get('token')]);
            if (!$user) {
                $this->addFlash('danger', 'Token Inconnu');
                return $this->redirectToRoute('app_resetPassword');
            }
            $user->setPassword($this->passwordEncoder->encodePassword($user, $data));
            $user->setResetToken(null);
            $userRepository->save($user, true);
            $this->addFlash('success', 'Mot de passe mis à jour');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('user/password_update.html.twig', [
            'form' => $form->createView(),
            'token' => $request->get('token'),
        ]);
    }

    #[Route('/admin/users/{role}', name: 'app_admin_users', methods: ['GET'])]
    public function displayMembersAndDeliveryMen(UserRepository $userRepository, $role): Response
    {
        $user = $this->getUser();
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            $users = $userRepository->findByRole($role);

            return $this->render('user/backOfficeMembersList.html.twig', [
                'users' => $users,

            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    #[Route(path: '/admin/home', name: 'app_home_admin', methods: ['GET'])]
    public function displayAdmins(Request $req, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            $users = $userRepository->findByRole("ROLE_ADMIN");
            return $this->render('user/backOfficeAdminsView.html.twig', [
                'users' => $users,
            ]);
        }

        return $this->redirectToRoute('app_home');
    }
    #[Route('/user/all/delete/{id}', name: 'app_user_edit_delete')]
    public function delete(Request $request, User $user, UserRepository $userRepository,$id,EntityManagerInterface $em,MemberRepository $memberRepository): Response
    {
        $user=$memberRepository->find($id);
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_admin_users', ['role' => 'ROLE_MEMBER']);
    }

    #[Route('/user/edit/{id}', name: 'app_user_edit')]
    public function edit(Request $req,UserRepository $rep,$id,EntityManagerInterface $em): Response
    {

        $user=$rep->find($id);
        $form=$this->createForm(UpdateUserFormType::class,$user);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid())
        { 
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('app_home');
        }
        return $this->renderForm('user/profile.html.twig', ['userForm' => $form,'user' => $user,]);
        
    }

    #[Route('/user/edit/{id}', name: 'app_user_edit')]
    public function editProfile(Request $req,UserRepository $rep,$id,EntityManagerInterface $em): Response
    {

        $user=$rep->find($id);
        $form=$this->createForm(UpdateUserFormType::class,$user);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid())
        { 
            $em->persist($user);
            $em->flush();
            return $this->render('user/userProfile.html.twig', [
                'user' => $user,
            ]);
        }
        return $this->renderForm('user/userUpdate.html.twig', ['userForm' => $form,'user' => $user,]);
        
    }


}
