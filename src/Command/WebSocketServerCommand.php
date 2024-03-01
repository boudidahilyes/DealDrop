<?php 
// src/Command/WebSocketServerCommand.php
namespace App\Command;

use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\IoServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\WebSocket\LocationHandler;

class WebSocketServerCommand extends Command
{
    protected static $defaultName = 'websocket:server';
    protected LocationHandler $location;
    public function __construct(LocationHandler $location)
    {
      parent::__construct();
      $this->location = $location;
    }
    protected function configure()
    {
        $this->setDescription('Starts the WebSocket server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Instantiate your WebSocket handler (Chat in this case)

        // Create the WebSocket server using Ratchet
        $server = IoServer::factory(
            new HttpServer(
                new WsServer($this->location)
            ),
            8000,
            '127.0.0.10'
        );

        // Output message indicating the server is running
        $output->writeln('WebSocket server started on port 8000.');

        // Run the server
        $server->run();

        return Command::SUCCESS;
    }
}
