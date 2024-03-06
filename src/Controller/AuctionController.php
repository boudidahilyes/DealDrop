<?php

namespace App\Controller;

use App\Entity\Auction;
use App\Entity\Bid;
use App\Entity\Member;
use App\Entity\Order;
use App\Entity\ProductImage;
use App\Entity\Reminder;
use App\Entity\User;
use App\Form\AuctionFormType;
use App\Form\AuctionType;
use App\Form\BidFormType;
use App\Form\OrderFormType;
use App\Repository\AuctionRepository;
use App\Repository\MemberRepository;
use App\Repository\ReminderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twilio\Rest\Client;

use function Symfony\Component\Clock\now;


class AuctionController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    
    /* #[Route('/login/success')]
    public function loginSuccess(TexterInterface $texter)
    {
        $sms = new SmsMessage(
            '+21626852727',
            
            'A new login was detected!'
        );
         $texter->send($sms);
       

    }*/

    /////////////////////////////////////////////////////////////////////////Front Auction////////////////////////////////////////////////////////////////////////////////
    #[Route('/Auction/front_single_auction{id}', name: 'app_front_single_auction')]
    public function frontSingleAuction(Request $request, int $id): Response
    {
        $auction = $this->entityManager->getRepository(Auction::class)->findOneBy(['id' => $id]);

        if (!$auction) {
            throw $this->createNotFoundException('Auction not found');
        }
        $bidder = $this->getUser();

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


            $auction->setHighestBid($bid);
            $this->entityManager->persist($bid);
            $this->entityManager->persist($auction);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_front_single_auction', ['id' => $auction->getId()]);
        }
        $bids = $this->entityManager->getRepository(Bid::class)->findBy(['auction' => $auction]);
        $now = new \DateTimeImmutable();
        if ($auction->getEndDate() <= $now) {

            if ($auction->hightestBid != null) {
                ///////////////////big prob//////////
                $member = $auction->hightestBid->getBidder();
                /////////////////here/////////////////////
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
                ]);
            } else {
                return $this->redirectToRoute('app_front_auction_list');
            }
        }
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
        $user = $this->getUser();
        $Product = $this->entityManager->getRepository(Auction::class)->findBy(['owner' => $user]);
        return $this->render('Auction/frontUserAddedAuctions.html.twig', [
            'auctions' => $Product
        ]);
    }
    #[Route('/Auction/frontParticipatedAuctions', name: 'app_front_user_participated_auctions')]
    public function userParticipatedAuctions(): Response
    {
        $user = $this->getUser();

        $bids = $this->entityManager->getRepository(Bid::class)->findBy(['bidder' => $user]);

        // Extract the auctions from the bids using Bid as the owning side
        $Product = [];
        foreach ($bids as $bid) {
            $auction = $bid->getAuction();
            if ($auction) {
                $Product[] = $auction;
            }
        }

        return $this->render('Auction/frontParticipatedAuctions.html.twig', [
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
        $member = $this->getUser();
        $au = new Auction();
        $form = $this->createForm(AuctionFormType::class, $au);

        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $au->setAddDate(new \DateTimeImmutable());
            $au->setMember($member);
            $bid = new Bid();
            $bid->setValue($au->getCurrentPrice());
            $au->setHighestBid($bid);
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
            $reminder = new Reminder($au);
            $this->entityManager->persist($reminder);
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


    #[Route('/delete/back_single_auction/{id}/{source}', name: 'app_auction_deleted')]
    public function deleteAuction($id, $source): Response
    {

       
            $auction = $this->entityManager->getRepository(Auction::class)->find($id);
            $hb = $auction->getHighestBid();
     
            $auction->setHighestBid(null);
            $this->entityManager->persist($auction);
            $this->entityManager->flush();

            

            $this->entityManager->remove($auction);
            $this->entityManager->flush();

            $this->entityManager->remove($hb);
            $this->entityManager->flush();
            
       
        if ($source == 'user') {
            return $this->redirectToRoute('app_front_user_added_auctions');
        } else {
            return $this->redirectToRoute('app_back_auction_list');
        }
    }


    #[Route('/auction/set_reminder', name: 'app_set_reminder')]
    public function setReminder(Request $request, ReminderRepository $rep): JsonResponse
    {
        $member = $this->getUser();
        $reminder = $rep->findOneBy(['auction' => $request->get('id')]);
        $reminder->addMember($member);
        $this->entityManager->persist($reminder);
        $this->entityManager->flush();
        

        return new JsonResponse(['message' => 'Rating updated successfully']);
    }
}
