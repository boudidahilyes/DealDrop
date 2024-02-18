<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\ProductForRent;
use App\Entity\ProductImage;
use App\Form\ProductForRentFormType;
use App\Repository\ProductForRentRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductForRentController extends AbstractController
{    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    private function getCookieID(Request $req)
    {
        $cookie = new Cookie(
            "user_id",                              // Cookie name
            2,                                       // Cookie content
            (new DateTime('now'))->modify("+1 day"), // Expiration date
            "/",                                     // Path
            "localhost",                             // Domain
            $req->getScheme() === 'https',       // Secure
            false,                                   // HttpOnly
            true,                                    // Raw
            'Strict'                                 // SameSite policy
        );
        return $cookie->getValue();
    }
 //---------------------------------------ProductForRent Begin------------------------------
 #[Route('/productForRent', name: 'app_product_for_rent')]
 public function index1(Request $req,ProductForRentRepository $productForRentRepository): Response
 {
    
     $listProduct = $productForRentRepository->findAllProductForRent($this->getCookieID($req));
     return $this->render('product/frontOfficeListProductForRent.html.twig', [
         'listProduct' => $listProduct
     ]);
 }
 #[Route('/productForRent/add', name: 'app_product_for_rent_add')]
 public function addProductForRent(Request $req): Response
 {
     $member = $this->entityManager->getRepository(Member::class)->findOneBy(['id' => 1]);
     $pfr = new ProductForRent();
     $form = $this->createForm(ProductForRentFormType::class, $pfr);
     $form->handleRequest($req);
     if ($form->isSubmitted() && $form->isValid()) {
         $pfr->setAddDate(new \DateTimeImmutable());
         $pfr->setMember($member);
         $pfr->setStatus('Pending');
         $pfr->setDisponibility('Available');
         $ProductImages = $form->get("productImage")->getData();
         foreach ($ProductImages as $img) {
             $ProductImage = new ProductImage($img->getClientOriginalName());
             $ProductImage->setImageFile($img);
             $ProductImage->setProduct($pfr);
             $this->entityManager->persist($ProductImage);
             $this->entityManager->flush();
         }
         $this->entityManager->persist($pfr);
         $this->entityManager->flush();
         return $this->redirectToRoute('app_product_for_rent');
     }
     return $this->render('product/frontOfficeAddProductForRent.html.twig', ['formProduct' => $form->createView()]);
 }
 #[Route('/productForRent/overview/{id}', name: 'app_product_for_rent_details')]
 public function ProductForRentMoreDetails($id): Response
 {
     $product = $this->entityManager->getRepository(ProductForRent::class)->findOneBy(['id' => $id]);
     return $this->render('product/frontOfficeProductForRentDetails.html.twig', [
         'product' => $product
     ]);
 }
 #[Route('/homeDashboard/productForRent', name: 'app_product_for_rent_list')]
 public function listProductForRent(): Response
 {
     $listProduct = $this->entityManager->getRepository(ProductForRent::class)->findAll();
     return $this->render('product/backOfficeListProductForRent.html.twig', [
         'listProduct' => $listProduct
     ]);
 }
 #[Route('/homeDashboard/productForRentOverView{id}', name: 'app_product_for_rent_overview')]
 public function overviewProductForRent($id): Response
 {
     $product = $this->entityManager->getRepository(ProductForRent::class)->findOneBy(['id' => $id]);
     return $this->render('product/backOfficeProductForRentOverView.html.twig', [
         'product' => $product
     ]);
 }
 #[Route('/homeDashboard/productForRentA/{id}', name: 'app_product_for_rent_accepted')]
 public function productForRentAccepted($id): Response
 {
     $product = $this->entityManager->getRepository(ProductForRent::class)->findOneBy(['id' => $id]);
     if ($product != null) {
         $product->setStatus("Approved");
         $this->entityManager->persist($product);
         $this->entityManager->flush();
     }
     return $this->redirectToRoute('app_product_for_rent_list');
 }
 #[Route('/homeDashboard/productForRentD/{id}', name: 'app_product_for_rent_declined')]
 public function productForRentDeclined($id): Response
 {
     $product = $this->entityManager->getRepository(ProductForRent::class)->findOneBy(['id' => $id]);
     if ($product != null) {
         $product->setStatus("Declined");
         $this->entityManager->persist($product);
         $this->entityManager->flush();
     }
     return $this->redirectToRoute('app_product_for_rent_list');
 }

 #[Route('/homeDashboard/productForRentDel/{id}', name: 'app_product_for_rent_deleted')]
 public function deleteProductForRent($id): Response
 {

     $product = $this->entityManager->getRepository(ProductForRent::class)->findOneBy(['id' => $id]);
     $this->entityManager->remove($product);
     $this->entityManager->flush(); 

     return $this->redirectToRoute('app_product_for_rent_list');
     
 }
 //------------------------ProductForRent END-------------------------------------
}
