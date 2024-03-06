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
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;


class ReponseController extends AbstractController
{
    #[Route('/addreponse/{id}', name: 'app_reponse')]
    public function add(Request $request, EntityManagerInterface $em, int $id, SupportTicketRepository $SuppR, UserRepository $u, MailerInterface $mailer): RP
    {
        $Ticket = $SuppR->findOneBy(['id' => $id]);
        $LesReponses= $Ticket->getResponses();
        $Response = new Response();
        $form = $this->createForm(ReponseType::class, $Response);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $choice = $request->get('choice');
            $Response->setUser($u->findOneBy(['id' => 3]));
            $Response->setSupportTicket($SuppR->findOneBy(['id' => $id]));
            $Response->setAddDate(new \DateTime());
            $Ticket->setState($choice);
            $em->persist($Response);
            $em->flush();
            $email = (new TemplatedEmail())
                ->from('dealdrop.pidev@outlook.com')
                ->to('mahdikhadher2001@gmail.com')
                ->subject('Ticket Updated')
                ->htmlTemplate('reponse/email.html.twig')
                ->context([
                    'Rep' => $Response,
                    'info' => $Ticket,
                ]);
            $mailer->send($email);
            $Ticket->addResponse($Response);
            return $this->redirectToRoute('app_ticketlistadmin');
        }
        return $this->render('reponse/edit.html.twig', [
            'form' => $form->createView(),
            'LesReponses' => $LesReponses,
            'Ticket' => $Ticket
        ]);
    }
    #[Route('/details/{id}', name: 'app_detailsticket')]
    public function details(Request $request, EntityManagerInterface $em, SupportTicketRepository $SuppR, int $id, UserRepository $u): RP
    {
        $Ticket = $SuppR->findOneBy(['id' => $id]);
        $LesReponses= $Ticket->getResponses();
        $Response = new Response();
        $form = $this->createForm(ReponseType::class, $Response);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $Response->setUser($u->findOneBy(['id' => 1]));
            $Response->setSupportTicket($SuppR->findOneBy(['id' => $id]));
            $Response->setAddDate(new \DateTime());
            $em->persist($Response);
            $em->flush();
            $Ticket->addResponse($Response);
            $Ticket->setState("Pending");
            $em->persist($Ticket);
            $em->flush();
            return $this->redirectToRoute('app_ticketlist');
        }
        return $this->render('reponse/details.html.twig', [
            'form' => $form->createView(),
            'LesReponses' => $LesReponses,
            'Ticket' => $Ticket
        ]);
    }
    #[Route('/detailsless/{id}', name: 'app_detailslessticket')]
    public function detailsless(Request $request, EntityManagerInterface $em, SupportTicketRepository $SuppR, int $id, UserRepository $u): RP
    {
        $Ticket = $SuppR->findOneBy(['id' => $id]);
        $LesReponses= $Ticket->getResponses();
        return $this->render('reponse/detailsless.html.twig', [
            'LesReponses' => $LesReponses,
            'Ticket' => $Ticket
        ]);
    }
    #[Route('/ticketlist/{id}', name: 'app_ticketlistid')]
    public function affiche(SupportTicketRepository $repository, $id, EntityManagerInterface $em): RP
    {
        $Ticket=$repository->findOneBy(['id' => $id]);
        $Ticket->setState("Closed");
        $em->persist($Ticket);
        $em->flush();
        $list=$repository->findAll();
        return $this->render('ticket/list.html.twig',[
            'list'=>$list
        ]);
    }
}
