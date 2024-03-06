<?php

namespace App\Controller;

use App\Form\TicketType;
use App\Entity\SupportTicket;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use App\Entity\SupportTicketCategory;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SupportTicketRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SupportTicketCategoryRepository;
use Symfony\Component\HttpFoundation\Response as RP;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function afficheadmin(SupportTicketRepository $repository, PaginatorInterface $paginator, Request $request): RP
    {
        $State = $request->query->get('SortState');
        $list = $repository->findAll();
        //test push
        if ($State) {
            $list = $repository->findBy(['state' => $State]);
        }
        $pagination = $paginator->paginate(
            $list, 
            $request->query->getInt('page', 1),
            7
        );
        return $this->render('ticket/listadmin.html.twig',[
            'pagination' => $pagination,
            'selectedState' => $State,
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
    public function add(Request $request, EntityManagerInterface $em, UserRepository $u, MailerInterface $mailer): RP
    {
        $SuppTicket= new SupportTicket();
        $form=$this->createForm(TicketType::class,$SuppTicket);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $content = $SuppTicket->getDescription();
            $cleanedContenu = \ConsoleTVs\Profanity\Builder::blocker($content)->filter();
            $SuppTicket->setDescription($cleanedContenu);
            $SuppTicket->setUser($u->findOneBy(['id'=> 1]));
            $SuppTicket->setState('Pending');
            $SuppTicket->setCreationDate(new \DateTime());
            $em->persist($SuppTicket);
            $em->flush();
            $email = (new TemplatedEmail())
                ->from('dealdrop.pidev@outlook.com')
                ->to('mahdikhadher2001@gmail.com')
                ->subject('Ticket Created')
                ->htmlTemplate('ticket/email.html.twig')
                ->context([
                    'info' => $SuppTicket,
                ]);
            $mailer->send($email);
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
    #[Route('/Statistics', name: 'app_stats')]
        public function DisplayStats(SupportTicketRepository $repository, CategoryRepository $u): RP
        {
            $a=count($repository->findAll());
            $CatPro =count($repository->findBy(['supportTicketCategory' => $u->findBy(['name' => "Product"])]));
            $CatDel =count($repository->findBy(['supportTicketCategory' => $u->findBy(['name' => "Delivery"])]));
            $CatWeb =count($repository->findBy(['supportTicketCategory' => $u->findBy(['name' => "Website"])]));
            $CatAcc =count($repository->findBy(['supportTicketCategory' => $u->findBy(['name' => "Account"])]));
            $StatePen =count($repository->findBy(['state' => "Pending"]));
            $StateRes =count($repository->findBy(['state' => "Resolved"]));
            $StateRej =count($repository->findBy(['state' => "Rejected"]));
            $StateClo =count($repository->findBy(['state' => "Closed"]));
            return $this->render('ticket/statistics.html.twig',[
                'StatePen' => $StatePen,
                'StateRes' => $StateRes,
                'StateRej' => $StateRej,
                'StateClo' => $StateClo,
                'CatPro' => $CatPro,
                'CatDel' => $CatDel,
                'CatWeb' => $CatWeb,
                'CatAcc' => $CatAcc,
                'total' => $a,
            ]);
        }
}
