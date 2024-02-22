<?php

namespace App\Controller;

use App\Form\ReponseType;
use App\Repository\SupportTicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as RP;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Response;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping\Id;

class ReponseController extends AbstractController
{
    #[Route('/addreponse/{id}', name: 'app_reponse')]
    public function edit(Request $request, EntityManagerInterface $em, int $id, SupportTicketRepository $SuppR, UserRepository $u): RP
    {
        $One = $SuppR->findOneBy(['id'=> $id]);
        $Response= new Response();
        $form=$this->createForm(ReponseType::class,$Response);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $choice = $request->get('choice');
            $Response->setUser($u->findOneBy(['id'=> 1]));
            $Response->setAddDate(new \DateTime());
            $Response->setSupportTicket($SuppR->findOneBy(['id'=> $id]));
            $One->setState($choice);
            $em->persist($Response);
            $em->flush();
            return $this->redirectToRoute('app_ticketlistadmin');
        }
        return $this->render('reponse/edit.html.twig',[
            'form'=>$form->createView(),
            'One' => $One
        ]);
    }
}
