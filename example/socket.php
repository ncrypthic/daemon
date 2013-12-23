<?php
require_once "../vendor/autoload.php";

use Psr\Log\LoggerInterface;
use Ncrypthic\Daemon\Interfaces\ProcessInterface;
use Ncrypthic\Daemon\Manager\AbstractProcessManager;
use Ncrypthic\Daemon\Exception\NoChildProcessErrorException;

$loader = new Composer\Autoload\ClassLoader();
$loader->add('Ncrypthic\\', '../src/Ncrypthic');
$loader->register();

class SocketServerManager extends AbstractProcessManager
{
    private $socket;
    private $childSocket;
    
    public function getName()
    {
        return 'socket_server';
    }

    public function daemonize(LoggerInterface $logger)
    {
        $this->socket = $this->listen($logger);
        while(true)
        {
            $this->childSocket = @socket_accept($this->socket);
            if($this->childSocket)
            {
                $logger->info('Incomming connection detected...');
                $service = new EchoProcess();
                $service->setSocket($this->socket);
                $service->setConnection($this->childSocket);
                $this->addProcess($service);
                $this->run($logger);
            }
            try {
                $this->monitor($logger);
            } catch (NoChildProcessErrorException $exc) {
                $logger->debug('No connection...');
            }
        }
    }

    public function onAfterFork( $isParent, LoggerInterface $logger )
    {
        if($isParent && is_resource($this->childSocket))
        {
            $logger->debug("Closing connection on parent...");
            socket_close($this->childSocket);
        }
    }

    public function onBeforeFork( ProcessInterface $process, LoggerInterface $logger )
    {
        
    }
    
    private function listen( LoggerInterface $logger )
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if(false === $socket)
        {
            $this->displayError('Fail to create socket', $socket, $logger);
        }
        if(false === socket_bind($socket, '127.0.0.1', 2345))
        {
            $this->displayError('Fail to bind socket', $socket, $logger);
        }
        if(false === socket_set_nonblock($socket))
        {
            $this->displayError('Fail to set non blocking socket', $socket, $logger);
        }
        if(false === socket_listen($socket))
        {
            $this->displayError('Fail to set non blocking socket', $socket, $logger);
        }
        
        return $socket;
    }
    
    private function displayError($msg, $socket, LoggerInterface $logger)
    {
        $errNo  = socket_last_error( $socket );
        $errMsg = socket_strerror( $errNo );
        $logger->error("{$msg}. Socket error: [{$errNo}] [{$errMsg}]");
        die;
    }
}

class EchoProcess implements ProcessInterface
{
    private $socket;
    private $connection;
    
    public function setSocket($socket)
    {
        $this->socket = $socket;
    }
    
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    public function getName()
    {
        return 'echo_process';
    }

    public function run( LoggerInterface $logger )
    {
        socket_close($this->socket);
        $logger->debug('Closing socket on child...');
        $logger->info('Start interaction...');
        $connected = true;
        while($connected)
        {
            try 
            {
                $content = $this->read();
            } 
            catch (Exception $exc) 
            {
                $content = $exc->getMessage();
                $connected = false;
            }
            if(!empty($content))
            {
                $logger->info($content);
            }
        }
        socket_close($this->connection);
        $logger->info('Stop interaction');
    }
    
    private function read()
    {
        $content = @socket_read($this->connection, 2048, PHP_NORMAL_READ);
        if(false === $content)
        {
            throw new Exception('Connection ended...');
        }
        
        return str_replace(array("\r", "\n"), '', $content);
    }
}

$handler = new Monolog\Handler\StreamHandler('php://output', Monolog\Logger::INFO);
$logger  = new Monolog\Logger('default', array($handler));
$manager = new SocketServerManager();
try {
    $manager->daemonize($logger);
} catch (\Ncrypthic\Daemon\Exception\ChildProcessException $exc) {
    echo "\t".$exc->getMessage().PHP_EOL;
}
