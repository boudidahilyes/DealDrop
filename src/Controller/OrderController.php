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
use App\Entity\Delivery;
use App\Entity\Order;

class OrderController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/productForSale/order/{id}', name: 'app_product_for_sale_order')]
    public function orderProductForSale(Request $req,$id,ProductForSaleRepository $rep,prdcon $prdcon): Response
    {
        $product=$this->entityManager->getRepository(ProductForSale::class)->findOneBy(['id' => $id]);
        $member = $this->getUser();
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
            
            $delivery = new Delivery($order, $req->get('order_form')["coordinates"]);
            $this->entityManager->persist($delivery);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_product_for_sale');
        }
        return $this->render('order/frontOfficeProductForSaleOrder.html.twig',[
            'form' => $form->createView(),
            'product'=> $product]);
    }
    #[Route('/account/orders', name: 'app_product_for_sale_orders')]
    public function ProductForSaleOrders(Request $req,prdcon $prdcon): Response
    {
        $member=$this->getUser();
        $orders = $this->entityManager->getRepository(ProductForSale::class)->findBy(['member' => $member]);
        return $this->render('order/frontOfficeProductForSaleOrders.html.twig', [
            'orders' => $orders
        ]);
    }
}
