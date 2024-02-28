<?php

namespace App\Controller;

use App\Entity\SupportTicket;
use App\Entity\User;
use App\Form\ReponseType;
use App\Form\TicketType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response as RP;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SupportTicketRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Response;



class TicketController extends AbstractController
{
    #[Route('/basefront', name: 'app_basefront')]
    public function index(): RP
    {
        return $this->render('baseFrontOffice.html.twig');
    }

    #[Route('/baseback', name: 'app_baseback')]
    public function baseback(): RP
    {
        return $this->render('baseBackOffice.html.twig');
    }

    #[Route('/ticketlistadmin', name: 'app_ticketlistadmin')]
    public function afficheadmin(SupportTicketRepository $repository): RP
    {
        $list=$repository->findAll();
        return $this->render('ticket/listadmin.html.twig',[
            'list'=>$list
        ]);
    }

    #[Route('/ticketlist', name: 'app_ticketlist')]
    public function affiche(SupportTicketRepository $repository): RP
    {
        $list=$repository->findAll();
        return $this->render('ticket/list.html.twig',[
            'list'=>$list
        ]);
    }
    #[Route('/addticket', name: 'app_addticket')]
    public function add(Request $request, EntityManagerInterface $em, UserRepository $u): RP
    {
        $SuppTicket= new SupportTicket();
        $form=$this->createForm(TicketType::class,$SuppTicket);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $SuppTicket->setUser($u->findOneBy(['id'=> 1]));
            $SuppTicket->setState('Pending');
            $SuppTicket->setCreationDate(new \DateTime());
            $em->persist($SuppTicket);
            $em->flush();
            return $this->redirectToRoute('app_ticketlist');
        }
        return $this->render('ticket/add.html.twig',[
            'form'=>$form->createView()
        ]);
    }
    
    #[Route('/delete/{id}', name: 'app_deleteticket')]
    public function delete(EntityManagerInterface $em, SupportTicketRepository $rep, int $id): RP
    {
        $SuppTicket= new SupportTicket();
        $SuppTicket=$rep->find($id);
        $em->remove($SuppTicket);
        $em->flush();
            return $this->redirectToRoute('app_ticketlist');
        }
}
