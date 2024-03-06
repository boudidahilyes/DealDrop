<?php

namespace App\Controller;

use App\Entity\ImagePermit;
use App\Entity\Livreur;
use App\Entity\Membre;
use App\Entity\User;
use App\Form\LivreurFormType;
use App\Repository\LivreurRepository;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
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
        if ($user instanceof User) {
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return $this->redirectToRoute('app_home_admin');

        }
        if (in_array('ROLE_DELIVERY_MAN', $user->getRoles(), true)) {
            return $this->render('user/userProfile.html.twig', [
                'user' => $user,
            ]);
            

        }
        
        return $this->render('user/userProfile.html.twig', [
            'user' => $user,
        ]);
    }

    return $this->redirectToRoute('app_login');
        
    }
    #[Route('/homeDashboard', name: 'app_home_Dashboard')]
    public function indexDashboard(): Response
    {
        

        return $this->render('baseBackOffice.html.twig');
        
    }
        
}
    
/*
    #[Route('/livreurs', name: 'listlivreur')]
    public function listLivreur(LivreurRepository $rep): Response
    {
        $livreurs=$rep->findAll();
        
        return $this->render('livreur/ListLivreur.html.twig', [
            'livreurs' => $livreurs,
        ]);
    }*/
