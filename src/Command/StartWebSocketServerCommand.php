<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\IoServer;
use App\WebSocket\ChatServer;

class StartWebSocketServerCommand extends Command
{
    protected static $defaultName = 'websocket:chat';
    protected $chat;
    public function __construct(ChatServer $chat)
    {
        parent::__construct();
        $this->chat=$chat;
    }
    protected function configure()
    {
        $this->setDescription('Starts the WebSocket server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    $this->chat
                )
            ),
            8800
        );

        $output->writeln('WebSocket server started.');

        $server->run();
    }
}