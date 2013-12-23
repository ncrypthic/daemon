<?php
require_once "../vendor/autoload.php";

$loader = new Composer\Autoload\ClassLoader();
$loader->add('Ncrypthic\\', '../src/Ncrypthic');
$loader->register();

use Ncrypthic\Daemon\Interfaces\ProcessInterface;
use Psr\Log\LoggerInterface;

class AliceProcess implements ProcessInterface
{
    public function getName()
    {
        return 'alice';
    }

    public function run(LoggerInterface $logger)
    {
        sleep(2);
        $logger->info('Alice done');
    }
}

class BobProcess implements ProcessInterface
{
    public function run(LoggerInterface $logger)
    {
        sleep(3);
        $logger->info('Bob done');
    }

    public function getName()
    {
        return 'bob';
    }
}

$handler = new Monolog\Handler\StreamHandler('php://output', Monolog\Logger::INFO);
$logger  = new Monolog\Logger('default', array($handler));
$manager = new Ncrypthic\Daemon\Manager\DefaultManager();
$manager->addProcess(new AliceProcess());
$manager->addProcess(new BobProcess());
try {
    $manager->daemonize($logger);
} catch (\Ncrypthic\Daemon\Exception\ChildProcessException $exc) {
    echo "\t".$exc->getMessage().PHP_EOL;
}
