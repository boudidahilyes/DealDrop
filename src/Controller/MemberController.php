<?php

namespace App\Controller;

use App\Entity\Member;
use App\Form\MemberRegistrationFormType;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class MemberController extends AbstractController
{
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
            $entityManager->persist($user);
            $entityManager->flush();


            return $this->redirectToRoute('app_home');
        }

        return $this->render('member/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
