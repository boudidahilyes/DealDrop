<?php

namespace App\Command;

use App\Repository\ReminderRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use Twilio\Rest\Client;

#[AsCommand(
    name: 'reminder:check-and-send',
    description: 'Add a short description for your command',
)]
class ReminderCommand extends Command
{
    public ReminderRepository $rep;
    public $entityManager;
    public $texter;
    public function __construct(ReminderRepository $rep, EntityManagerInterface $em, TexterInterface $texter)
    {
        parent::__construct();
        $this->rep = $rep;
        $this->entityManager = $em;
        $this->texter = $texter;
    }
    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $reminders = $this->rep->findBy(["status" => "waiting"]);
        $currentDateTime = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        for ($i = 0;  $i < count($reminders); $i++) {
            if ($reminders[$i]->getReminderDate()->format('Y-m-d H:i:s') < $currentDateTime) {
                $twilioPhoneNumber = $_ENV['TWILIO_PHONE_NUMBER'];
                $members = $reminders[$i]->getMembers();
                for ($j = 0 ; $j < count($members) ; $j++) {
                    $recipientPhoneNumber = '+216' . $members[$j]->getPhone();
                    $message = 'The auction that u wanted to be reminded of begans in few minutes';
                    $sms = new SmsMessage($recipientPhoneNumber, $message);
                    $this->texter->send($sms);
                }
                $reminders[$i]->setStatus('triggered');
                $this->entityManager->persist($reminders[$i]);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
    private function sendSms(string $to, string $message)
    {
        $twilio = new Client($_ENV['TWILIO_ACCOUNT_SID'], $_ENV['TWILIO_AUTH_TOKEN']);

        $twilio->messages->create($to, [
            'from' => $_ENV['TWILIO_PHONE_NUMBER'],
            'body' => $message,
        ]);
    }
}
