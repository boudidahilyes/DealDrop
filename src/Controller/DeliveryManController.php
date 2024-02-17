<?php

namespace App\Controller;

use App\Entity\DeliveryMan;
use App\Entity\DriverLicenseImage;
use App\Entity\UserImage;
use App\Form\DeliveryManApplicationFormType;
use App\Form\DeliveryManFormType;
use App\Repository\DeliveryManRepository;
use App\Repository\DriverLicenseImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;



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
        $applicationForm = $this->createForm(DeliveryManApplicationFormType::class, $dm);
        $applicationForm->handleRequest($req);
        if ($applicationForm->isSubmitted() && $applicationForm->isValid()) {
            $dm->setRoles(['ROLE_DELIVERY_MAN']);
            $this->entityManager->persist($dm);
            $this->entityManager->flush();

            $userImage = $applicationForm->get("userImage")->getData();
            $licenseImages = $applicationForm->get("driverLicense")->getData();

            foreach ($licenseImages as $img) {
                $i = new DriverLicenseImage();
                $i->setImageFile($img);
                $i->setDeliveryMan($dm);
                $this->entityManager->persist($i);
                $this->entityManager->flush();
            }


            $userIm = new UserImage();
            $userIm->setImageFile($userImage);
            $userIm->setUser($dm);



            $this->entityManager->persist($userIm);
            $this->entityManager->flush();
            sleep(5);
            return $this->redirectToRoute('app_home');
        }
        return $this->render('delivery_man/delivery_man_application.html.twig', [
            'application' => $applicationForm->createView()

        ]);
    }
    #[Route('/delivery_man_application_list', name: 'app_delivery_man_application_list')]
    public function DeliveryManApplications(DeliveryManRepository $rep): Response
    {
        $deliveryManApps = $rep->findAllApplications();
        return $this->render('delivery_man/delivery_man_application_list.html.twig', [
            'applications' => $deliveryManApps

        ]);
    }

    #[Route('/delivery_man_delete/{id}', name: 'app_delivery_man_delete')]
    public function DeleteDeliveryMan(DeliveryManRepository $rep, $id): Response
    {
        $deliveryManApp = $rep->findOneBy(['id' => $id]);

        $this->entityManager->remove($deliveryManApp);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_delivery_man_application_list');
    }

    #[Route('/delivery_man_application_details/{id}', name: 'app_delivery_man_application_details')]
    public function DeliveryManApplicationDetails(DeliveryManRepository $rep, $id): Response
    {
        $deliveryMan = $rep->findOneBy(['id' => $id]);

        return $this->render('delivery_man/delivery_man_application_details.html.twig', [
            'application' => $deliveryMan,
        ]);
    }

    #[Route('/application_refusal_email/{id}', name: 'app_send_application_refusal_email')]
    public function SendApplicationRefusalEmail(DeliveryManRepository $rep, Request $req, MailerInterface $mailer, $id): Response
    {
        $receiver = $req->get("email");
        $content = $req->get("content");
        $deliveryMan = $rep->findOneBy(['id' => $id]);
       
            $email = (new Email())
                ->from('dealdrop.pidev@gmail.com')
                ->to($receiver)
                ->subject('Application Notification')
                ->html($content);

            $mailer->send($email);
        
        $this->entityManager->remove($deliveryMan);
        return $this->redirectToRoute('app_delivery_man_application_list');
    }
}
