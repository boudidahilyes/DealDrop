<?php

namespace App\Controller;

use App\Form\TicketCategoryType;
use App\Entity\SupportTicketCategory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SupportTicketCategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;


class TicketCategoryController extends AbstractController
{
    #[Route('/backoffice', name: 'app_ticket_category')]
    public function index(): Response
    {
        return $this->render('baseBackOffice.html.twig');
    }
    #[Route('/ticketcategorylist', name: 'app_ticketcategorylist')]
    public function affiche(SupportTicketCategoryRepository $repository,Request $request, EntityManagerInterface $em): Response
    {
        $list=$repository->findAll();
        $SuppTicket= new SupportTicketCategory();
        $form=$this->createForm(TicketCategoryType::class,$SuppTicket);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em->persist($SuppTicket);
            $em->flush();
            return $this->redirectToRoute('app_ticketcategorylist');
        }
        return $this->render('ticket_category/list.html.twig',[
            'list'=>$list,
            'form'=>$form->createView()
        ]);
    }
    #[Route('/ticketcategorylist/editcat/{id}', name: 'app_editcat')]
    public function edit(Request $request, EntityManagerInterface $em, SupportTicketCategoryRepository $rep, int $id): Response
    {
        $SuppTicket= new SupportTicketCategory();
        $SuppTicket=$rep->find($id);
        $form=$this->createForm(TicketCategoryType::class,$SuppTicket);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em->persist($SuppTicket);
            $em->flush();
            return $this->redirectToRoute('app_ticketcategorylist');
        }
        return $this->render('ticket_category/edit.html.twig',[
            'form'=>$form->createView()
        ]);
    }
    #[Route('/deletecat/{id}', name: 'app_deletecat')]
    public function delete(EntityManagerInterface $em, SupportTicketCategoryRepository $rep, int $id): Response
    {
        $SuppTicket= new SupportTicketCategory();
        $SuppTicket=$rep->find($id);
        $em->remove($SuppTicket);
        $em->flush();
            return $this->redirectToRoute('app_ticketcategorylist');
        }
}
