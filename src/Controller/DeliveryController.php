<?php

namespace App\Controller;

use App\Repository\DeliveryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeliveryController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/delivery', name: 'app_delivery')]
    public function index(): Response
    {
        return $this->render('delivery/index.html.twig', [
            'controller_name' => 'DeliveryController',
        ]);
    }

    #[Route('/available_deliveries', name: 'app_available_deliveries')]
    public function getAvailableDeliveries(DeliveryRepository $rep): Response
    {
        $deliveries = $rep->findBy(['state' => 'Awaiting Pick Up']);
        return $this->render('delivery/available_deliveries.html.twig', [
            'deliveries' => $deliveries
        ]);
    }
    
    #[Route('/delivery_details/{id}', name: 'app_delivery_details')]
    public function getDeliveryDetails(DeliveryRepository $rep, $id): Response
    {
        $delivery = $rep->findOneBy(['id' => $id]);
        return $this->render('delivery/delivery_details.html.twig', [
            'delivery' => $delivery
        ]);
    }
}
