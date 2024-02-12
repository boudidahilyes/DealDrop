<?php

namespace App\Controller;

use App\Entity\DeliveryMan;
use App\Entity\DriverLicenseImage;
use App\Entity\UserImage;
use App\Form\DeliveryManApplicationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\UploadHandler;

class DeliveryManController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/delivery/man', name: 'app_delivery_man')]
    public function index(): Response
    {
        return $this->render('delivery_man/index.html.twig', [
            'controller_name' => 'DeliveryManController',
        ]);
    }

    #[Route('/delivery_man_application', name: 'app_delivery_man_application')]
    public function DeliveryManApplication(Request $req): Response
    {
         $dm = new DeliveryMan();
        $applicationForm= $this->createForm(DeliveryManApplicationFormType::class, $dm);
        $applicationForm->handleRequest($req);
        if($applicationForm->isSubmitted() && $applicationForm->isValid()){
            $this->entityManager->persist($dm);
            $this->entityManager->flush();


            $userImage=$applicationForm->get("userImage")->getData();
            $licenseImages=$applicationForm->get("driverLicense")->getData();
            
            foreach($licenseImages AS $img){
                $i = new DriverLicenseImage($img->getClientOriginalName()); 
                $i->setImageFile($img);
                $i->setDeliveryMan($dm);
                $this->entityManager->persist($i);
                $this->entityManager->flush();    
            }
            

            $userIm =new UserImage($userImage->getClientOriginalName());
            $userIm->setImageFile($userImage);
            $userIm->setUser($dm);
            


            $this->entityManager->persist($userIm);
            $this->entityManager->flush();    

        }
        return $this->render('delivery_man/delivery_man_application.html.twig', [
            'application' => $applicationForm->createView()
           
        ]);
    }
}
