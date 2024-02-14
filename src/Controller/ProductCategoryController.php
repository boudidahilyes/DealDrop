<?php

namespace App\Controller;

use App\Entity\ProductCategory;
use App\Form\ProductCategoryFormType;
use App\Repository\ProductCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductCategoryController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    } 
    #[Route('/homeDashboard/categories', name: 'app_categories')]
    public function listCategory(ProductCategoryRepository $productCategory, Request $req): Response
    {
        $listCategory = $productCategory->findALL();
        $ProductCategory = new ProductCategory();
        $form = $this->createForm(ProductCategoryFormType::class, $ProductCategory);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($ProductCategory);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_categories');
        }
        return $this->render('product_category/ProductCategoryList.html.twig', [
            'listCategory' => $listCategory,
            'formCategory' => $form->createView(),
        ]);
    }
    #[Route('/homeDashboard/cateogires/remove/{id}', name: 'app_category_remove')]
    public function removeCategory($id): RedirectResponse
    {
        $Category = $this->entityManager->getRepository(ProductCategory::class)->findOneBy(['id' => $id]);
        $this->entityManager->remove($Category);
        $this->entityManager->flush();
        return $this->redirectToRoute("app_categories");
    }
}
