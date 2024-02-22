<?php

namespace App\Controller;

use App\Entity\Auction;
use App\Entity\Bid;
use App\Entity\Member;
use App\Entity\Order;
use App\Entity\ProductImage;
use App\Entity\User;
use App\Form\AuctionFormType;
use App\Form\AuctionType;
use App\Form\BidFormType;
use App\Form\OrderFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function Symfony\Component\Clock\now;


class AuctionController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /////////////////////////////////////////////////////////////////////////Front Auction////////////////////////////////////////////////////////////////////////////////
    #[Route('/Auction/front_single_auction{id}', name: 'app_front_single_auction')]
    public function frontSingleAuction(Request $request, int $id): Response
    {
        $auction = $this->entityManager->getRepository(Auction::class)->findOneBy(['id' => $id]);

        if (!$auction) {
            throw $this->createNotFoundException('Auction not found');
        }
        $bidder = $this->entityManager->getRepository(Member::class)->findOneBy(['id' => 1]);

        $bid = $this->entityManager->getRepository(Bid::class)->findOneBy(['auction' => $auction, 'bidder' => $bidder]);
        if (is_null($bid)) {
            $bid = new Bid();
        }
        $form = $this->createForm(BidFormType::class, $bid);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bid->setbidDate(new \DateTimeImmutable());
            $bid->setState('Valid');
            $bid->setAuction($auction);
            $bid->setBidder($bidder);
            $bid = $form->getData();
            if ($auction->getHighestBid() === null) {
                $newHighestBid = new Bid();
                $auction->setHighestBid($newHighestBid);
            }
            $auction->setHighestBid($bid->getValue());
            $this->entityManager->persist($bid);
            $this->entityManager->persist($auction);
            $this->entityManager->flush();
              
            return $this->redirectToRoute('app_front_single_auction', ['id' => $auction->getId()]);
        }
        $bids = $this->entityManager->getRepository(Bid::class)->findBy(['auction' => $auction]);
        $now= new \DateTimeImmutable();
        if($auction->getEndDate() <= $now)
        {     
            $highestBid =$this->entityManager->getRepository(Bid::class)->findOneBy(['value'=>$auction->getHighestBid(),'auction'=>$auction]);
            $member = $highestBid->getBidder();
           
            $order = new Order();
            $form = $this->createForm(OrderFormType::class, $order);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                $order->setMember($member);
                $order->setProduct($auction);
                $order->setOrderDate(new \DateTimeImmutable());
                $this->entityManager->persist($order);
                $this->entityManager->flush();
                return $this->redirectToRoute('app_front_auction_list');
            }
            return $this->render('order/frontAuctionOrder.html.twig', [
                'form' => $form->createView(),
                'product' => $auction
            ]);}
        return $this->render('Auction/front_single_auction.html.twig', [
            'auction' => $auction,
            'bidForm' => $form->createView(),
            'bids' => $bids,
            'bid2' => $bid,
            'bidder' => $bidder
        ]);
    }


    #[Route('/Auction/frontUserAuctionList', name: 'app_front_user_added_auctions')]
    public function userAuctions(): Response
    {
        $user = $this->entityManager->getRepository(Member::class)->findOneBy(['id' => 1]);
        $Product = $this->entityManager->getRepository(Auction::class)->findBy(['owner' => $user]);
        return $this->render('Auction/frontUserAddedAuctions.html.twig', [
            'auctions' => $Product
        ]);
    }


    #[Route('/Auction/frontAuctionList', name: 'app_front_auction_list')]
    public function frontListAuction(): Response
    {
        $listAuction = $this->entityManager->getRepository(Auction::class)->findAll();

        return $this->render('Auction/front_list_auction.html.twig', [
            'listAuction' => $listAuction,

        ]);
    }



    #[Route('/Auction/add', name: 'app_auction')]
    public function addAuction(Request $req): Response
    {
        $member = $this->entityManager->getRepository(Member::class)->findOneBy(['id' => 2]);
        $au = new Auction();
        $form = $this->createForm(AuctionFormType::class, $au);

        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $au->setAddDate(new \DateTimeImmutable());
            $au->setMember($member);
            $au->setHighestBid(0);
          
            $au->setStatus('Pending');
            $ProductImages = $form->get("productImage")->getData();
            foreach ($ProductImages as $img) {
                $ProductImage = new ProductImage($img->getClientOriginalName());
                $ProductImage->setImageFile($img);
                $ProductImage->setProduct($au);
                $this->entityManager->persist($ProductImage);
                $this->entityManager->flush();
            }
            $this->entityManager->persist($au);
            $this->entityManager->flush();
            
            return $this->redirectToRoute('app_front_user_added_auctions');
        }
        return $this->render('Auction/add.html.twig', ['form' => $form->createView()]);
    }

    
    



    #[Route('/Auction/update/{id}', name: 'app_front_update_Auction')]
    public function editProductForSale($id, Request $req): Response
    {
        $au = $this->entityManager->getRepository(Auction::class)->findOneBy(['id' => $id]);
        $form = $this->createForm(AuctionFormType::class, $au);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $au->setAddDate(new \DateTimeImmutable());
            $au->setStatus('Pending');

            $ProductImages = $form->get("productImage")->getData();
            if ($ProductImages != null) {
                foreach ($au->getProductImages() as $productImg) {
                    $this->entityManager->remove($productImg);
                    $this->entityManager->flush();
                }
                $au->getProductImages()->clear();
                foreach ($ProductImages as $img) {
                    $ProductImage = new ProductImage($img->getClientOriginalName());
                    $ProductImage->setImageFile($img);
                    $ProductImage->setProduct($au);
                    $this->entityManager->persist($ProductImage);
                    $this->entityManager->flush();
                }
            }
            $this->entityManager->persist($au);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_front_user_added_auctions');
        }
        return $this->render('Auction/add.html.twig', ['form' => $form->createView()]);
    }

    ///////////////////////////////////////////////////////////////////////////////////back////////////////////////////////////////////////////////////
    #[Route('/backAuctionList', name: 'app_back_auction_list')]
    public function back_list(): Response
    {
        $listAuction = $this->entityManager->getRepository(Auction::class)->findAll();


        $resultCount = count($listAuction);
        return $this->render('Auction/backListAuction.html.twig', [
            'listAuction' => $listAuction,
            'resultCount' => $resultCount

        ]);
    }
    #[Route('/Auction/back_single_auction{id}', name: 'app_back_single_auction')]
    public function backSingleAuction($id): Response
    {


        $auction = $this->entityManager->getRepository(Auction::class)->findOneBy(['id' => $id]);
        $bids = $this->entityManager->getRepository(Bid::class)->findBy(['auction' => $auction]);
        return $this->render('Auction/backSingleAuction.html.twig', [
            'auction' => $auction,
            'bids' => $bids

        ]);
    }
    #[Route('/back/approved{id}', name: 'app_auction_accepted')]
    public function auctionAccepted($id): Response
    {
        $auction = $this->entityManager->getRepository(Auction::class)->findOneBy(['id' => $id]);
        if ($auction != null) {
            $auction->setStatus("Approved");
            $this->entityManager->persist($auction);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('app_back_auction_list');
    }

    #[Route('/back/declineAuction{id}', name: 'app_auction_declined')]
    public function auctionDeclined($id): Response
    {
        $auction = $this->entityManager->getRepository(Auction::class)->findOneBy(['id' => $id]);
        if ($auction != null) {
            $auction->setStatus("Declined");
            $this->entityManager->persist($auction);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('app_back_auction_list');
    }


    #[Route('/delete/back_single_auction{id}/{source}', name: 'app_auction_deleted')]
    public function deleteAuction($id, $source): Response
    {       

        try {
            $auction = $this->entityManager->getRepository(Auction::class)->find($id);

            if (!$auction) {
                $this->addFlash('error', 'Auction not found.');
            } else {
                $this->entityManager->remove($auction);
                $this->entityManager->flush();
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred: ' . $e->getMessage());
        }
        if ($source == 'user') {
            return $this->redirectToRoute('app_front_user_added_auctions');
        } else {
            return $this->redirectToRoute('app_back_auction_list');
        }
    }
}
