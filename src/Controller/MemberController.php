<?php

namespace App\Controller;

use App\Entity\Member;
use App\Form\MemberRegistrationFormType;
use App\Repository\MemberRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class MemberController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }
    #[Route('/member', name: 'app_member')]
    public function index(): Response
    {
        return $this->render('member/index.html.twig', [
            'controller_name' => 'MemberController',
        ]);
    }

    #[Route('/register', name: 'app_member_register')]
    public function register(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, MemberRepository $rep, Request $request): Response
    {
        $user = new Member();
        $form = $this->createForm(MemberRegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setRoles(["ROLE_MEMBER"]);
            $user->setIsVerified(false);
            $user->setJoiningDate(new \DateTime());   
            $entityManager->persist($user);
            $entityManager->flush();
            
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from('dealdrop.pidev@outlook.com')
                    ->to(new Address($user->getEmail()))
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );


            return $this->redirectToRoute('app_home');
        }

        return $this->render('member/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_home');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_home');
    }
}
