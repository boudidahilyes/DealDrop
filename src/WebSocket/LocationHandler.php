<?php

namespace App\WebSocket;

use App\Entity\Delivery;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\DeliveryMan;
use App\Repository\DeliveryRepository;

class LocationHandler implements MessageComponentInterface
{
    protected $clients;
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->clients = new \SplObjectStorage();
        $this->entityManager = $entityManager;
    }

    public function onOpen(ConnectionInterface $conn)
    {
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        if (isset($data['type']) && $data['type'] === 'location') {
            $this->storeLocation($data['id'], $data['location']);
        }
        if (isset($data['type']) && $data['type'] === 'get-location'){
            $location["location"] = $this->getLocation($data['id']);
            $from->send(json_encode($location));
        }
    }

    public function onClose(ConnectionInterface $conn)
    {

    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    protected function storeLocation( $id, $coordinates)
    {
      $rep = $this->entityManager->getRepository(DeliveryMan::class);
      $dm = $rep->findOneBy(['id' => $id]);
      $deliveries = $dm->getDeliveries();
      for($i = 0 ; $i<count($deliveries);$i++)
      {
          if($deliveries[$i]->getState('In Route'))
          {
              $deliveries[$i]->setCurrentCoordinates($coordinates);
              $this->entityManager->persist($deliveries[$i]);
              $this->entityManager->flush();
          }       
      }
    }
    protected function getLocation( $id)
    {
        $rep = $this->entityManager->getRepository(Delivery::class);
        $delivery = $rep->findOneBy(['id' => $id]);
        return $delivery->getCurrentCoordinates();
    }
}
