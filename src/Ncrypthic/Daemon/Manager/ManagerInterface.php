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

use Ncrypthic\Daemon\Process\ProcessInterface;

/**
 * Process manager interface
 * 
 * @author Lim Afriyadi <lim.afriyadi.id@gmail.com>
 */
interface ManagerInterface extends \Iterator
{
    /**
     * Daemoninze process
     */
    public function daemonize();
    /**
     * Fork a process
     */
    public function fork(ProcessInterface $proc);
    /**
     * Child process monitoring
     */
    public function monitor();
    /**
     * Find managed process id
     * 
     * @param \Ncrypthic\Daemon\Process\ProcessInterface $proc
     */
    public function findProcess(ProcessInterface $proc);
    /**
     * Add managed process
     * 
     * @param \Ncrypthic\Daemon\Process\ProcessInterface $proc
     */
    public function addProcess(ProcessInterface $proc);
    /**
     * Remove a managed process
     * 
     * @param \Ncrypthic\Daemon\Process\ProcessInterface $proc
     */
    public function removeProcess(ProcessInterface $proc);
    /**
     * Get process by its id
     * 
     * @param int $index
     */
    public function getProcess($index);
}
