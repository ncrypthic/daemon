<?php
namespace Ncrypthic\Daemon\Interfaces;

use Psr\Log\LoggerInterface;

/**
 * Task manager
 * 
 * @author Lim Afriyadi <lim.afriyadi.id@gmail.com>
 */
interface ProcessManagerInterface extends ForkAwareProcessInterface
{
    /**
     * Clean up a process
     */
    public function cleanUp(LoggerInterface $logger, $pid);
    /**
     * Daemoninze process
     */
    public function daemonize(LoggerInterface $logger);
    /**
     * Fork a process
     */
    public function fork(LoggerInterface $logger, ProcessInterface $proc);
    /**
     * Child process monitoring
     */
    public function monitor(LoggerInterface $logger);
    /**
     * Adds a task
     * 
     * @param \Ncrypthic\Daemon\Interfaces\ProcessInterface $process
     * @param int $index
     */
    public function addProcess(ProcessInterface $process, $index=null);
    /**
     * Add tasks
     * 
     * @param array $tasks
     */
    public function addProcesses(array $tasks);
    /**
     * Get a task by its id
     * 
     * @param int $index
     */
    public function getProcess($index=null);
    /**
     * Get all tasks
     */
    public function getProcesses();
    /**
     * Remove task with specified id from collection
     * 
     * @param int $index
     */
    public function remove($index);
    /**
     * Remove task from collection
     * 
     * @param Ncrypthic\Daemon\Interfaces\ProcessInterface $process
     */
    public function removeProcess(ProcessInterface $process);
}
