<?php
namespace App\WebSocket;

use App\Entity\Chat;
use App\Entity\Member;
use App\Entity\Message;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ChatServer implements MessageComponentInterface
{
    protected $clients;
    protected $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->clients = new \SplObjectStorage;
        $this->entityManager=$entityManager;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg);
        if($data->type == 'open')
        {
            $from->clientId = $data->id;
        }
        if($data->type == 'send')
        {
            foreach($this->clients as $client){
                if($client->clientId == $data->id){
                    $client->send($data->message);
                }
            }
            $chat=$this->entityManager->getRepository(Chat::class)->findOneBy(['id'=>$data->idChat]);
            $message=new Message();
            $message->setChat($chat);
            $sender=$this->entityManager->getRepository(Member::class)->findOneBy(['id'=>$data->idSender]);
            $message->setSender($sender);
            $message->setContent($data->message);
            $this->entityManager->persist($message);
            $this->entityManager->flush();
        }
/*         if($data->type == 'sendPrice')
        {
            foreach($this->clients as $client){
                if($client->clientId == $data->id){
                    $client->send($data);
                }
            }
        } */
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}