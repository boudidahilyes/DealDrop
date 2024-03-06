<?php

namespace App\Controller;

use App\Entity\Auction;
use App\Entity\Member;
use App\Entity\Reminder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;

class ReminderController extends AbstractController
{

    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/reminder', name: 'app_reminder')]
    public function index(): Response
    {
        return $this->render('reminder/index.html.twig', [
            'controller_name' => 'ReminderController',
        ]);
    }



    #[Route('/create-reminder/{id}', name: 'create_reminder')]
    public function createReminder(Request $request, TexterInterface $texter, int $id)
    {    $auction = $this->entityManager->getRepository(Auction::class)->findOneBy(['id' => $id]);
        $reminder = new Reminder();
        $reminder->setAuction($auction);
        $reminder->setStatus("Wainting");
       // $reminder->set($request->request->get('phone_number')); // Assuming you have a form field for the phone number
       $reminderTime = $auction->getStartDate()->modify('-5 minutes');
       $reminderTimeImmutable = \DateTimeImmutable::createFromMutable($reminderTime);

       $reminder->setReminderTime($reminderTimeImmutable);
       $this->entityManager->persist($reminder);
       $this->entityManager->flush();

       $user = $this->entityManager->getRepository(Member::class)->findOneBy(['id' => 1]);


     $twilioPhoneNumber = $_ENV['TWILIO_PHONE_NUMBER'];

      // $recipientPhoneNumber = '+21626852727'; 
       $recipientPhoneNumber = '+216' .$user->getPhone();
       //dd($recipientPhoneNumber);
    $message = 'The auction that u wanted to be reminded of begans in few minutes';

       $this->sendSms($recipientPhoneNumber, $message);

       $sms = new SmsMessage($recipientPhoneNumber, $message);
       $texter->send($sms);




        return $this->redirectToRoute('app_front_auction_list');
    }

    // Background task or cron job
   /* public function sendReminders(TexterInterface $texter)
    {
        $repository = $this->getDoctrine()->getRepository(Reminder::class);
        $upcomingReminders = $repository->findUpcomingReminders();

        foreach ($upcomingReminders as $reminder) {
            $sms = new SmsMessage(
                $reminder->getPhoneNumber(),
                'Reminder: Auction starting in 5 minutes!'
            );

            try {
                // Send the SMS message
                $texter->send($sms);

                // Optionally mark the reminder as sent or delete it from the database
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($reminder);
                $entityManager->flush();
            } catch (\Exception $e) {
                // Handle the error, log or retry as needed
            }
        }
        
    }




    
    #[Route('/auction/reminder', name: 'app_auction_reminder')]
    public function loginSuccess(TexterInterface $texter)
    {
        $twilioPhoneNumber = $_ENV['TWILIO_PHONE_NUMBER'];
        $user = $this->entityManager->getRepository(Member::class)->findOneBy(['id' => 1]);

       // $recipientPhoneNumber = '+21626852727'; 
        $recipientPhoneNumber = '+216' .$user->getPhone();
        //dd($recipientPhoneNumber);
        $message = 'The auction that u wanted to be reminded of begans in few minutes';

        $this->sendSms($recipientPhoneNumber, $message);

        $sms = new SmsMessage($recipientPhoneNumber, $message);
        $texter->send($sms);

       
    }

    private function sendSms(string $to, string $message)
    {
        $twilio = new Client($_ENV['TWILIO_ACCOUNT_SID'], $_ENV['TWILIO_AUTH_TOKEN']);

        $twilio->messages->create($to, [
            'from' => $_ENV['TWILIO_PHONE_NUMBER'],
            'body' => $message,
        ]);
    }
    #[Route('/login/success')]
    public function loginSuccess(TexterInterface $texter)
    {
        $sms = new SmsMessage(
            '+21626852727',
            
            'A new login was detected!'
        );
         $texter->send($sms);
       

    }*/

}



