<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\Auction;
use App\Entity\ProductForSale;
use App\Form\OrderFormType;
use App\Repository\ProductForSaleRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\ProductForSaleController as prdcon;
use App\Entity\Delivery;
use App\Entity\Offer;
use App\Entity\Order;
use App\Entity\ProductForRent;
use App\Entity\ProductForTrade;
use App\Form\OrderForRentFormType;
use App\Repository\ProductForRentRepository;
use App\Repository\ProductForTradeRepository;

class OrderController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    ##Order productForSale Begin ##
    #[Route('/productForSale/order/{id}', name: 'app_product_for_sale_order')]
    public function orderProductForSale(Request $req, $id, ProductForSaleRepository $rep, prdcon $prdcon): Response
    {
        $product=$this->entityManager->getRepository(ProductForSale::class)->findOneBy(['id' => $id]);
        $member = $this->getUser();
        $order = new Order();
        $form = $this->createForm(OrderFormType::class, $order);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {
            $order->setMember($member);
            $order->setProducts($product);
            $order->setOrderDate(new \DateTimeImmutable());
            if($form->get('paymentMethod')->getData() == 'bank')
            {
                $order->setPayment('bank');
                $this->entityManager->persist($order);
                $this->entityManager->flush();
                $delivery = new Delivery($order, $req->get('order_form')["coordinates"]);
                $this->entityManager->persist($delivery);
                $this->entityManager->flush();
                $rep->setStatusSold($id);
                return $this->redirectToRoute('app_stripe_sale',['price'=>$product->getPrice()+8]);
            }
            else 
            {
                $order->setPayment('onDelivery');
                $this->entityManager->persist($order);
                $this->entityManager->flush();
                $delivery = new Delivery($order, $req->get('order_form')["coordinates"]);
                $this->entityManager->persist($delivery);
                $this->entityManager->flush();
                $rep->setStatusSold($id);
                 return $this->redirectToRoute('app_product_for_sale');
            }

            return $this->redirectToRoute('app_product_for_sale');
        }
        return $this->render('order/frontOfficeProductForSaleOrder.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }

    ##Order productForSale End ##
    ##Order productForRent Begin ##
    #[Route('/productForRent/order/{id}', name: 'app_product_for_rent_order')]
    public function orderProductForRent(Request $req, $id, ProductForRentRepository $rep, prdcon $prdcon): Response
    {
        $product = $this->entityManager->getRepository(ProductForRent::class)->findOneBy(['id' => $id]);
        $member=$this->getUser();
        $order = new Order();
        $form = $this->createForm(OrderForRentFormType::class, $order);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $order->setMember($member);
            $order->setProducts($product);
            $order->setOrderDate(new \DateTimeImmutable());
            if($form->get('paymentMethod')->getData() == 'bank')
            {
                $order->setPayment('bank');
                $this->entityManager->persist($order);
                $this->entityManager->flush();
                $rep->setAvailabilityUnavailable($id);
                return $this->redirectToRoute('app_stripe_rent',['price'=>$product->getPricePerDay()*$order->getRentDays()+8]);
            }
            else 
            {
                $order->setPayment('onDelivery');
                $this->entityManager->persist($order);
                $this->entityManager->flush();
                $rep->setAvailabilityUnavailable($id);
            return $this->redirectToRoute('app_product_for_rent');
            }
        }
        return $this->render('order/frontOfficeProductForRentOrder.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }
    ## Order productForRent End ##
        ##Order productForTrade Begin ##
    #[Route('/productForTrade/order', name: 'app_product_for_trade_order')]
    public function orderProductForTrade(Request $req,ProductForTradeRepository $productForTradeRepository):Response
    {
        $offer=$this->entityManager->getRepository(Offer::class)->findOneBy(['id'=>$req->get('offerId')]);
        $productOffered=$this->entityManager->getRepository(ProductForTrade::class)->findOneBy(['id'=>$offer->getProductOffered()->getId()]);
        $productPosted=$this->entityManager->getRepository(ProductForTrade::class)->findOneBy(['id'=>$offer->getProductPosted()->getId()]);
        //order for Product Posted
        $orderForProductPosted=new Order();
        $orderForProductPosted->setMember($productPosted->getMember());
        $orderForProductPosted->setProducts($productPosted);
        $orderForProductPosted->setOrderDate(new \DateTimeImmutable());
        $orderForProductPosted->setDeliveryAdress($productPosted->getMember()->getAdress());
        $productForTradeRepository->setStatusSold($productPosted->getId());
        $this->entityManager->persist($orderForProductPosted);
        $this->entityManager->flush();
        //order for Product Accepted
        $orderForProductOffered=new Order();
        $orderForProductOffered->setMember($productOffered->getMember());
        $orderForProductOffered->setProducts($productOffered);
        $orderForProductOffered->setOrderDate(new \DateTimeImmutable());
        $orderForProductOffered->setDeliveryAdress($productOffered->getMember()->getAdress());
        $productForTradeRepository->setStatusSold($productOffered->getId());
        $this->entityManager->persist($orderForProductOffered);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_product_for_trade_profil');
    }
            ##Order productForTrade End ##
    ## BackOffice Begin ##
    #[Route('/homeDashboard/orders', name: 'app_orders_list')]
    public function listOrders(): Response
    {
        $orders = $this->entityManager->getRepository(order::class)->findAll();
        return $this->render('order/backOfficeOrders.html.twig', [
            'orders' => $orders
        ]);
    }
    #[Route('/homeDashboard/orders/remove/{id}', name: 'app_order_remove')]
    public function removeOrder($id): Response
    {
        $order = $this->entityManager->getRepository(order::class)->findOneBy(['id' => $id]);
        $this->entityManager->remove($order);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_orders_list');
    }
    #[Route('/homeDashboard/orders/Overview/{id}', name: 'app_order_overview')]
    public function orderOverview($id): Response
    {
        $order = $this->entityManager->getRepository(order::class)->findOneBy(['id' => $id]);
        return $this->render('order/backOfficeOrderOverView.html.twig', [
            'order' => $order
        ]);
    }
    ## BackOffice End ##
    ## FrontOffice Begin ##

    #[Route('/account/orders', name: 'app_product_for_sale_orders')]
    public function ProductForSaleOrders(Request $req, prdcon $prdcon): Response
    {
        $member=$this->getUser();
        $orders = $this->entityManager->getRepository(order::class)->findBy(['member' => $member]);
        return $this->render('order/frontOfficeProductForSaleOrders.html.twig', [
            'orders' => $orders
        ]);
    }
    ## FrontOffice End ##
}
