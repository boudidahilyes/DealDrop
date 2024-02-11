<?php

namespace App\Controller;

use App\Entity\ImagePermit;
use App\Entity\Livreur;
use App\Entity\Membre;
use App\Form\LivreurFormType;
use App\Repository\LivreurRepository;
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
        

        return $this->render('home/index.html.twig', [
            
        ]);
        
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
}
