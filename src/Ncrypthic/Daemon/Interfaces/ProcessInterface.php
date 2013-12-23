<?php
namespace Ncrypthic\Daemon\Interfaces;

use Psr\Log\LoggerInterface;

/**
 * Task Interface
 * 
 * @author Lim Afriyadi <lim.afriyadi.id@gmail.com>
 */
interface ProcessInterface
{
    /**
     * Run actions task
     */
    public function run(LoggerInterface $logger);
    /**
     * Get task name
     */
    public function getName();
}
