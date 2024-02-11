<?php

namespace App\Controller;
use App\Entity\ProductCategory;
use App\Entity\ProductForSale;
use App\Form\ProductForSaleFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig');
    }

    #[Route('/product/add', name: 'app_product_add')]
    public function addProduct(Request $req):Response
    {
        $categories = $this->entityManager->getRepository(ProductCategory::class)->findAll();
        if($req->get('ProductType')=='ProductForSale')
        {
            $pfs=new ProductForSale();
            $form = $this->createForm(ProductForSaleFormType::class, $pfs);
            $form->handleRequest($req);
            return $this->render('product/addProduct.html.twig',[
            'ProductType' =>$req->get('ProductType'),
            'formProduct'=> $form->createView(),
            'categories'=> $categories]
            );
        }
        return $this->render('product/addProduct.html.twig' , [
            'ProductType' =>''
        ]);
    }
}
