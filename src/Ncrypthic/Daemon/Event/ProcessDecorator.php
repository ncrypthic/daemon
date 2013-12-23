<?php
/*
 *  Copyright (C) 2013  Lim Afriyadi <lim.afriyadi.id@gmail.com>
 * 
 *  Permission is hereby granted, free of charge, to any person obtaining a copy of
 *  this software and associated documentation files (the "Software"), to deal in 
 *  the Software without restriction, including without limitation the rights to 
 *  use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 *  the Software, and to permit persons to whom the Software is furnished to do so,
 *  subject to the following conditions:
 * 
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 * 
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 *  FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR 
 *  COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER 
 *  IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN 
 *  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace Ncrypthic\Daemon\Event;

use Ncrypthic\Daemon\Interfaces\ProcessInterface;
use Ncrypthic\Daemon\Interfaces\PreparedProcessInterface;
use Ncrypthic\Daemon\Interfaces\CleanedProcessInterface;
use Ncrypthic\Daemon\Interfaces\InitializedProcessInterface;
use Ncrypthic\Daemon\Interfaces\ForkAwareProcessInterface;
use Psr\Log\LoggerInterface;

/**
 * EventDecorator
 *
 * @author Lim Afriyadi <lim.afriyadi.id@gmail.com>
 */
class ProcessDecorator implements ProcessInterface
{
    /**
     * @var ProcessInterface
     */
    private $process;
    
    public function __construct(ProcessInterface $process)
    {
        $this->process = $process;
    }
    
    public function onBeforeFork($logger)
    {
        if($this->process instanceof ForkAwareProcessInterface)
        {
            $this->process->onBeforeFork($this, $logger);
        }
    }

    public function onAfterFork($isParent, LoggerInterface $logger)
    {
        if($this->process instanceof ForkAwareProcessInterface)
        {
            $this->process->onAfterFork($isParent, $logger);
        }
        
        return $this;
    }

    public function onBeforeStart(LoggerInterface $logger)
    {
        if( 
            $this->process instanceof PreparedProcessInterface ||
            $this->process instanceof InitializedProcessInterface 
        ){
            $this->process->onBeforeStart($logger);
        }
        
        return $this;
    }

    public function onExit(LoggerInterface $logger)
    {
        if( 
            $this->process instanceof PreparedProcessInterface || 
            $this->process instanceof CleanedProcessInterface
        ){
            $this->process->onExit($logger);
        }
        
        return $this;
    }

    public function run(LoggerInterface $logger)
    {
        $logger->debug("Running [{$this->process->getName()}]...");
        $this->process->run($logger);
        
        return $this;
    }

    public function getName()
    {
        return $this->process->getName();
    }
    
    public function terminate(LoggerInterface $logger, ProcessInterface $process)
    {
        exit(0);
    }
}
