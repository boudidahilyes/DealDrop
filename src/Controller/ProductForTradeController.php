<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\ProductForSaleController as prdcon;
use App\Entity\Member;
use App\Entity\Offer;
use App\Entity\ProductForTrade;
use App\Entity\ProductImage;
use App\Form\ProductForTradeFormType;
use App\Repository\ProductForTradeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductForTradeController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #FrontOffice Begin#
    #[Route('/productForTrade', name: 'app_product_for_trade')]
    public function index(Request $req, ProductForTradeRepository $productForTradeRepository, prdcon $prdcon): Response
    {
        $listProduct = $productForTradeRepository->findAllProductForTrade($prdcon->getCookieID($req));
        return $this->render('product/frontOfficeListProductForTrade.html.twig', [
            'listProduct' => $listProduct
        ]);
    }
    #[Route('/productForTrade/add', name: 'app_product_for_trade_add')]
    public function addProductForTrade(Request $req): Response
    {
        $member = $this->entityManager->getRepository(Member::class)->findOneBy(['id' => 1]);
        $pft = new ProductForTrade();
        $form = $this->createForm(ProductForTradeFormType::class, $pft);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $pft->setAddDate(new \DateTimeImmutable());
            $pft->setMember($member);
            $pft->setStatus('Pending');
            $pft->setTradeType('POSTED');
            $ProductImages = $form->get("productImage")->getData();
            foreach ($ProductImages as $img) {
                $ProductImage = new ProductImage($img->getClientOriginalName());
                $ProductImage->setImageFile($img);
                $ProductImage->setProduct($pft);
                $this->entityManager->persist($ProductImage);
                $this->entityManager->flush();
                dump($ProductImage);
            }
            die;
            $this->entityManager->persist($pft);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_product_for_trade');
        }
        return $this->render('product/frontOfficeAddProductForTrade.html.twig', ['formProduct' => $form->createView()]);
    }
    #[Route('/productForTrade/overview/{id}', name: 'app_product_for_trade_details')]
    public function ProductForTradeMoreDetails($id): Response
    {
        $product = $this->entityManager->getRepository(ProductForTrade::class)->findOneBy(['id' => $id]);
        return $this->render('product/frontOfficeProductForTradeDetails.html.twig', [
            'product' => $product
        ]);
    }
    #[Route('/productForTrade/offer/{id}', name: 'app_product_for_trade_offer')]
    public function offerProductForTrade(Request $req, prdcon $prdcon, $id): Response
    {
        $productPosted = $this->entityManager->getRepository(ProductForTrade::class)->findOneBy(['id' => $id]);
        $member = $this->entityManager->getRepository(Member::class)->findOneBy(['id' => $prdcon->getCookieID($req)]);
        $pft = new ProductForTrade();
        $offer = new offer();
        $form = $this->createForm(ProductForTradeFormType::class, $pft);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $pft->setAddDate(new \DateTimeImmutable());
            $pft->setMember($member);
            $pft->setStatus('Pending');
            $pft->setTradeType('OFFERED');
            $ProductImages = $form->get("productImage")->getData();
            foreach ($ProductImages as $img) {
                $ProductImage = new ProductImage($img->getClientOriginalName());
                $ProductImage->setImageFile($img);
                $ProductImage->setProduct($pft);
                $this->entityManager->persist($ProductImage);
                $this->entityManager->flush();
            }
            $this->entityManager->persist($pft);
            $this->entityManager->flush();
            $offer->setProductPosted($productPosted);
            $offer->setProductOffered($pft);
            $this->entityManager->persist($offer);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_product_for_trade_details', ['id' => $id]);
        }
        return $this->render('product/frontOfficeAddProductForTrade.html.twig', ['formProduct' => $form->createView()]);
    }

    #frontOffice End#
    #BackOffice Begin#
    #[Route('/homeDashboard/productForTrade', name: 'app_product_for_trade_list')]
    public function listProductForTrade(): Response
    {
        $listProduct = $this->entityManager->getRepository(ProductForTrade::class)->findAll();
        return $this->render('product/backOfficeListProductForTrade.html.twig', [
            'listProduct' => $listProduct
        ]);
    }
    #[Route('/homeDashboard/productForTradeOverView{id}', name: 'app_product_for_trade_overview')]
    public function overviewProductForTrade($id): Response
    {
        $product = $this->entityManager->getRepository(ProductForTrade::class)->findOneBy(['id' => $id]);
        return $this->render('product/backOfficeProductForTradeOverView.html.twig', [
            'product' => $product
        ]);
    }
    #[Route('/homeDashboard/productForTradeA/{id}', name: 'app_product_for_trade_accepted')]
    public function productForTradeAccepted($id): Response
    {
        $product = $this->entityManager->getRepository(ProductForTrade::class)->findOneBy(['id' => $id]);
        if ($product != null) {
            $product->setStatus("Approved");
            $this->entityManager->persist($product);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('app_product_for_trade_list');
    }
    #[Route('/homeDashboard/productForTradeD/{id}', name: 'app_product_for_trade_declined')]
    public function productForTradeDeclined($id): Response
    {
        $product = $this->entityManager->getRepository(ProductForTrade::class)->findOneBy(['id' => $id]);
        if ($product != null) {
            $product->setStatus("Declined");
            $this->entityManager->persist($product);
            $this->entityManager->flush();
            if ($product->getTradeType() == 'OFFERED') {
                $offer = $this->entityManager->getRepository(Offer::class)->findOneBy(['productOffered' => $id]);
                $this->entityManager->remove($offer);
                $this->entityManager->flush();
            }
        }
        return $this->redirectToRoute('app_product_for_trade_list');
    }
    #[Route('/homeDashboard/productForTradeDel/{id}', name: 'app_product_for_trade_deleted')]
    public function deleteProductForTrade($id): Response
    {
        $product = $this->entityManager->getRepository(ProductForTrade::class)->findOneBy(['id' => $id]);
        $this->entityManager->remove($product);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_product_for_trade_list');
    }
}
