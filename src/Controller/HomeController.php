<?php

namespace App\Controller;

use App\Entity\ImagePermit;
use App\Entity\Livreur;
use App\Entity\Membre;
use App\Entity\Users;
use App\Form\EditUserType;
use App\Form\LivreurFormType;
use App\Repository\LivreurRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $entityManager;
    

    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
        
    }
    
    #[Route('/home', name: 'app_home')]
    public function index(Request $req): Response
    {
        $user=$this->getUser();
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return $this->redirectToRoute('app_home_admin');
            dump('User has ROLE_ADMIN');

        }
        if (in_array('ROLE_DELIVERY_MAN', $user->getRoles(), true)) {
            return $this->render('Users/userHome.html.twig', [
                'user' => $user,
            ]);
            

        }
        
        return $this->render('Users/userHome.html.twig', [
            'user' => $user,
        ]);
        
    }
    #[Route('/user/edit/{id}', name: 'app_user_edit')]
    public function edit(Request $req,UsersRepository $rep,$id,EntityManagerInterface $em): Response
    {

        $user=$rep->find($id);
        $form=$this->createForm(EditUserType::class,$user);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid())
        { 
            $em->persist($user);
            $em->flush();
            return $this->render('Users/userHome.html.twig', [
                'user' => $user,
            ]);
        }
        return $this->renderForm('Users/userEdit.html.twig', ['form' => $form,'user' => $user,]);
        
    }

}
