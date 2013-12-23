<?php
namespace Ncrypthic\Daemon\Manager;

use Ncrypthic\Daemon\Interfaces\ProcessManagerInterface;
use Ncrypthic\Daemon\Interfaces\ProcessInterface;
use Ncrypthic\Daemon\Event\ProcessDecorator;
use Ncrypthic\Daemon\Exception\ForkErrorException;
use Ncrypthic\Daemon\Exception\NoChildProcessErrorException;
use Ncrypthic\Daemon\Interfaces\PersistentProcessInterface;
use Psr\Log\LoggerInterface;

/**
 * Abstract implementation of TaskManagerInterface
 *
 * @author Lim Afriyadi <>
 * @created Aug 16, 2013 4:52:46 PM
 */
abstract class AbstractProcessManager implements ProcessManagerInterface
{
    /**
     * @var bool
     */
    private $isBreak;
    /**
     * @var array
     */
    private $childs;
    /**
     * @var array
     */
    private $processes;
    /**
     * @var self
     */
    protected static $instance;

    public function __construct()
    {
        $this->childs = array();
        $this->processes = array();
        $this->isBreak = false;
    }
    /**
     * Add collection of process
     * 
     * @param array $processes
     */
    public function addProcesses(array $processes)
    {
        foreach($processes as $process)
        {
            $this->addProcess($process);
        }
    }
    /**
     * Add a task to collection
     * 
     * @param ProcessInterface $process
     * @param int $index
     */
    public function addProcess( ProcessInterface $process, $index = null )
    {
        if(is_int($index))
        {
            $this->processes[$index] = $process;
        }
        else
        {
            $this->processes[] = $process;
        }
        
        return array_search($process, $this->processes);
    }
    /**
     * Get a process with specified index
     * 
     * @param int $index
     * @return ProcessInterface
    */
    public function getProcess($index=null)
    {
        $exists = isset($this->processes[$index]);
        return ($exists === false) 
                   ? $this->processes[0] 
                   : $this->processes[$index];
    }
    /**
     * Get process collection
     * 
     * @return array
     */
    public function getProcesses()
    {
        return $this->processes;
    }
    /**
     * Remove process at index
     * 
     * @param int $index
     */
    public function remove( $index )
    {
        if(isset($this->processes[$index]))
        {
            unset($this->processes[$index]);
        }
    }
    /**
     * Remove a process from the list
     * 
     * @param ProcessInterface $process
     */
    public function removeProcess( ProcessInterface $process )
    {
        $index = array_search($process, $this->processes, true);
        if(false !== $index)
        {
            unset($this->processes[$index]);
        }
    }
    /**
     * @return AbstractTaskManager
     */
    public static function createInstance()
    {
        if(!static::$instance)
        {
            static::$instance = new static();
        }
        
        return static::$instance;
    }
    /**
     * Runs the process manager
     * 
     * @param LoggerInterface $logger
     */
    public function run(LoggerInterface $logger)
    {
        foreach($this->processes as $process)
        {
            if(!array_search($process, $this->childs))
            {
                $this->fork($logger, $process);
            }
        }
    }
    /**
     * Fork a process
     * 
     * @param LoggerInterface $logger
     * @param ProcessInterface $process
     */
    public function fork(LoggerInterface $logger, ProcessInterface $process)
    {
        // Fork process
        $decorator = new ProcessDecorator($process);
        $this->onBeforeFork($process, $logger);
        $decorator->onBeforeFork($logger);
        $pid       = pcntl_fork();
        switch($pid)
        {
            case -1:
                throw new ForkErrorException('Could not fork process');
            case 0:
                // Run task in child process
                $this->onAfterFork(false, $logger);
                $decorator->onAfterFork(false, $logger)
                          ->onBeforeStart($logger)
                          ->run($logger)
                          ->onExit($logger)
                          ->terminate($logger, $process);
                break;
            default:
                // Register spawned child pid in parent process
                $logger->info("Forked process [$pid]...");
                $this->onAfterFork(true, $logger);
                $this->childs[$pid] = $process;
        }
    }
    /**
     * Monitor child process for parent process
     * 
     * @param LoggerInterface $logger
     */
    public function monitor(LoggerInterface $logger)
    {
        $status = '';
        $pid = pcntl_waitpid(-1, $status, WNOHANG|WUNTRACED);
        switch($pid)
        {
            case -1:
                $logger->debug('No childs exists');
                throw new NoChildProcessErrorException();
            case 0:
                // No stopped child
                break;
            default:
                $this->cleanUp($logger, $pid);
        }
    }
    
    public function cleanUp(LoggerInterface $logger, $pid)
    {
        if(isset($this->childs[$pid]))
        {
            $process = $this->childs[$pid];
            // Remove done process from our child list
            unset($this->childs[$pid]);
            // Re-spawn persistent child
            if($process instanceof PersistentProcessInterface)
            {
                $this->fork($logger, $process);
            }
            else
            {
                $logger->info("Removing process [$pid]");
                $this->removeProcess($process);
            }
        }
    }
}
