<?php

namespace App\Controller;

use App\Entity\Auction;
use App\Entity\Bid;
use App\Entity\Member;
use App\Form\BidFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class BidController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

   /* #[Route('/bid', name: 'app_bid')]
    public function index(): Response
    {       

        return $this->render('bid/index.html.twig', [
            'bidForm' => 'bidder'
        ]);
    } 
#[Route('/place_bid/{id}', name: 'place_bid')]
public function placeBid(Request $request, int $id): Response
{
    

    // Fetch the Auction entity manually
    $auction = $this->entityManager->getRepository(Auction::class)->find($id);

    if (!$auction) {
        throw $this->createNotFoundException('Auction not found');
    }

    $bidder = $this->entityManager->getRepository(Member::class)->findOneBy(['id' => 1]);
    $bid = new Bid();
    
    $form = $this->createForm(BidFormType::class, $bid);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $bid->setbidDate(new \DateTimeImmutable());
        $bid->setState('mriguel');
        $bid->setAuction($auction);
        $bid->setBidder($bidder);
        $this->entityManager->persist($bid);
        $this->entityManager->flush();
        
        // $this->addFlash('success', 'Bid placed successfully.');
        return $this->redirectToRoute('place_bid', ['id' => $auction->getId()]);
    }

    return $this->render('Auction/front_single_auction.html.twig', [
        'bidForm' => $form->createView(),
        'auction' => $auction,
    ]);
}

    #[Route('/fetch_bid/{id}', name: 'fetch_bid')]
    public function fetchBids(int $id): Response
    { 
           $auction = $this->entityManager->getRepository(Auction::class)->findOneBy(['id' => 1]);

        if (!$auction) {
            throw $this->createNotFoundException('Auction not found');
        }
        
        $bids =$this->entityManager->getRepository(Bid::class)->findBy(['auction' => $auction]);

        return $this->render('Auction/front_single_auction.html.twig', [
            'auction' => $auction,
            'bids' => $bids,
        ]);
    }*/


}

