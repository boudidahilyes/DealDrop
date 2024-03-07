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
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\Builder\BuilderInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpKernel\KernelInterface;

class OrderController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    ##Order productForSale Begin ##
    #[Route('/productForSale/order/{id}', name: 'app_product_for_sale_order')]
    public function orderProductForSale(Request $req, $id, ProductForSaleRepository $rep, MailerInterface $mailer,BuilderInterface $customQrCodeBuilder): Response
    {
        $product = $this->entityManager->getRepository(ProductForSale::class)->findOneBy(['id' => $id]);
        $member = $this->getUser();
        $order = new Order();
        $form = $this->createForm(OrderFormType::class, $order);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {
            $order->setMember($member);
            $order->setProducts($product);
            $order->setOrderDate(new \DateTimeImmutable());
            if ($form->get('paymentMethod')->getData() == 'bank') {
                $order->setPayment('bank');
                $this->entityManager->persist($order);
                $this->entityManager->flush();
                $delivery = new Delivery($order, $req->get('order_form')["coordinates"]);
                $this->entityManager->persist($delivery);
                $this->entityManager->flush();
                $rep->setStatusSold($id);
                $this->sendEmailWithPDF($member,$mailer,$product, $order,$customQrCodeBuilder);
                return $this->render('stripe/index.html.twig', [
                    'stripe_key' => $_ENV["STRIPE_KEY"],
                    'price' => $product->getPrice() + 8,
                    'type' => $product->WhoIAm()
                ]);
            } else {
                $order->setPayment('onDelivery');
                $this->entityManager->persist($order);
                $this->entityManager->flush();
                $delivery = new Delivery($order, $req->get('order_form')["coordinates"]);
                $this->entityManager->persist($delivery);
                $this->entityManager->flush();
                $this->sendEmailWithPDF($member,$mailer,$product, $order,$customQrCodeBuilder);
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
    public function orderProductForRent(Request $req, $id, ProductForRentRepository $rep, MailerInterface $mailer,BuilderInterface $customQrCodeBuilder): Response
    {
        $product = $this->entityManager->getRepository(ProductForRent::class)->findOneBy(['id' => $id]);
        $member = $this->getUser();
        $order = new Order();
        $form = $this->createForm(OrderForRentFormType::class, $order);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $order->setMember($member);
            $order->setProducts($product);
            $order->setOrderDate(new \DateTimeImmutable());
            if ($form->get('paymentMethod')->getData() == 'bank') {
                $order->setPayment('bank');
                $this->entityManager->persist($order);
                $this->entityManager->flush();
                $delivery = new Delivery($order, $req->get('order_for_rent_form')["coordinates"]);
                $this->entityManager->persist($delivery);
                $this->entityManager->flush();
                $this->sendEmailWithPDF($member,$mailer,$product, $order,$customQrCodeBuilder);
                $rep->setAvailabilityUnavailable($id);
                return $this->render('stripe/index.html.twig', [
                    'stripe_key' => $_ENV["STRIPE_KEY"],
                    'price' => $product->getPricePerDay() * $order->getRentDays() + 8,
                    'type' => $product->WhoIAm()
                ]);
            } 
            else {
                $order->setPayment('onDelivery');
                $this->entityManager->persist($order);
                $this->entityManager->flush();
                $delivery = new Delivery($order, $req->get('order_for_rent_form')["coordinates"]);
                $this->entityManager->persist($delivery);
                $this->entityManager->flush();
                $this->sendEmailWithPDF($member, $mailer,$product, $order,$customQrCodeBuilder);
                $rep->setAvailabilityUnavailable($id);
                return $this->redirectToRoute('app_product_for_rent');
            }
            return $this->redirectToRoute('app_product_for_rent');
        }
        return $this->render('order/frontOfficeProductForRentOrder.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }
    ## Order productForRent End ##
    ##Order productForTrade Begin ##
    #[Route('/productForTrade/order', name: 'app_product_for_trade_order')]
    public function orderProductForTrade(Request $req, ProductForTradeRepository $productForTradeRepository): Response
    {
        $offer = $this->entityManager->getRepository(Offer::class)->findOneBy(['id' => $req->get('offerId')]);
        $productOffered = $this->entityManager->getRepository(ProductForTrade::class)->findOneBy(['id' => $offer->getProductOffered()->getId()]);
        $productPosted = $this->entityManager->getRepository(ProductForTrade::class)->findOneBy(['id' => $offer->getProductPosted()->getId()]);
        //order for Product Posted
        $orderForProductPosted = new Order();
        $orderForProductPosted->setMember($productPosted->getMember());
        $orderForProductPosted->setProducts($productPosted);
        $orderForProductPosted->setOrderDate(new \DateTimeImmutable());
        $orderForProductPosted->setDeliveryAdress($productPosted->getMember()->getAdress());
        $productForTradeRepository->setStatusSold($productPosted->getId());
        $this->entityManager->persist($orderForProductPosted);
        $this->entityManager->flush();
        //order for Product Accepted
        $orderForProductOffered = new Order();
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
        $deliveries = $this->entityManager->getRepository(Delivery::class)->findAll();
        return $this->render('order/backOfficeOrders.html.twig', [
            'orders' => $orders,
            'deliveries' =>$deliveries
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
        $delivery=$this->entityManager->getRepository(Delivery::class)->findOneBy(['deliveryOrder'=> $order]);
        return $this->render('order/backOfficeOrderOverView.html.twig', [
            'order' => $order,
            'delivery' => $delivery
        ]);
    }
    ## BackOffice End ##
    ## FrontOffice Begin ##

    #[Route('/account/orders', name: 'app_product_for_sale_orders')]
    public function ProductForSaleOrders(): Response
    {
        $member = $this->getUser();
        $orders = $this->entityManager->getRepository(order::class)->findBy(['member' => $member]);
        $deliveries=$this->entityManager->getRepository(Delivery::class)->findAll();
        return $this->render('order/frontOfficeProductForSaleOrders.html.twig', [
            'orders' => $orders,
            'deliveries' =>$deliveries
        ]);
    }
    ## FrontOffice End ##


    public function sendEmailWithPDF($member, $mailer, $product, $order,$customQrCodeBuilder)
    {
        $deliveryId=$this->entityManager->getRepository(Delivery::class)->findOneBy(['deliveryOrder'=>$order])->getId();
        $pdfFileName = 'MGX' . $order->getId() . 'N.pdf'; 
        $pdfFilePath = $this->getParameter('kernel.project_dir') . '/public/pdfs/' . $pdfFileName;
        $result = $customQrCodeBuilder
        ->size(100)
        ->margin(20)
        ->data('https://172.18.5.70:443/Delivered/' . $order->getId())
        ->build();

    $imageData = $result->getString();
    $base64Image = base64_encode($imageData);

    // Get the MIME type of the image
    $mimeType = $result->getMimeType();

        $htmlContent = $this->renderView('invoice/invoiceProduct.html.twig', [
            'base64Image' => $base64Image,
        'mimeType' => $mimeType,
            'product' => $product,
            'seller' => $product->getMember(),
            'buyer' => $member,
            'order' => $order
        ]);
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($htmlContent);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        // Save PDF to file
        file_put_contents($pdfFilePath, $dompdf->output());
        $content = '<center><h1>YOUR ORDER WAS SHIPPED!</h1><center>Dear ' . $member->getFirstName() . ' ' . $member->getLastName() . '<p>We know you cant wait for your package to arrive .That is why you can track your order here :</p><a href="https://172.18.5.70:443/track_delivery/'.$deliveryId.'">TRACK PACKAGE</a><h6>please note , it could take some time for the tracking information to show on the above</h6></center>';
        $email = (new Email())
            ->from('dealdrop.pidev@outlook.com')
            ->to($member->getEmail())
            ->subject('DealDrop INVOICE')
            ->html($content)
            ->attachFromPath($pdfFilePath);
        $mailer->send($email);
    }
}
