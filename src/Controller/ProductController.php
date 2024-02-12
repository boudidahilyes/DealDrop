<?php

namespace App\Controller;
use App\Entity\ProductCategory;
use App\Entity\ProductForRent;
use App\Entity\ProductForSale;
use App\Entity\ProductForTrade;
use App\Form\ProductCategoryFormType;
use App\Form\ProductForSaleFormType;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductForRentRepository;
use App\Repository\ProductForSaleRepository;
use App\Repository\ProductForTradeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $entityManager;
    private $productForSaleRepository;
    private $productForTradeRepository;
    private $productForRentRepository;
    public function __construct(EntityManagerInterface $entityManager,ProductForSaleRepository $productForSaleRepository,ProductForRentRepository $productForRentRepository,ProductForTradeRepository $productForTradeRepository)
    {
        $this->entityManager = $entityManager;
        $this->productForSaleRepository= $productForSaleRepository;
        $this->productForRentRepository= $productForRentRepository;
        $this->productForTradeRepository= $productForTradeRepository;
    }
    #[Route('/product', name: 'app_product')]
    public function index(Request $req): Response
    {
        $listProduct=[];
        if($req->get('ProductType') == 'ProductForSale')
        {
            $listProduct=$this->productForSaleRepository->findAll();
        }
        if($req->get('ProductType') == 'ProductForTrade')
        {
            $listProduct=$this->productForTradeRepository->findAll();
        }
        if($req->get('ProductType') == 'ProductForRent')
        {
            $listProduct=$this->productForRentRepository->findAll();
        }
        return $this->render('product/index.html.twig',[
            'ProductType' => $req->get('ProductType'),
            'listProduct'=> $listProduct
        ]);
    }

    #[Route('/product/add', name: 'app_product_add')]
    public function addProduct(Request $req):Response
    {
        
        if($req->get('ProductType')=='ProductForSale')
        {
            $pfs=new ProductForSale();
            $form = $this->createForm(ProductForSaleFormType::class, $pfs);
            $form->handleRequest($req);
            return $this->render('product/addProduct.html.twig',[
            'ProductType' =>$req->get('ProductType'),
            'formProduct'=> $form->createView(),]
            );
        }
        return $this->render('product/addProduct.html.twig' , [
            'ProductType' =>'3asba'
        ]);
    }
    #[Route('/homeDashboard/categories',name:'app_categories')]
    public function listCategories(ProductCategoryRepository $productCategory,Request $req):Response
    {
        $listCategory=$productCategory->findALL();
        $ProductCategory=new ProductCategory();
        $form = $this->createForm(ProductCategoryFormType::class, $ProductCategory);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) { 
            $this->entityManager->persist($ProductCategory);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_categories');
           } 
        return $this->render('product/ProductCategoryList.html.twig',[
            'listCategory'=> $listCategory,'formCategory'=>$form->createView()
        ]);
    }
}
