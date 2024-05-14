<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\Message;
use App\Entity\Offer;
use App\Entity\ProductForTrade;
use App\Entity\User;
use App\Repository\ChatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ChatController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/home/chat/{offerId}', name: 'app_chat')]
    public function index($offerId,ChatRepository $rep,MailerInterface $mailer,UrlGeneratorInterface $urlGenerator): Response
    {   $offer=$this->entityManager->getRepository(Offer::class)->findOneBy(['id' => $offerId]);
        $chat=$rep->findOneBy(['offer'=>$offer]);
        $productOffered=$this->entityManager->getRepository(ProductForTrade::class)->findOneBy(['id' => $offer->getProductOffered()->getId()]);
        $ownerOffered = $this->entityManager->getRepository(User::class)->findOneBy(['id'=>$productOffered->getMember()->getId()]);
        if($chat == null)
        {
            $url = $urlGenerator->generate('app_chat', ['offerId' => $offerId], UrlGeneratorInterface::ABSOLUTE_URL);

            // HTML content of the email
            $content = '<center><h1>Dear '.$ownerOffered->getFirstName().' '.$ownerOffered->getLastName().',</h1>' .
                '<p>I hope this email finds you well. I wanted to inform you that there has been interest in your offered product listed on our platform. ' .
                'The owner of another listed product is interested in negotiating with you, potentially offering an exchange or discussing terms that might be mutually beneficial.</p>' .
                '<p>To facilitate this conversation, I\'m providing you with a direct link to the chat with the interested party: ' .
                '<a href="'.$url.'">Go to Chat</a></p>' .
                '<p>Feel free to initiate the discussion at your convenience.</p></center>';
                $email = (new Email())
                    ->from('dealdrop.pidev@outlook.com')
                    ->to($ownerOffered->getEmail())
                    ->subject('Opportunity for Negotiation on Your Offered Product')
                    ->html($content);
                    $mailer->send($email);
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
