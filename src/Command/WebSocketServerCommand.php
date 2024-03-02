<?php 
// src/Command/WebSocketServerCommand.php
namespace App\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\WebSocket\LocationHandler;
use Doctrine\DBAL\Exception\ConnectionException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Loop;
use React\Socket\SocketServer;

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
      $app = new HttpServer(
        new WsServer(
           $this->location
        )
     );
     
     $loop = Loop::get();
     $secure_websockets = new SocketServer('0.0.0.0:8443', array(
      'local_cert'        => 'C:/Users/fares/Desktop/Certificate/dealdrop.local.pem', // path to your cert
      'local_pk'          => 'C:/Users/fares/Desktop/Certificate/dealdrop.local-key.pem', // path to your server private key
      'allow_self_signed' => TRUE, // Allow self signed certs (should be false in production)
      'verify_peer' => FALSE
    ), $loop);
    
     $secure_websockets_server = new IoServer($app, $secure_websockets, $loop);
     $secure_websockets_server->run();
     

///
      

        return Command::SUCCESS;
    }
}
