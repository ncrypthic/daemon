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
namespace Ncrypthic\Daemon\Manager;

use Psr\Log\LoggerInterface;
use Ncrypthic\Daemon\Exception\NoChildProcessErrorException;

/**
 * Default implementation of ManagerInterface
 *
 * created : Oct 8, 2013 4:43:06 PM
 * @author Lim Afriyadi <lim.afriyadi.id@gmail.com>
 */
class DefaultManager extends AbstractProcessManager
{
    public function daemonize(LoggerInterface $logger)
    {
        $done = false;
        while(false == $done)
        {
            try {
                $this->run($logger);
                $this->monitor($logger);
            } catch (NoChildProcessErrorException $exc) {
                $done = true;
                $logger->info('No processes left to run exiting...');
            }
        }
    }

    public function getName()
    {
        return 'process_manager';
    }

    public function onAfterFork( $isParent, LoggerInterface $logger )
    {
        
    }

    public function onBeforeFork( \Ncrypthic\Daemon\Interfaces\ProcessInterface $process, LoggerInterface $logger )
    {
        
    }

}
