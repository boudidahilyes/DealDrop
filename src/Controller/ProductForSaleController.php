<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\Order;
use App\Entity\ProductForSale;
use App\Entity\ProductImage;
use App\Form\ProductForSaleFormType;
use App\Repository\ProductForSaleRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductForSaleController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    //-------------------------ProductForSale Begin----------------------
    #FrontOffice Begin#
    #[Route('/productForSale', name: 'app_product_for_sale')]
    public function index(Request $req, ProductForSaleRepository $productForSaleRepository): Response
    {
        $listProduct = $productForSaleRepository->findAllProductForSale($this->getUser()->getId());
        return $this->render('product/frontOfficeListProductForSale.html.twig', [
            'listProduct' => $listProduct
        ]);
    }

    #[Route('/productForSale/add', name: 'app_product_for_sale_add')]
    public function addProductForSale(Request $req): Response
    {
        $member = $this->getUser();
        $pfs = new ProductForSale();
        $form = $this->createForm(ProductForSaleFormType::class, $pfs);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $pfs->setAddDate(new \DateTimeImmutable());
            $pfs->setMember($member);
            $pfs->setStatus('Pending');
            $ProductImages = $form->get("productImage")->getData();
            foreach ($ProductImages as $img) {
                $ProductImage = new ProductImage($img->getClientOriginalName());
                $ProductImage->setImageFile($img);
                $ProductImage->setProduct($pfs);
                $this->entityManager->persist($ProductImage);
                $this->entityManager->flush();
            }
            $this->entityManager->persist($pfs);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_product_for_sale');
        }
        return $this->render('product/frontOfficeAddProductForSale.html.twig', ['formProduct' => $form->createView()]);
    }

    #[Route('/productForSale/overview/{id}', name: 'app_product_for_sale_details')]
    public function ProductForSaleMoreDetails($id): Response
    {
        $product = $this->entityManager->getRepository(ProductForSale::class)->findOneBy(['id' => $id]);
        return $this->render('product/frontOfficeProductForSaleDetails.html.twig', [
            'product' => $product
        ]);
    }
    #[Route('/profil/productForSale', name: 'app_product_for_sale_profil')]
    public function ProductForSaleProfil(ProductForSaleRepository $productForSaleRepository): Response
    {
        $products = $productForSaleRepository->findAllProductForSaleProfil($this->getUser()->getId());
        return $this->render('product/frontOfficeListProductForSaleProfil.html.twig', [
            'listProduct' => $products
        ]);
    }
    #[Route('/profil/productForSale/edit{id}', name: 'app_product_for_sale_edit')]
    public function editProductForSale($id, Request $req): Response
    {
        $pfs = $this->entityManager->getRepository(ProductForSale::class)->findOneBy(['id' => $id]);
        $form = $this->createForm(ProductForSaleFormType::class, $pfs);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $pfs->setAddDate(new \DateTimeImmutable());
            $pfs->setStatus('Pending');
            $ProductImages = $form->get("productImage")->getData();
            if ($ProductImages != null) {
                foreach ($pfs->getProductImages() as $productImg) {
                    $this->entityManager->remove($productImg);
                    $this->entityManager->flush();
                }
                $pfs->getProductImages()->clear();
                foreach ($ProductImages as $img) {
                    $ProductImage = new ProductImage($img->getClientOriginalName());
                    $ProductImage->setImageFile($img);
                    $ProductImage->setProduct($pfs);
                    $this->entityManager->persist($ProductImage);
                    $this->entityManager->flush();
                }
            }
            $this->entityManager->persist($pfs);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_product_for_sale_profil');
        }
        return $this->render('product/frontOfficeAddProductForSale.html.twig', ['formProduct' => $form->createView()]);
    }
    #[Route('/profil/productForSaleDel/{id}', name: 'app_product_for_sale_remove')]
    public function removeProductForSale($id): Response
    {
        $product = $this->entityManager->getRepository(ProductForSale::class)->findOneBy(['id' => $id]);
        $this->entityManager->remove($product);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_product_for_sale_profil');
    }
    #frontOffice End#

    #BackOffice Begin#
    #[Route('/homeDashboard/productForSale', name: 'app_product_for_sale_list')]
    public function listProductForSale(): Response
    {
        $listProduct = $this->entityManager->getRepository(ProductForSale::class)->findAll();
        return $this->render('product/backOfficeListProductForSale.html.twig', [
            'listProduct' => $listProduct
        ]);
    }

    #[Route('/homeDashboard/productForSaleOverView{id}', name: 'app_product_for_sale_overview')]
    public function overviewProductForSale($id): Response
    {
        $product = $this->entityManager->getRepository(ProductForSale::class)->findOneBy(['id' => $id]);
        return $this->render('product/backOfficeProductForSaleOverView.html.twig', [
            'product' => $product
        ]);
    }

    #[Route('/homeDashboard/productForSaleA/{id}', name: 'app_product_for_sale_accepted')]
    public function productForSaleAccepted($id): Response
    {
        $product = $this->entityManager->getRepository(ProductForSale::class)->findOneBy(['id' => $id]);
        if ($product != null) {
            $product->setStatus("Approved");
            $this->entityManager->persist($product);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('app_product_for_sale_list');
    }

    #[Route('/homeDashboard/productForSaleD/{id}', name: 'app_product_for_sale_declined')]
    public function productForSaleDeclined($id): Response
    {
        $product = $this->entityManager->getRepository(ProductForSale::class)->findOneBy(['id' => $id]);
        if ($product != null) {
            $product->setStatus("Declined");
            $this->entityManager->persist($product);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('app_product_for_sale_list');
    }

    #[Route('/homeDashboard/productForSaleDel/{id}', name: 'app_product_for_sale_deleted')]
    public function deleteProductForSale($id): Response
    {

        $product = $this->entityManager->getRepository(ProductForSale::class)->findOneBy(['id' => $id]);
        $this->entityManager->remove($product);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_product_for_sale_list');
    }

    #backOffice End#
    //-----------------------------ProductForSale End-------------------------------------------
}
