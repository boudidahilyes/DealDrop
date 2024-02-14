<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\ProductCategory;
use App\Entity\ProductForRent;
use App\Entity\ProductForSale;
use App\Entity\ProductForTrade;
use App\Entity\ProductImage;
use App\Form\ProductCategoryFormType;
use App\Form\ProductForSaleFormType;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductForRentRepository;
use App\Repository\ProductForSaleRepository;
use App\Repository\ProductForTradeRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Exception\VichUploaderExceptionInterface;

class ProductController extends AbstractController
{
    private $entityManager;
    private $productForSaleRepository;
    private $productForTradeRepository;
    private $productForRentRepository;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    //ProductForSale Begin
    #[Route('/productForSale', name: 'app_product_for_sale')]
    public function index(): Response
    {
        $listProduct = $this->entityManager->getRepository(ProductForSale::class)->findAll();
        return $this->render('product/index.html.twig', [
            'listProduct' => $listProduct
        ]);
    }

    #[Route('/productForSale/add', name: 'app_product_for_sale_add')]
    public function addProductForSale(Request $req): Response
    {
        $member = $this->entityManager->getRepository(Member::class)->findOneBy(['id' => 1]);
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
        return $this->render('product/addProductForSale.html.twig', ['formProduct' => $form->createView()]);
    }

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
        return $this->render('product/backOfficeProductOverView.html.twig', [
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
    //ProductForSale End
}
