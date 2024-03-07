<?php

namespace App\Controller;

use App\Entity\Delivery;
use Location\Polygon;
use Location\Coordinate;
use App\Entity\DeliveryMan;
use App\Repository\DeliveryManRepository;
use App\Repository\DeliveryRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/admin/deliveries', name: 'app_admin_deliveries')]
    public function getAllDeliveries(DeliveryRepository $rep): Response
    {
        $deliveries = $rep->findAll();
        return $this->render('delivery/admin_deliveries_list.html.twig', [
            'deliveries' => $deliveries
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

    #[Route('/my_claimed_deliveries', name: 'app_my_claimed_deliveries')]
    public function getMyDeliveries(DeliveryRepository $rep, DeliveryManRepository $dmrep): Response
    {
        $deliveries = $rep->findBy(['deliveryMan' => $this->getUser()]);
        return $this->render('delivery/my_claimed_deliveries.html.twig', [
            'deliveries' => $deliveries
        ]);
    }
    #[Route('/available_deliveries_in_my_area', name: 'app_available_deliveries_in_my_area')]
    public function getAvailableDeliveriesInMyArea(DeliveryRepository $rep, DeliveryManRepository $dmrep): Response
    {
        $deliveries = $rep->findBy(['state' => 'Awaiting Pick Up']);
        $deliveryMan = $this->getUser();

        $parcedCoords = explode(',', $deliveryMan->getArea());
        
        $polygon = new Polygon();
        for($i = 0; $i < count($parcedCoords); $i+=2){
            $polygon->addPoint(new Coordinate(floatval($parcedCoords[$i]),floatval($parcedCoords[$i + 1])));
        }
       $deliveriesInArea = [];
        foreach($deliveries AS $delivery){
            $coords = explode(',', $delivery->getCoordinates()) ;
            if($polygon->contains(new Coordinate(floatval($coords[0]),floatval($coords[1]))))
                $deliveriesInArea[] = $delivery;
        }
        
        return $this->render('delivery/available_deliveries_in_my_area.html.twig', [
            'deliveries' => $deliveriesInArea,
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

    #[Route('/claim_delivery/{id}', name: 'app_claim_delivery')]
    public function claimDelivery(DeliveryRepository $rep, $id): Response
    {
        $delivery = $rep->findOneBy(['id' => $id]);
        $delivery->claim($this->getUser());
        $this->entityManager->persist($delivery);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_my_claimed_deliveries');
    }

    #[Route('/unclaim_delivery/{id}', name: 'app_unclaim_delivery')]
    public function unclaimDelivery(DeliveryRepository $rep, $id): Response
    {
        $delivery = $rep->findOneBy(['id' => $id]);
        $delivery->unclaim();
        $this->entityManager->persist($delivery);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_my_claimed_deliveries');
    }

    #[Route('/mark_as_delivered/{id}', name: 'app_mark_as_delivered')]
    public function markAsDeliverd(DeliveryRepository $rep, $id): Response
    {
        $delivery = $rep->findOneBy(['id' => $id]);
        $delivery->setState('Delivered');
        $delivery->setArrivalTime(new DateTime());
        $this->entityManager->persist($delivery);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_my_claimed_deliveries');
    }

    #[Route('/claimed_delivery_details/{id}', name: 'app_claimed_delivery_details')]
    public function getClaimedDeliveryDetails(DeliveryRepository $rep, $id): Response
    {
        $delivery = $rep->findOneBy(['id' => $id]);
        return $this->render('delivery/claimed_delivery_details.html.twig',[
            'delivery' => $delivery  
        ]);
    }

    #[Route('/admin/delivery_details/{id}', name: 'app_admin_delivery_details')]
    public function getDeliveryDetailsAdmin(DeliveryRepository $rep, $id): Response
    {
        $delivery = $rep->findOneBy(['id' => $id]);
        return $this->render('delivery/admin_delivery_details.html.twig',[
            'delivery' => $delivery  
        ]);
    }

    #[Route('/admin/track_active_deliveries', name: 'app_admin_track_active_deliveries')]
    public function getActiveDeliveries(DeliveryRepository $rep): Response
    {
        $deliveries = $rep->findBy(['state' => ['In Route', 'Awaiting Pick Up']]);
        return $this->render('delivery/admin_all_deliveries_locations.html.twig',[
            'deliveries' => $deliveries
        ]);
    }


    #[Route('/track_delivery/{id}', name: 'app_track_delivery')]
    public function trackDelivery(DeliveryRepository $rep, $id): Response
    {
        $delivery = $rep->findOneBy(['id' => $id]);
        return $this->render('delivery/track_delivery.html.twig',[
            'delivery' => $delivery
        ]);
    }

    #[Route('/get_delivery_position', name: 'app_delivery_position')]
    public function updateDeliveryPosition(DeliveryRepository $rep, Request $req): JsonResponse
    {
        $id = $req->get('id');
        $delivery = $rep->findOneBy(['id' => $id]);
        return new JsonResponse(['location' => $delivery->getCurrentCoordinates()]);
    }
    
            
    #[Route('/Delivered/{id}',name:'app_set_delivered')]
    public function setDeliveryDelivered($id):Response
    {
        $delivery=$this->entityManager->getRepository(Delivery::class)->findOneBy(['deliveryOrder'=>$id]);
        $delivery->setState('Delivered');
        $this->entityManager->persist($delivery);
        $this->entityManager->flush();
        return $this->render('Order/thankyou.html.twig');
    }

}
