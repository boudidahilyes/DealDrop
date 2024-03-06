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
use App\Repository\OfferRepository;
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
        $listProduct = $productForTradeRepository->findAllProductForTrade($this->getUser()->getId());
        return $this->render('product/frontOfficeListProductForTrade.html.twig', [
            'listProduct' => $listProduct
        ]);
    }
    #[Route('/productForTrade/add', name: 'app_product_for_trade_add')]
    public function addProductForTrade(Request $req): Response
    {
        $member=$this->getUser();
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
            }
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
    #[Route('/profil/productForTrade', name: 'app_product_for_trade_profil')]
    public function ProductForTradeProfil(ProductForTradeRepository $productForTradeRepository): Response
    {
        $products = $productForTradeRepository->findAllProductForTradeProfil($this->getUser()->getId());
        return $this->render('product/frontOfficeListProductForTradeProfil.html.twig', [
            'listProduct' => $products
        ]);
    }
    #[Route('/profil/productForTrade/edit{id}', name: 'app_product_for_trade_edit')]
    public function editProductForTrade($id, Request $req): Response
    {
        $pft = $this->entityManager->getRepository(ProductForTrade::class)->findOneBy(['id' => $id]);
        $form = $this->createForm(ProductForTradeFormType::class, $pft);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $offers = $this->entityManager->getRepository(Offer::class)->findBy(['productPosted' => $pft]);
            foreach ($offers as $offer) {
                $pft->removeOffer($offer);
                $productoffered = $this->entityManager->getRepository(ProductForTrade::class)->findOneBy(['id' => $offer->getProductOffered()]);
                $this->entityManager->remove($productoffered);
                $this->entityManager->flush();
            }
            $pft->setAddDate(new \DateTimeImmutable());
            $pft->setStatus('Pending');
            $ProductImages = $form->get("productImage")->getData();
            if ($ProductImages != null) {
                foreach ($pft->getProductImages() as $productImg) {
                    $this->entityManager->remove($productImg);
                    $this->entityManager->flush();
                }
                $pft->getProductImages()->clear();
                foreach ($ProductImages as $img) {
                    $ProductImage = new ProductImage($img->getClientOriginalName());
                    $ProductImage->setImageFile($img);
                    $ProductImage->setProduct($pft);
                    $this->entityManager->persist($ProductImage);
                    $this->entityManager->flush();
                }
            }
            $this->entityManager->persist($pft);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_product_for_trade_profil');
        }
        return $this->render('product/frontOfficeAddProductForTrade.html.twig', ['formProduct' => $form->createView()]);
    }
    #[Route('/productForTrade/offer/{id}', name: 'app_product_for_trade_offer')]
    public function offerProductForTrade(Request $req, prdcon $prdcon, $id): Response
    {
        $productPosted = $this->entityManager->getRepository(ProductForTrade::class)->findOneBy(['id' => $id]);
        $member=$this->getUser();
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
    #[Route('/profil/productForTrade/offers/{id}', name: 'app_product_for_trade_offers')]
    public function ViewAllOffers($id): Response
    {
        $product = $this->entityManager->getRepository(ProductForTrade::class)->findOneBy(['id' => $id]);
        $offers = [];
        foreach ($product->getOffers() as $offer) {
            $offers[] = $offer->getProductOffered();
        }
        return $this->render('product/frontOfficeProductForTradeOffers.html.twig', [
            'listProduct' => $offers
        ]);
    }

    #[Route('/profil/productForTrade/details/{id}', name: 'app_product_for_trade_offer_details')]
    public function offerMoreDetails($id): Response
    {
        $product = $this->entityManager->getRepository(ProductForTrade::class)->findOneBy(['id' => $id]);
        return $this->render('product/frontOfficeProductForTradeOfferDetails.html.twig', [
            'product' => $product
        ]);
    }
    #[Route('/profil/productForTrade/accept/{id}', name: 'app_product_for_trade_offer_accept')]
    public function acceptOffer($id): Response
    {
        $offer = $this->entityManager->getRepository(Offer::class)->findOneBy(['productOffered' => $id]);
        $offer->getProductPosted()->setChosenOffer($offer);
        $this->entityManager->persist($offer->getProductPosted());
        $this->entityManager->flush();
        return $this->redirectToRoute('app_product_for_trade_order', ['offerId' => $offer->getId()]);
    }
    #[Route('/profil/productForTradeDel/{id}', name: 'app_product_for_trade_remove')]
    public function removeProductForTrade($id): Response
    {
        $product = $this->entityManager->getRepository(ProductForTrade::class)->findOneBy(['id' => $id]);
        $this->entityManager->remove($product);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_product_for_trade_profil');
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
