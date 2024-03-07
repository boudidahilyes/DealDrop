<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\Message;
use App\Entity\Offer;
use App\Repository\ChatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/home/chat/{offerId}', name: 'app_chat')]
    public function index($offerId,ChatRepository $rep): Response
    {   $offer=$this->entityManager->getRepository(Offer::class)->findOneBy(['id' => $offerId]);
        $chat=$rep->findOneBy(['offer'=>$offer]);
        if($chat == null)
        {
        $chat=new Chat();
        $chat->setOffer($offer);
        $this->entityManager->persist($chat);
        $this->entityManager->flush();
        $messages=null;
        }
        else
        {
            $messages=$this->entityManager->getRepository(Message::class)->findBy(['chat'=>$chat]);
        }
        return $this->render('chat/index.html.twig', [
            'chat' => $chat,
            'messages' =>$messages
        ]);
    }
}
