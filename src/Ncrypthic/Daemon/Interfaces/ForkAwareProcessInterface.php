<?php
namespace Ncrypthic\Daemon\Interfaces;

use Psr\Log\LoggerInterface;

/**
 *
 * @author Lim Afriyadi <adi@whatiwear.com>
 */
interface ForkAwareProcessInterface extends ProcessInterface
{
    /**
     * After forking process callback
     */
    public function onAfterFork($isParent, LoggerInterface $logger);
    /**
     * Before forking process callback 
     */
    public function onBeforeFork( ProcessInterface $process, LoggerInterface $logger);
}
