<?php

namespace App\Controller;

use App\Entity\Member;
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
use App\Entity\Order;
use App\Entity\ProductForRent;
use App\Form\OrderForRentFormType;
use App\Repository\ProductForRentRepository;

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
        $product = $this->entityManager->getRepository(ProductForSale::class)->findOneBy(['id' => $id]);
        $member = $this->entityManager->getRepository(Member::class)->findOneBy(['id' => $prdcon->getCookieID($req)]);
        $order = new Order();
        $form = $this->createForm(OrderFormType::class, $order);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $order->setMember($member);
            $order->setProduct($product);
            $order->setOrderDate(new \DateTimeImmutable());
            $this->entityManager->persist($order);
            $this->entityManager->flush();
            $rep->setStatusSold($id);
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
        $member = $this->entityManager->getRepository(Member::class)->findOneBy(['id' => $prdcon->getCookieID($req)]);
        $order = new Order();
        $form = $this->createForm(OrderForRentFormType::class, $order);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $order->setMember($member);
            $order->setProduct($product);
            $order->setOrderDate(new \DateTimeImmutable());
            $this->entityManager->persist($order);
            $this->entityManager->flush();
            $rep->setAvailabilityUnavailable($id);
            return $this->redirectToRoute('app_product_for_rent');
        }
        return $this->render('order/frontOfficeProductForRentOrder.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }
    ##Order productForRent End ##
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
        $member = $this->entityManager->getRepository(Member::class)->findOneBy(['id' => $prdcon->getCookieID($req)]);
        $orders = $this->entityManager->getRepository(order::class)->findBy(['member' => $member]);
        return $this->render('order/frontOfficeProductForSaleOrders.html.twig', [
            'orders' => $orders
        ]);
    }
    ## FrontOffice End ##
}
