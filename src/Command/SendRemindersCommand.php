<?php

namespace App\Command;
use App\Entity\Member;
use App\Entity\Reminder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Notifier\Message\SmsMessage;
use App\Service\TwilioService;


#[AsCommand(
    name: 'SendRemindersCommand',
    description: 'Add a short description for your command',
)]
class SendRemindersCommand extends Command
{
   /* protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }
*/

    private $entityManager;
    private $reminderSender;

    public function __construct(EntityManagerInterface $entityManager)
    {
    parent::__construct();

    $this->entityManager = $entityManager;
    //$this->twilioService = $twilioService;
}

protected function configure()
{
    $this->setName('app:send-reminders')
        ->setDescription('Send reminders for upcoming auctions');
}

protected function execute(InputInterface $input, OutputInterface $output)
{
    $currentTime = new \DateTimeImmutable();
    $reminders = $this->entityManager->getRepository(Reminder::class)->findByUpcomingReminders($currentTime);

    foreach ($reminders as $reminder) {
        $this->reminderSender->sendReminder($reminder);
    }

    $output->writeln('Reminders sent successfully.');

    return Command::SUCCESS;
}
/*
public function sendReminder(Reminder $reminder)
{
    $user = $this->entityManager->getRepository(Member::class)->findOneBy(['id' => 1]);
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

   /* protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }*/
}
